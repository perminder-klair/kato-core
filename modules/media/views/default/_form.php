<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/**
 * @var yii\web\View $this
 * @var backend\models\Media $model
 * @var yii\bootstrap\ActiveForm $form
 */
?>

<div class="block-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'status')->dropDownList($model->listStatus()); ?>

    <?= $form->field($model, 'media_type')->dropDownList($model->listMediaType(), ['prompt'=>'Select media type']); ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success col-sm-offset-2' : 'btn btn-primary col-sm-offset-2']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
