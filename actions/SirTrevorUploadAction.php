<?php

namespace kato\actions;

use Yii;
use yii\web\Response;

class SirTrevorUploadAction extends \yii\base\Action
{

    /**
     * Action for file uploads via sir-trevor image block from SirTrevorWidget (input widget)
     */
    public function run()
    {
        $result = Yii::$app->kato->mediaUpload('attachment[file]');

        $response = new Response();
        $response->format = Response::FORMAT_JSON;

        if ($result['success'] === true) {

            $response->setStatusCode(200);
            $response->data = [
                'file' => [
                    'url' => '/' . $result['data']['source'],
                    'media_id' => $result['data']['id'],
                ],
            ];
            $response->send();
        } else {
            $response->statusText = $result['message'];
            $response->setStatusCode(500);
            $response->send();
            Yii::$app->end();
        }
    }
}
