<?php

use hesabro\errorlog\models\MGTarget;
use hesabro\errorlog\models\MGTargetSearch;
use hesabro\helpers\widgets\grid\ActionColumn;
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
    <div class="panel-group m-bot20" id="accordion">
        <div class="card-header d-flex justify-content-between">
            <h4 class="panel-title">
                <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion"
                   href="#collapseOne" aria-expanded="false">
                    <i class="far fa-search"></i> جستجو
                </a>
            </h4>
            <div>
                <?= $searchModel->type && Yii::$app->controller->action->id == "index" ? Html::a(
                    \hesabro\errorlog\Module::t('module', 'Delete All'),
                    [
                        'delete-all',
                        'type' => $searchModel->type,
                        'category' => $searchModel->category,
                        'application' => $searchModel->application
                    ],
                    [
                        'class' => 'btn btn-danger ajax-btn',
                        'title' => Yii::t('yii', 'Delete'),
                        'data-confirm' => Yii::t("app", 'Are you sure you want to delete this item?'),
                        'data-view' => 'index',
                        'data-p-jax' => '#p-jax-view-log',
                    ]
                ) : '' ?>

                <?= $findType == "archive" ? Html::a(
                    'حذف دايمی سه ماه به قبل',
                    [
                        'delete-all-permanently',
                    ],
                    [
                        'class' => 'btn btn-danger ajax-btn',
                        'title' => Yii::t('yii', 'Delete'),
                        'data-confirm' => Yii::t("app", 'Are you sure you want to delete this item?'),
                        'data-view' => 'index',
                        'data-p-jax' => '#p-jax-view-log',
                    ]
                ) : '' ?>
            </div>
        </div>
        <div id="collapseOne" class="panel-collapse collapse" aria-expanded="false">
            <?= $this->render('_search', ['model' => $searchModel]); ?>
        </div>
    </div>
    <div class="card-body">

        <?= $this->render('_tab', [
            'type' => $searchModel->type,
        ]) ?>
    </div>
    <div class="card-body">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'tableOptions' => ['class' => 'table table-striped table-bordered text-center', 'style' => "table-layout: fixed;"],
            //'filterModel' => $searchModel,
            'showCustomToolbar' => true,
            'showCreateBtnAtToolbar' => false,
            'reloadPjaxContainer' => 'p-jax-view-log',
            'columns' => [
                //['class' => 'yii\grid\SerialColumn',],
                [
                    'class' => 'kartik\grid\ExpandRowColumn',
                    'expandIcon' => '<span class="fal fa-chevron-down" style="font-size: 13px"></span>',
                    'collapseIcon' => '<span class="fal fa-chevron-up" style="font-size: 13px"></span>',
                    'value' => function ($model, $key, $index, $column) {
                        return GridView::ROW_COLLAPSED;
                    },
                    'detailRowCssClass' => 'table-secondary',
                    'detailUrl' => Url::to(['expand', 'type' => $searchModel->type ? 'active' : 'archive']),
                ],
                //'level',
                [
                    'attribute' => 'category',
                    'value' => function ($model)use ($searchModel, $findType) {
                        return Html::a($model->category, Url::to(["index", 'MGTargetSearch[category]' => $model->category, 'type' => $searchModel->type ?: '']));
                    },
                    'format' => 'raw'
                ],
                [
                    'attribute' => 'application',
                    'value' => function ($model)use ($searchModel, $findType) {
                        return Html::a($model->application, Url::to(["index", 'MGTargetSearch[application]' => $model->application, 'type' => $searchModel->type ?: '']));
                    },
                    'format' => 'raw'
                ],
                [
                    'attribute' => 'type',
                    'value' => function ($model) {
                        return MGTarget::itemAlias('Type', $model->type);
                    },
                    'format' => 'raw'
                ],
                [
                    'attribute' => 'userID',
                    'value' => function ($model) {
                        return $model->userID . ' - ' . $model->user_full_name;
                    },
                    'format' => 'raw'
                ],
                [
                    'attribute' => 'ip',
                    'label' => 'IP',
                    'value' => function ($model) {
                        return Html::a($model->ip, "https://whatismyipaddress.com/ip/{$model->ip}", ['target' => '_blank']);
                    },
                    'format' => 'raw'
                ],
                [
                    'attribute' => 'log_time',
                    'value' => function (MGTarget $model) {
                        return Yii::$app->jdf->jdate('Y/m/d H:i:s', $model->log_time);
                    },
                ],
				[
					'attribute' => 'client_id',
					'format' => 'raw',
					'value' => function ($model) {
						return $model->client->title ?? '';
					}
				],
                [
                    'class' => ActionColumn::class,
                    'template' => '{delete}{view}',
                    'buttons' => [
                        'view' => function ($url, $model, $key) use($findType) {
                            return
                                Html::a('<span class="fa fa-eye text-info"></span>',
                                    'javascript:void(0)', [
                                        'title' => \hesabro\errorlog\Module::t('module', 'Details'),
                                        'id' => 'view-ipg-btn',
                                        'data-size' => 'modal-xxl',
                                        'data-title' => \hesabro\errorlog\Module::t('module', 'Details'),
                                        'data-toggle' => 'modal',
                                        'data-target' => '#modal-pjax',
                                        'data-url' => Url::to(['view', 'id' => (string)$model->_id, 'findType' => $findType]),
                                        'data-action' => 'view-ipg',
                                        'data-handleFormSubmit' => 0,
                                        'disabled' => true
                                    ]);
                        },
                        'delete' => function ($url, $model, $key) {
                            return $model->canDelete() ? Html::a('<span class="far fa-trash-alt"></span>', ['delete', 'id' => (string)$key], [
                                'class' => 'text-danger ajax-btn',
                                'title' => Yii::t('yii', 'Delete'),
                                'data-confirm' => Yii::t("app", 'Are you sure you want to delete this item?'),
                                'data-view' => 'index',
                                'data-p-jax' => '#p-jax-view-log',
                            ]) : '';
                        },
                    ]
                ],
            ],
        ]); ?>
    </div>
</div>
<?php Pjax::end() ?>