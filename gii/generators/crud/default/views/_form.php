<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/**
 * @var yii\web\View $this
 * @var yii\gii\generators\crud\Generator $generator
 */

/** @var \yii\db\ActiveRecord $model */
$model = new $generator->modelClass;
$safeAttributes = $model->safeAttributes();
if (empty($safeAttributes)) {
    $safeAttributes = $model->attributes();
}

echo "<?php\n";
?>

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use backend\models\Tag;
use kartik\widgets\Select2;
use kartik\widgets\DatePicker;
use yii\imperavi\Widget as ImperaviWidget;
use kartik\widgets\SwitchInput;
use kato\modules\media\widgets\Media;

$tag = new Tag;

/**
* @var kato\web\View $this
* @var <?= ltrim($generator->modelClass, '\\') ?> $model
* @var yii\bootstrap\ActiveForm $form
*/
?>

<div class="block-title">
    <ul class="nav nav-tabs" data-toggle="tabs">
        <li class="active"><a href="#form">Form</a></li>
        <li class=""><a href="#media">Media</a></li>
    </ul>
</div>

<div class="tab-content">

    <div class="tab-pane active <?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-form" id="form">

        <?= "<?php " ?>$form = ActiveForm::begin(); ?>

<?php foreach ($safeAttributes as $attribute) {
echo "              <?= " . $generator->generateActiveField($attribute) . " ?>\n\n";
} ?>
            <div class="form-group">
                <?= "<?= " ?>Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            </div>

        <?= "<?php " ?>ActiveForm::end(); ?>

    </div>

    <div class="tab-pane" id="media">

        <?= "<?= " ?>Media::widget([
        'model' => $model,
        ]); ?>

    </div>

</div>
