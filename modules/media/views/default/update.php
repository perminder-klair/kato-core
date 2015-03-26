<?php

use yii\helpers\Html;
use yii\widgets\Breadcrumbs;

/**
 * @var backend\components\View $this
 * @var backend\models\Blog $model
 * @var $meta
 * @var $controllerName
 */

$this->description = $meta['description'];
$this->pageIcon = $meta['pageIcon'];
$this->title = 'Update ' . $meta['title'] . ': ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => $meta['title'], 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->title;
$this->params['breadcrumbs'][] = 'Update';
?>

<div class="row">
    <div class="col-lg-12">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>
    <!-- /.col-lg-12 -->
</div>
<!-- /.row -->

<?=
Breadcrumbs::widget([
    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
    'options' => ['class' => 'breadcrumb breadcrumb-top'],
    'encodeLabels' => false,
    'homeLink' => ['label' => '<i class="' . Html::encode($this->pageIcon) . '"></i>'],
]) ?>
<!-- END Blank Header -->

<?= \backend\widgets\Alert::widget(); ?>

<?php echo $this->render('_form', [
    'model' => $model,
]); ?>