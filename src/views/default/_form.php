<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var yii\web\View $this */
/* @var hesabro\errorlog\models\MGTarget $model */
/* @var yii\widgets\ActiveForm $form */
?>

<div class="mgtarget-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <?= $form->field($model, '_id') ?>
            </div>

            <div class="col-md-4">
                <?= $form->field($model, 'level') ?>
            </div>

            <div class="col-md-4">
                <?= $form->field($model, 'log_time') ?>
            </div>

            <div class="col-md-4">
                <?= $form->field($model, 'message') ?>
            </div>

            <div class="col-md-4">
                <?= $form->field($model, 'trace') ?>
            </div>

            <div class="col-md-4">
                <?= $form->field($model, 'category') ?>
            </div>

            <div class="col-md-4">
                <?= $form->field($model, 'userID') ?>
            </div>

            <div class="col-md-4">
                <?= $form->field($model, 'ip') ?>
            </div>

            <div class="col-md-4">
                <?= $form->field($model, 'sessionID') ?>
            </div>

            <div class="col-md-4">
                <?= $form->field($model, 'application') ?>
            </div>

            <div class="col-md-4">
                <?= $form->field($model, 'status') ?>
            </div>
        </div>
    </div>
    <div class="card-footer">
        <?= Html::submitButton($model->isNewRecord ? \hesabro\errorlog\Module::t('module', 'Create') : \hesabro\errorlog\Module::t('module', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
