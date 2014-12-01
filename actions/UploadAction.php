<?php

namespace kato\actions;

use Yii;
use yii\web\Response;
use backend\models\ContentMedia;

class UploadAction extends \yii\base\Action
{

    /**
     * Uploads the file and update database
     */
    public function run()
    {
        $result = Yii::$app->kato->mediaUpload();

        $response = new Response();
        $response->format = Response::FORMAT_JSON;
        $response->setStatusCode(500);

        if ($result['success'] === true) {

            if (isset($_GET['content_id']) && isset($_GET['content_type'])) {
                $media = $result['data'];
                if (!is_null($media)) {
                    //Do join here
                    $contentMedia = new ContentMedia();
                    $contentMedia->content_id = $_GET['content_id'];
                    $contentMedia->content_type = $_GET['content_type'];
                    $contentMedia->media_id = $media['id'];
                    if ($contentMedia->save(false)) {
                        //success
                        $response->setStatusCode(200);
                        $response->data = $media;
                        $response->send();
                        Yii::$app->end();
                    } else {
                        $response->statusText = 'Unable to do join of media with content.';
                        $response->send();
                        Yii::$app->end();
                    }
                }
            } else {
                $response->statusText = 'Invalid Media data return.';
                $response->send();
                Yii::$app->end();
            }

        } else {
            $response->statusText = $result['message'];
            $response->send();
            Yii::$app->end();
        }
    }
}
