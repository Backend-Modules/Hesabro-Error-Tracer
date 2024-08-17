<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model hesabro\errorlog\models\MGTarget */

$this->title = $model->_id;
$this->params['breadcrumbs'][] = ['label' => \hesabro\errorlog\Module::t('module', 'Mg Targets'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mgtarget-view card">
	<div class="card-body">
	<?= DetailView::widget([
		'model' => $model,
		'attributes' => [
            '_id',
            'level',
            'log_time',
            'message',
            'trace',
            'category',
            'userID',
            'ip',
            'sessionID',
            'application',
            'status',
		],
	]) ?>
	</div>
	<div class="card-footer">
		<?= Html::a(\hesabro\errorlog\Module::t('module', 'Update'), ['update', 'id' => (string)$model->_id], ['class' => 'btn btn-primary']) ?>
		<?= Html::a(\hesabro\errorlog\Module::t('module', 'Delete'), ['delete', 'id' => (string)$model->_id], [
		'class' => 'btn btn-danger',
		'data' => [
		'confirm' => \hesabro\errorlog\Module::t('module', 'Are you sure you want to delete this item?'),
		'method' => 'post',
		],
		]) ?>
	</div>
</div>
