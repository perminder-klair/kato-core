<?php

namespace kato\modules\media\controllers;

use Yii;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use kato\modules\media\models\ContentMedia;
use yii\web\Controller;
use kato\modules\media\models\Media;

class DefaultController extends Controller
{
    public function actionIndex()
    {

        //$module = Media::getInstance();
        //dump($module);exit;

        return $this->render('index');
    }

    /**
     * Uploads the file and update database
     */
    public function actionUpload()
    {
        /**
         * @var \kato\modules\media\Media $module
         */
        $module = \Yii::$app->controller->module;
        $result = $module->mediaUpload();

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

    /**
     * Action for file uploads via sir-trevor image block from SirTrevorWidget (input widget)
     */
    public function SirTrevorUploadAction()
    {
        //header('Access-Control-Allow-Origin: *');
        /**
         * @var \kato\modules\media\Media $module
         */
        $module = \Yii::$app->controller->module;
        $result = $module->mediaUpload('attachment[file]');

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

    public function actionUpdateData($id)
    {
        if ($post = Yii::$app->request->post()) {
            $model = $this->findModel($id);
            $model->$post['name'] = $post['value'];
            if ($model->save(false)) {
                echo 'true';
                exit;
            }
        }
        echo 'false';
    }

    public function actionListMedia()
    {
        $result = array();
        if ($media = Media::find()->all()) {
            foreach ($media as $data) {
                $result[] = array(
                    'thumb' => '/' . $data->source,
                    'image' => '/' . $data->source,
                    'title' => '/' . $data->filename,
                    //'filelink' => '/' . $data->source,
                );
            }
        }

        echo Json::encode($result);
    }

    public function actionRenderRow($id)
    {
        $this->layout = false;

        if ($media = $this->findModel($id)) {
            return $this->render('mediaRow', [
                'media' => $media,
                'isNew' => true,
            ]);
        }
        return '';
    }

    /**
     * Deletes an existing Media model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        if (Yii::$app->request->isAjax) {
            echo 'true';
            exit;
        }

        Yii::$app->session->setFlash('success', 'Media has been deleted');

        return $this->redirect(Url::previous());
    }

    /**
     * Finds the Media model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Media the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Media::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
