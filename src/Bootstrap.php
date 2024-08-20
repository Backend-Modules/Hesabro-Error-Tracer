<?php

namespace hesabro\errorlog;

use yii\base\BootstrapInterface;

class Bootstrap implements BootstrapInterface
{
    public function bootstrap($app)
    {
        $app->params['bsVersion'] = 4;
    }
}