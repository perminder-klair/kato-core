<?php
use yii\helpers\Html;
use \yii\bootstrap\Modal;
?>
<?php foreach ($dataProvider as $data): ?>
    <div class="pages-tree-item">
        - <?= Html::a($data['model']->title, ['update', 'id' => $data['model']->id], ['class' => 'title-link']) ?> (<small><?= $data['model']->id; ?></small>)
        <div class="tree-btns">
            <?= Html::a('Edit', ['update', 'id' => $data['model']->id], ['class' => 'btn btn-primary btn-xs']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $data['model']->id], ['class' => 'btn btn-primary btn-xs popup-modal', 'data-toggle' => 'modal',
                'data-target' => '#modal',
                'data-id' => $data['model']->id,
                'id' => 'popupModal-'. $data['model']->id]) ?>
        </div>
        <?php if ($data['children']): ?>
            <?php echo $this->render('_pageTreeItem', [
                'dataProvider' => $data['children'],
            ]) ?>
        <?php endif; ?>
    </div>
<?php endforeach; ?>

<?php Modal::begin([
    'header' => '<h2 class="modal-title"></h2>',
    'id'     => 'modal-delete',
    'footer' => ''.Html::a('Cancel', '#', ['class' => 'btn btn-primary btn-xs modal-close', 'data-dismiss' => "modal"]).''.Html::a('Delete', ['delete', 'id' => 0], ['class' => 'btn btn-primary btn-xs', 'id' => 'modaldeletelink']),
]); ?>

<?= 'Are you sure you wish to delete this page?'; ?>

<?php Modal::end(); ?>

<?php

$this->registerJs("$(function() {
$('.popup-modal').click(function(e) {
    e.preventDefault();
    var modal = $('#modal-delete').modal('show');
    modal.find('.modal-body').load($('.modal-dialog'));
    var that = $(this);
    var id = that.data('id');
    modal.find('.modal-title').text('Delete page ' + id+ '?');
    modal.find('.modal-footer').find('#modaldeletelink').attr('href', 'delete?id='+id);
});
});");

?>
