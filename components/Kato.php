<?php

namespace kato\components;

use Yii;
use backend\models\Setting;
use backend\models\Block;
use yii\helpers\HtmlPurifier;
use yii\web\BadRequestHttpException;
use yii\helpers\Json;

/**
 * Usage
 * -----
 *
 * In your configuration file, add the setting component.
 *
 * ```php
 * 'components' => [
 *	...
 *	'kato' => 'kato\components\Kato',
 *	...
 * ]
 * ```
 */

class Kato extends \yii\base\Component 
{

    /**
     * Get settings for Kato
     * Usage:
     *
     * ```php
     * $setting = Yii::$app->kato->setting('site_name');
     * ```
     * @param $key
     * @return bool
     */
    public static function setting($key)
    {
        $model = Setting::find()
            ->where(['define' => $key])
            ->one();

        if (!is_null($model)) {
            return $model->value;
        }

        return false;
    }

    /**
     * returns page slug if set
     * @return string
     */
    public function pageSlug()
    {
        if (isset($_GET['slug'])) {
            return HtmlPurifier::process($_GET['slug']);
        }
        return false;
    }

    /**
     * Returns HTML content of block
     * Usage: \Yii::$app->kato->block('block_slug');
     * @param null $slug
     * @return bool
     * @throws \yii\web\BadRequestHttpException
     */
    public function block($slug = null)
    {
        if (is_null($slug)) {
            throw new BadRequestHttpException('Block slug not specified.');
        }

        $model = Block::find()
            ->where(['title' => $slug])
            ->one();

        if (is_null($model)) {
            return false;
        }

        //TODO check parent
        if (!is_null($model->parent)) {
            //var_dump($model->parent);exit;
        }

        return $model->render();
    }

    /**
     * Uploads the file
     * if success returns json array for media data
     * Usage: echo \Yii::$app->kato->mediaUpload();
     *
     * @param string $fileName
     * @return bool|string
     * @throws \yii\web\BadRequestHttpException
     */
    public function mediaUpload($fileName = 'file')
    {
        if (isset($_FILES[$fileName])) {
            $media = new \backend\models\Media();
            $uploadTime = date("Y-m-W");
            $file = \yii\web\UploadedFile::getInstanceByName($fileName);

            if ($file->size > Yii::$app->params['maxUploadSize']) {
                throw new BadRequestHttpException('Max upload size limit reached');
            }

            //$media->file = $file;
            $media->filename = \kato\helpers\KatoBase::sanitizeFile($file->baseName). '-' . \kato\helpers\KatoBase::genRandomString(4) . '.' . $file->extension;
            $media->mimeType = $file->type;
            $media->byteSize = $file->size;
            $media->extension = $file->extension;
            $media->source = basename(\Yii::$app->params['uploadPath']) . '/' . $uploadTime . '/' . $media->filename;

            //Save to media table
            if ($media->save(false)) {
                //If saved upload the file
                $uploadPath = \Yii::$app->params['uploadPath'] .  $uploadTime;
                if (!is_dir($uploadPath)) mkdir($uploadPath, 0777, true);

                if ($file->saveAs($uploadPath . '/' . $media->filename)) {
                    return Json::encode($media);
                }
            }
        }

        return false;
    }
}