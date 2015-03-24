<?php
    use yii\helpers\Html;
?>
<?php foreach ($dataProvider as $data): ?>
    <div class=" ">
        - <?= Html::a($data['model']->title, ['update', 'id' => $data['model']->id], ['class' => 'title-link']) ?> (<small><?= $data['model']->id; ?></small>)
        <div class="category-block"><?php echo $data['model']->getCategoriesTitle(); ?></div>
        <div class="tree-btns">
            <?= Html::a('Edit', ['update', 'id' => $data['model']->id], ['class' => 'btn btn-primary btn-xs']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $data['model']->id], ['class' => 'btn btn-primary btn-xs']) ?>
        </div>
        <?php if ($data['children']): ?>
            <?php echo $this->render('_pageTreeItem', [
                'dataProvider' => $data['children'],
            ]) ?>
        <?php endif; ?>
    </div>
<?php endforeach; ?>
