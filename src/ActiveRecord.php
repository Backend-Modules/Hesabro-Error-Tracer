<?php

namespace hesabro\errorlog;

use Yii;
use yii\mongodb\ActiveRecord as BaseActiveRecord;

class ActiveRecord extends BaseActiveRecord
{
    public static function getDb()
    {
        return Yii::$app->get(Yii::$app->getModule('error-log')?->mongoConnection);
    }
}
