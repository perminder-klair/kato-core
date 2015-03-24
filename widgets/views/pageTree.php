<?php

$this->registerCss("
.pages-tree-container {
    width:100%;
    font-size: 16px;
}
.pages-tree-item {
    margin-top: 7px;
    margin-left: 25px;
    border-top: 1px solid #ccc;
    padding: 7px 0 2px 0;
    position: relative;
}
.pages-tree-item .tree-btns {
    right: 0;
    top: 7px;
    position: absolute;
}
.pages-tree-item .title-link {
    color: #333;
}
.pages-tree-item:hover {
    border-color: #ff503f;
}
");

?>

<div class="pages-tree-container">
    <?php echo $this->render('_pageTreeItem', [
        'dataProvider' => $dataProvider,
    ]) ?>
</div>
