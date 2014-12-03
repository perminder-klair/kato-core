<?php

use kato\helpers\KatoBase;
use kato\DropZone;

?>

<div class="block full">
    <!-- Dropzone Title -->
    <div class="block-title">
        <h2><i class="fa fa-cloud-upload"></i> File Upload <small>Drag and Drop files to upload them!</small></h2>
    </div>
    <!-- END Dropzone Title -->

    <!-- Dropzone Content -->
    <!-- Dropzone.js, You can check out https://github.com/enyo/dropzone/wiki for usage examples -->
    <?= DropZone::widget([
        'options' => [
            'url' => \Yii::$app->urlManagerFrontend->createUrl(['media/default/upload', 'content_id' => $model->id, 'content_type' => $model->className()]),
            'addRemoveLinks' => true,
            'maxFilesize' => KatoBase::formatBytes(Yii::$app->params['maxUploadSize'], 'MB', '0', true),
            'acceptedFiles' => implode(",", Yii::$app->params['acceptedUploadTypes']),
        ],
        'clientEvents' => [
            'success' => "function(file, responseText){
                $.get( '" . \Yii::$app->urlManagerFrontend->createUrl(['media/default/render-row']) . "?id=' + responseText.id, function(data) {
                    $('.media-container').prepend(data);
                });
            }",
        ]
    ]); ?>
    <!-- END Dropzone Content -->
</div>

<div class="row media-container">
    <?php if ($model->media): ?>
        <?php foreach ($model->media as $media): ?>
            <?= $this->render('@media/views/default/mediaRow.php', [
                    'media' => $media,
                ]); ?>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
