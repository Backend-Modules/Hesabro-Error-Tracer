<?php

use hesabro\errorlog\models\MGTarget;
use hesabro\errorlog\models\MGTargetSearch;
use hesabro\helpers\widgets\grid\GridView;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Pjax;

/* @var $this View */
/* @var $searchModel MGTargetSearch */
/* @var $dataProvider ActiveDataProvider */
/* @var $findType string */

$this->title = \hesabro\errorlog\Module::t('module', 'Mg Targets');
$this->params['breadcrumbs'][] = $this->title;
?>
<?php Pjax::begin(['id' => 'p-jax-view-log', 'timeout' => false]) ?>
    <div class="mgtarget-index card">
        <div class="card-body">
            <?= $this->render('_tab', [
                'type' => 'Category',
            ]) ?>
        </div>
        <div class="card-body">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'tableOptions' => ['class' => 'table table-striped table-bordered text-center', 'style' => "table-layout: fixed;"],
                //'filterModel' => $searchModel,
                //'showCustomToolbar' => true,
                //'showCreateBtnAtToolbar' => false,
                //'reloadPjaxContainer' => 'p-jax-view-log',
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    [
                        'attribute' => 'category',
                        'value' => function ($model) {
                            return Html::a($model['_id']['category'], Url::to(["index", 'MGTargetSearch[category]' => $model['_id']['category'], 'type' => $model['_id']['type'] ?: '']));
                        },
                        'vAlign' => 'middle',
                        'format' => 'raw'
                    ],
                    [
                        'attribute' => 'type',
                        'value' => function ($model) {
                            return MGTarget::itemAlias('Type', $model['_id']['type']);
                        },
                    ],
                    'count',
                ],
            ]); ?>
        </div>
    </div>
<?php Pjax::end() ?>