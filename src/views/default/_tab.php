<?php


use hesabro\errorlog\models\MGTarget;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $type int */
?>
<ul class="nav nav-tabs nav-fill bg-white pt-3">
    <li class="nav-item">
        <?= Html::a('Category List', ['category'], ['class' => $type == 'Category' ? 'nav-link active' : 'nav-link', 'data-pjax' => '0']); ?>
    </li>
    <?php foreach (MGTarget::itemAlias('Type') as $index => $title): ?>
        <li class="nav-item">
            <?= Html::a($title, ['index', 'type' => $index], ['class' => $type == $index ? 'nav-link active' : 'nav-link', 'data-pjax' => '0']); ?>
        </li>
    <?php endforeach; ?>
    <li class="nav-item">
        <?= Html::a('Archive List', ['archive'], ['class' => !$type ? 'nav-link active' : 'nav-link', 'data-pjax' => '0']); ?>
    </li>
</ul>