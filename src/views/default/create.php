<?php
/* @var $this yii\web\View */
/* @var $model hesabro\errorlog\models\MGTarget */

$this->title = \hesabro\errorlog\Module::t('module', 'Create Mg Target');
$this->params['breadcrumbs'][] = ['label' => \hesabro\errorlog\Module::t('module', 'Mg Targets'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="card">
	<?= $this->render('_form', [
		'model' => $model,
	]) ?>
</div>
