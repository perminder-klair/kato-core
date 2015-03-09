<?php
use yii\helpers\Html;
?>

<ul>
    <?php foreach ($data as $item): ?>
    <li>
        <?= Html::a($item['title'], $item['loc']) ?>
    </li>
    <?php endforeach; ?>
</ul>

<?= Html::a('XML Sitemap', '/sitemap.xml', ['class' => 'btn btn-link pull-right']) ?>
