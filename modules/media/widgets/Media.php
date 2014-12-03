<?php

namespace kato\modules\media\widgets;

use kato\modules\media\MediaAsset;
use Yii;
use yii\base\Widget;

class Media extends Widget
{
    public $model;

    public function init()
    {
        parent::init();

        $this->registerAsset();
    }

    public function run()
    {
        return $this->render('media', [
            'model' => $this->model,
        ]);
    }

    protected function registerAsset()
    {
        $view = $this->getView();
        MediaAsset::register($view);
    }
}