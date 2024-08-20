<?php

namespace hesabro\errorlog;

use Yii;
use yii\base\Module as BaseModule;
use yii\i18n\PhpMessageSource;
use hesabro\helpers\Module as HesabroHelpersModule;

class Module extends BaseModule
{
    public string $mongoConnection = 'mongodb';

    public ?string $application = null;

    public ?string $user = null;

    public ?string $client = null;

    public ?string $hostName = null;

    public bool $isSecure = true;

    public function init()
    {
        parent::init();

        $this->registerTranslation();

        $this->setModules([
            'helpers' => [
                'class' => HesabroHelpersModule::class,
            ]
        ]);
    }

    private function registerTranslation(): void
    {
        Yii::$app->i18n->translations['hesabro/errorlog*'] = [
            'class' => PhpMessageSource::class,
            'basePath' => '@hesabro/errorlog/messages',
            'sourceLanguage' => 'en-US',
            'fileMap' => [
                'hesabro/errorlog/module' => 'module.php'
            ],
        ];
    }

    public static function t($category, $message, $params = [], $language = null): string
    {
        return Yii::t('hesabro/errorlog/' . $category, $message, $params, $language);
    }
}