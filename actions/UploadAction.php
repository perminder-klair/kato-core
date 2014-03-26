<?php

namespace kato\actions;

use Yii;
use yii\helpers\Json;
use backend\models\ContentMedia;

class UploadAction extends \yii\base\Action
{

    /**
     * Uploads the file and update database
     */
    public function run()
    {
        //If any media upload catch it and upload it
        $mediaJson = Yii::$app->kato->mediaUpload();

        $media = Json::decode($mediaJson);

        if (isset($_GET['content_id']) && isset($_GET['content_type'])) {
            if (is_array($media)) {
                //Do join here
                $contentMedia = new ContentMedia();
                $contentMedia->content_id = $_GET['content_id'];
                $contentMedia->content_type = $_GET['content_type'];
                $contentMedia->media_id = $media['id'];
                $contentMedia->save(false);
            }
        }

        echo $mediaJson;
    }
}
