<?php
/**
 * @var yii\web\View $this
 * @var kato\modules\setting\models\Setting $model
 */
$this->title = 'Settings';
$this->params['breadcrumbs'][] = $this->title;

use yii\widgets\Breadcrumbs;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kato\modules\setting\models\Setting;
use yii\imperavi\Widget as ImperaviWidget;
?>

<div class="row">
    <div class="col-lg-12">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>
    <!-- /.col-lg-12 -->
</div>
<!-- /.row -->

<?= Breadcrumbs::widget([
    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
    'options' => ['class' => 'breadcrumb breadcrumb-top'],
    'encodeLabels' => false,
    'homeLink' => ['label' => '<i class="fa fa-cogs"></i>'],
]) ?>
<!-- END Blank Header -->

<?= \backend\widgets\Alert::widget(); ?>

<?php $form = ActiveForm::begin([
    'layout' => 'horizontal',
    'fieldConfig' => [
        'horizontalCssClasses' => [
            'label' => 'col-sm-4',
            'offset' => 'col-sm-offset-4',
            'wrapper' => 'col-sm-8',
            'error' => '',
            'hint' => '',
        ],
    ],
]); ?>
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs">
                        <?php $catCount = 0; foreach ($model['categories'] as $category): $catCount++; ?>
                            <li class="<?php echo $catCount==1?'active':''; ?>"><a href="#<?php echo $category; ?>" data-toggle="tab"><?php echo ucwords(str_replace("-", " ", $category)); ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="panel-body">
                    <!-- Tab panes -->

                    <div class="tab-content">

                        <?php $catCount = 0; foreach ($model['settings'] as $category => $settings): $catCount++; ?>

                            <div class="tab-pane fade in <?php echo $catCount==1?'active':''; ?>" id="<?php echo $category; ?>">

                                <?php foreach ($settings as $index => $setting) {
                                    if ($setting->type == Setting::TYPE_TEXT_AREA) { ?>
                                        <div class="form-group field-setting-<?= $index; ?>-value">
                                            <label class="control-label col-sm-4" for="setting-<?= $index; ?>-value"><?= $setting->defineEncoded() ?></label>
                                            <div class="col-sm-8">
                                                <?= ImperaviWidget::widget([
                                                    'attribute' => "Setting[$index][value]",
                                                    'value' => $setting->value,
                                                    // Some options, see http://imperavi.com/redactor/docs/
                                                    'options' => [
                                                        'toolbar' => 'classic',
                                                        'buttonSource' => true,
                                                        'minHeight' => 300,
                                                        'autoresize' => true,
                                                        'imageUpload' => Yii::$app->urlManagerFrontend->createUrl(['media/default/redactor-upload', 'content_id' => $setting->id, 'content_type' => $setting->className()]),
                                                        'focus' => true,
                                                        'imageManagerJson' => Yii::$app->urlManagerBackend->createUrl(['media/default/list-media', 'content_id' => $setting->id, 'content_type' => $setting->className()]),
                                                    ],
                                                    'plugins' => [
                                                        'fullscreen',
                                                        'table',
                                                        'counter',
                                                        'definedlinks',
                                                        'fontsize',
                                                        'textexpander',
                                                        'video',
                                                        'imagemanager',
                                                    ],
                                                ]); ?>
                                            </div>
                                        </div>

                                    <?php } else {
                                        echo $form->field($setting, "[$index]value")->label($setting->defineEncoded());
                                    }
                                } ?>
                            </div>

                        <?php endforeach; ?>

                        <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']); ?>
                    </div>

                </div>
                <!-- /.panel-body -->
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
<?php ActiveForm::end(); ?>