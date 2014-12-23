<?php

namespace kato\modules\media;

use Yii;
use yii\web\BadRequestHttpException;
use yii\web\UploadedFile;
use kato\helpers\KatoBase;
use kato\modules\media\models\Media as MediaModel;

/**
 * Class Media
 * @package kato\modules\media
 *
 * @property string $adminView
 * @property string $adminLayout
 */
class Media extends \yii\base\Module
{
    public $controllerNamespace = 'kato\modules\media\controllers';

    public $adminView = 'index';
    public $adminLayout = null;

    public function init()
    {
        parent::init();

        Yii::setAlias('@media', dirname(__FILE__));
    }


    /**
     * Uploads the file
     * if success returns json array for media data
     *
     * @param string $fileName
     * @param bool $useFile
     * @return bool|string
     * @throws \yii\web\BadRequestHttpException
     */
    public function mediaUpload($fileName = 'file', $useFile = false)
    {
        if ($useFile === false) {
            $file = UploadedFile::getInstanceByName($fileName);
        } else {
            $files = UploadedFile::getInstancesByName($fileName);
            $file = $files[0];
        }

        if (is_null($file)) {
            throw new BadRequestHttpException('No file specified to upload.');
        }

        return $this->insertMedia($file);
    }

    /**
     * Upload to file and insert into media table
     * @param $file
     * @return array
     */
    private function insertMedia($file)
    {
        /**
         * @var \yii\web\UploadedFile $file
         */

        $result = ['success' => false, 'message' => 'File could not be saved.'];

        if ($file->size > Yii::$app->params['maxUploadSize']) {
            $result['message'] = 'Max upload size limit reached';
        }

        $uploadTime = date("Y-m-W");
        $media = new MediaModel();

        $media->filename = KatoBase::sanitizeFile($file->baseName) . '-' . KatoBase::genRandomString(4) . '.' . $file->extension;
        $media->mimeType = $file->type;
        $media->byteSize = $file->size;
        $media->extension = $file->extension;
        $media->source = basename(\Yii::$app->params['uploadPath']) . '/' . $uploadTime . '/' . $media->filename;


        if (!is_file($media->source)) {
            //If saved upload the file
            $uploadPath = \Yii::$app->params['uploadPath'] . $uploadTime;
            if (!is_dir($uploadPath)) mkdir($uploadPath, 0777, true);

            if ($file->saveAs($uploadPath . '/' . $media->filename)) {
                //Save to media table
                if ($media->save(false)) {
                    $result['success'] = true;
                    $result['message'] = 'Upload Success';
                    $result['data'] = $media;
                } else {
                    $result['message'] = "Database record could not be saved.";
                }
            } else {
                $result['message'] = "File could not be saved.";
            }
        } else {
            $result['message'] = "File already exists.";
        }

        return $result;
    }
}
