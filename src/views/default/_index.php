<?php

use hesabro\errorlog\models\MGTarget;
use hesabro\errorlog\Module;
use yii\helpers\Html;
use yii\helpers\HtmlPurifier;

/* @var $this yii\web\View */
/* @var $type string */
/* @var $model MGTarget */

$REQUEST_URI = $model->convertMessageApplication("REQUEST_URI: ", " END_URI;");
$RequestUrl = $model->application == 'backend' ? (Module::getInstance()->isSecure ? 'https://' : 'http://') . $model->client->domain . '.' . Module::getInstance()->host_name . $REQUEST_URI : '';
$httpReferrer = $model->convertMessageApplication("HTTP_REFERER: ", " END_REFERER;");
?>
<div class="card-body text-right" dir="ltr">
    <p>
        <b class="text-primary">Error:</b> <?= Html::encode($model->convertMessage(": ", "Stack trace:")); ?>
    </p>
    <p>
        <b class="text-primary">REQUEST_URI:</b> <?= Html::a($REQUEST_URI, $RequestUrl, ['target' => '_blank', 'data-pjax' => 0]); ?>
    </p>
    <p>
        <b class="text-primary">HTTP_REFERER:</b> <?= Html::a($httpReferrer, $httpReferrer, ['target' => '_blank', 'data-pjax' => 0]); ?>
    </p>
    <hr/>
    <?= HtmlPurifier::process(nl2br($model->message))  ?>
</div>
<div class="card-body text-right" dir="ltr">
    <hr/>
    <?= HtmlPurifier::process(nl2br($model->trace))  ?>
</div>
<hr/>
<div class="card-body text-right font-20" dir="ltr">
    <p>
        <b class="text-primary">HTTP_USER_AGENT:</b> <?= Html::encode($model->convertMessageApplication("HTTP_USER_AGENT", "END_USER_AGENT;")); ?>
    </p>
</div>