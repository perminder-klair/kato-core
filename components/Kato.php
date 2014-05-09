<?php

namespace kato\components;

use Yii;
use backend\models\Setting;
use backend\models\Block;
use yii\helpers\HtmlPurifier;
use yii\web\BadRequestHttpException;
use yii\helpers\Json;
use yii\web\UploadedFile;
use kato\helpers\KatoBase;
use yii\helpers\Html;
use kartik\markdown\Markdown;

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

        return $model->render();
    }



    /**
     * Renders blocks and out as html
     * Usage: \Yii::$app->kato->renderBlock($this->content);
     * @param null $content
     * @return string
     */
    public function renderBlock($content = null)
    {
        if (!is_array($content)) {
            if (empty($content) || $content == '') {
                return false;
            }
            $content = Json::decode($content);
        }

        $blocks = '';
        if (!empty($content)) {
            foreach ($content['data'] as $block) {
                if ($block['type'] === 'heading') {
                    $blocks .= Html::tag('h2', $block['data']['text']);
                }
                if ($block['type'] === 'text' || $block['type'] === 'list') {
                    $blocks .= Markdown::convert($block['data']['text']);
                }
            }
        }

        return $blocks;
    }

    /**
     * Uploads the file
     * if success returns json array for media data
     * Usage: echo \Yii::$app->kato->mediaUpload();
     * 
     * @param string $fileName
     * @param bool $useFile
     * @return bool|string
     * @throws \yii\web\BadRequestHttpException
     */
    public function mediaUpload($fileName = 'file', $useFile = false)
    {
        if (isset($_FILES[$fileName])) {
            $media = new \backend\models\Media();
            $uploadTime = date("Y-m-W");
            if ($useFile === false) {
                $file = UploadedFile::getInstanceByName($fileName);
            } else {
                $files = UploadedFile::getInstancesByName($fileName);
                $file = $files[0];
            }

            if ($file->size > Yii::$app->params['maxUploadSize']) {
                throw new BadRequestHttpException('Max upload size limit reached');
            }

            //$media->file = $file;
            $media->filename = KatoBase::sanitizeFile($file->baseName). '-' . KatoBase::genRandomString(4) . '.' . $file->extension;
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