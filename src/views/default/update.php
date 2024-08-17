<?php

/* @var $this yii\web\View */
/* @var $model hesabro\errorlog\models\MGTarget */

$this->title = \hesabro\errorlog\Module::t('module', 'Update');
$this->params['breadcrumbs'][] = ['label' => \hesabro\errorlog\Module::t('module', 'Mg Targets'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->_id, 'url' => ['view', 'id' => (string)$model->_id]];
$this->params['breadcrumbs'][] = \hesabro\errorlog\Module::t('module', 'Update');
?>
<div class="mgtarget-update card">
	<?= $this->render('_form', [
		'model' => $model,
	]) ?>
</div>
