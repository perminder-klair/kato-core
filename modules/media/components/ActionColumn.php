<?php

namespace kato\modules\media\components;

use Yii;
use yii\helpers\Html;

class ActionColumn extends \yii\grid\ActionColumn
{
    public $template = '{update} {delete}';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->buttons['update'] = function ($url, $model) {
            return Html::a('<i class="fa fa-pencil"></i> Edit', $url, [
                'title' => Yii::t('yii', 'Update'),
                'data-pjax' => '0',
                'class' => 'btn btn-primary btn-xs',
            ]);
        };
        $this->buttons['delete'] = function ($url, $model) {
            return Html::a('<i class="fa fa-trash-o"></i> Delete', $url, [
                'title' => Yii::t('yii', 'Delete'),
                'data-confirm' => Yii::t('yii', 'Are you sure to delete this item?'),
                'data-method' => 'post',
                'data-pjax' => '0',
                'class' => 'btn btn-primary btn-xs',
            ]);
        };
    }
}