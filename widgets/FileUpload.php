<?php

namespace kato\widgets;

use yii\base\Widget;
use kartik\widgets\FileInput;

/**
 * Class FileUpload
 * @package backend\widgets
 *
 * Usage:
 * In view: echo \backend\widgets\FileUpload::widget(['form' => $form]);
 * In
 */
class FileUpload extends Widget
{
    public $form;
    public $model = null;
    public $fileColumn = 'file';
    public $options = ['accept' => 'image/*'];

    /**
     * Initialize the widget
     */
    public function init()
    {
        parent::init();

        if ($this->model === null) {
            $this->model = new \backend\models\Media();
        }
    }

    /**
     * Run the widget
     */
    public function run()
    {
        return $this->form->field($this->model, $this->fileColumn)->widget(FileInput::classname(), [
            'options' => $this->options,
            'showUpload' => false,
        ]);
    }
}
