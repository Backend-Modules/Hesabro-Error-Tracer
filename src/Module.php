<?php

namespace hesabro\errorlog;

use Yii;
use yii\base\Module as BaseModule;
use yii\i18n\PhpMessageSource;

class Module extends BaseModule
{
    public ?string $mongo_dsn = null;

    public ?string $application = null;

    public ?string $user = null;

    public ?string $client = null;

    public ?string $host_name = null;

    public bool $isSecure = true;

    public function init()
    {
        parent::init();

        $this->registerTranslation();
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