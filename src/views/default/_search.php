<?php

use hesabro\errorlog\models\MGTarget;
use hesabro\helpers\widgets\DateRangePicker\DateRangePicker;
use hesabro\errorlog\Module;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var yii\web\View $this */
/* @var hesabro\errorlog\models\MGTargetSearch $model */
/* @var yii\widgets\ActiveForm $form */
$client = Module::getInstance()->client;
?>
<?php $form = ActiveForm::begin([
    'method' => 'get',
]); ?>
    <div class="card-body">
        <div class="row">
            <div class="col-md-3">
                <?= $form->field($model, 'application')->dropDownList(MGTarget::itemAlias('Application'), ['prompt' => \hesabro\errorlog\Module::t('module', 'Select...')]) ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, 'category') ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, 'user_full_name') ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, 'message') ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, 'type')->dropDownList(MGTarget::itemAlias('Type'), ['prompt' => \hesabro\errorlog\Module::t('module', 'Select...')]) ?>
            </div>

            <?php if ($client): ?>
                <div class="col-md-3">
                    <?= $form->field($model, 'client_id')->widget(Select2::class, [
                        'data' => ArrayHelper::map(Module::getInstance()->client::find()->all(), 'id', 'title'),
                        'options' => ['placeholder' => Yii::t("app", "Search"), 'multiple' => true],
                        'pluginOptions' => [
                            'allowClear' => true,
                        ],
                    ]);
                    ?>
                </div>
            <?php endif; ?>

            <div class="col-md-2">
                <?= $form->field($model, 'fromDate')->widget(DateRangePicker::class, [
                    'options' => [
                        'locale' => [
                            'format' => 'jYYYY/jMM/jDD HH:mm',
                        ],
                        'drops' => 'down',
                        'opens' => 'right',
                        'jalaali' => true,
                        'showDropdowns' => true,
                        'language' => 'fa',
                        'singleDatePicker' => true,
                        'useTimestamp' => true,
                        'timePicker' => true,
                        'timePickerSeconds' => true,
                        'autoApply' => true,
                        'timePicker24Hour' => true,
                    ],
                    'htmlOptions' => [
                        'id' => 'mgtargetsearch-fromDate',
                        'class' => 'form-control',
                        'autoComplete' => 'none',
                    ],
                ]); ?>
            </div>

            <div class="col-md-2">
                <?= $form->field($model, 'toDate')->widget(DateRangePicker::class, [
                    'options' => [
                        'locale' => [
                            'format' => 'jYYYY/jMM/jDD HH:mm',
                        ],
                        'drops' => 'down',
                        'opens' => 'right',
                        'jalaali' => true,
                        'showDropdowns' => true,
                        'language' => 'fa',
                        'singleDatePicker' => true,
                        'useTimestamp' => true,
                        'timePicker' => true,
                        'timePickerSeconds' => true,
                        'autoApply' => true,
                        'timePicker24Hour' => true,
                    ],
                    'htmlOptions' => [
                        'id' => 'mgtargetsearch-toDate',
                        'class' => 'form-control',
                        'autoComplete' => 'none',
                    ],
                ]); ?>
            </div>

            <div class="col align-self-center text-right">
                <?= Html::submitButton(\hesabro\errorlog\Module::t('module', 'Search'), ['class' => 'btn btn-primary']) ?>
                <?= Html::resetButton(\hesabro\errorlog\Module::t('module', 'Reset'), ['class' => 'btn btn-secondary']) ?>
            </div>
        </div>
    </div>
<?php ActiveForm::end(); ?>