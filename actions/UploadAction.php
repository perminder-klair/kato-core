<?php

namespace kato\actions;

class UploadAction extends \yii\base\Action
{

    /**
     * TODO: make it work with: Yii::$app->request->getQueryParams() instead of $_GET
     */
    public function run()
    {
        //If any media upload catch it and upload it
        $mediaJson = \Yii::$app->kato->mediaUpload();

        $media = \yii\helpers\Json::decode($mediaJson);

        if (isset($_GET['content_id']) && isset($_GET['media_type'])) {
            if (is_array($media)) {
                //Do join here
                $contentMedia = new \backend\models\ContentMedia();
                $contentMedia->content_id = $_GET['content_id'];
                $contentMedia->media_type = $_GET['media_type'];
                $contentMedia->media_id = $media['id'];
                $contentMedia->save(false);
            }
        }

        echo $mediaJson;
    }
}
