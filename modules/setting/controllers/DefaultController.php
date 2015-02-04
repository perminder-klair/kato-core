<?php

namespace kato\modules\setting\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use kato\modules\setting\models\Setting;
use yii\base\Model;
use kato\modules\setting\Setting as SettingModule;

class DefaultController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['settings'],
                'rules' => [
                    [
                        'actions' => ['settings'],
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $module = SettingModule::getInstance();
        if (!is_null($module->adminLayout)) {
            $this->layout = $module->adminLayout;
        }

        $model = new Setting();
        $settings = $model->getByCategories();

        $postData = Yii::$app->request->post();
        if (isset($postData['Setting'])) {
            foreach ($postData['Setting'] as $id => $setting) {
                $item = Setting::findOne($id);
                if ($item->load(['Setting' => $setting])) {
                    $item->save();
                }
            }

            return $this->redirect('index');
        }

        return $this->render('index', ['model' => $settings]);
    }
}
