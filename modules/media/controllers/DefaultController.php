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
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use kato\modules\media\Media as MediaModule;
use kato\modules\media\models\MediaSearch;
use yii\grid\DataColumn;

class DefaultController extends Controller
{
    public $pageTitle = 'Media';
    public $pageIcon = 'fa fa-camera-retro fa-fw';

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['delete'],
                'rules' => [
                    [
                        'actions' => ['index', 'update', 'delete'],
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $meta['title'] = $this->pageTitle;
        $meta['description'] = 'List all media';
        $meta['pageIcon'] = $this->pageIcon;

        $module = MediaModule::getInstance();
        if (!is_null($module->adminLayout)) {
            $this->layout = $module->adminLayout;
        }

        $searchModel = new MediaSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        $getColumns = [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'class' => DataColumn::className(),
                'attribute' => 'title',
                'format' => 'text',
                'label' => 'Title',
            ],
            'create_time',
            [
                'class' => DataColumn::className(),
                'attribute' => 'statusLabel',
                'format' => 'text',
                'label' => 'Status',
            ],
            [
                'class' => DataColumn::className(),
                'attribute' => 'uploadedTo',
                'format' => 'html',
                'label' => 'Uploaded to',
            ],
            ['class' => 'backend\components\ActionColumn'],
        ];

        return $this->render($module->adminView, [
            'meta' => $meta,
            'dataProvider' => $dataProvider,
            'getColumns' => $getColumns,
        ]);
    }

    /**
     * Updates an existing Media model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->title = $model->id;
        $controllerName = $this->getUniqueId();

        $meta['title'] = $this->pageTitle;
        $meta['description'] = 'Update media';
        $meta['pageIcon'] = $this->pageIcon;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Media has been updated');
            return $this->redirect(Url::previous());
        } else {
            return $this->render('/global/update', [
                'model' => $model,
                'meta' => $meta,
                'controllerName' => $controllerName,
            ]);
        }
    }

    /**
     * If content id and type provided then do join
     * return boolean
     * @param $result
     * @return bool
     */
    private function doMediaJoin($result)
    {
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
                    return true;
                } else {
                    return false;
                }
            }
        }

        return true;
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

            if ($this->doMediaJoin($result)) {
                //success
                $response->setStatusCode(200);
                $response->data = $result['data'];
                $response->send();
                Yii::$app->end();
            } else {
                $response->statusText = 'Unable to do join of media with content.';
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
    public function actionSirTrevorUpload()
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

            if ($this->doMediaJoin($result)) {
                //success
                $response->setStatusCode(200);
                $response->data = [
                    'file' => [
                        'url' => '/' . $result['data']['source'],
                        'media_id' => $result['data']['id'],
                    ],
                ];
                $response->send();
            } else {
                $response->statusText = 'Unable to do join of media with content.';
                $response->send();
                Yii::$app->end();
            }

        } else {
            $response->statusText = $result['message'];
            $response->setStatusCode(500);
            $response->send();
            Yii::$app->end();
        }
    }

    /**
     * Update data information of media
     * @param $id
     * @throws NotFoundHttpException
     */
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

    /**
     * Returns list of media in json format
     */
    public function actionListMedia()
    {
        $result = array();

        if (isset($_GET['content_id']) && isset($_GET['content_type'])) {
            //get for modal
            $media = Media::find()
                ->leftJoin('kato_content_media', 'kato_content_media.media_id = kato_media.id')
                ->where(['kato_content_media.content_id' => $_GET['content_id'], 'kato_content_media.content_type' => $_GET['content_type']])
                ->all();
        } else {
            //return all media
            $media = Media::find()->all();
        }

        if ($media) {
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

    /**
     * Get data about requested media and return as row
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
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
