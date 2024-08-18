<?php

namespace hesabro\errorlog;

use hesabro\errorlog\logs\MongoTarget;
use hesabro\errorlog\models\MGTarget;
use yii\base\Application;
use yii\base\BootstrapInterface;
use yii\base\InvalidConfigException;

class Bootstrap implements BootstrapInterface
{
    private array $requiredConfigs = [
        'application',
        'user'
    ];

    public function bootstrap($app)
    {
        $config = $this->findConfig($app);

        if (!$config) {
            return;
        }

        $app->params['bsVersion'] = 4;

        $configs = require __DIR__ . '/config/main.php';

        $this->setConfig($app, $configs['components'] ?? [], 'components');
        $this->setConfig($app, $configs['modules'] ?? [], 'modules');

        $logConfig = $app->components['log'] ?? [];
        $app->setComponents([
            'log' => array_merge($logConfig, [
                'targets' => array_merge($logConfig['targets'] ?? [], [
                    [
                        'class' => MongoTarget::class,
                        'application' => $config['application'],
                        'type' => MGTarget::ERROR_EXCEPTION,
                        'levels' => ['error'],
                    ],
                ])
            ])
        ]);
    }

    private function findConfig(Application $app): array|bool
    {
        $moduleConfig = current(array_filter($app->modules, fn($i) => ($i['class'] ?? '') === Module::class));

        if (!$moduleConfig) {
            return false;
        }

        foreach ($this->requiredConfigs as $requiredConfig) {
            if (!isset($moduleConfig[$requiredConfig])) {
                throw new InvalidConfigException(Module::class .": '$requiredConfig' must configure in module setup");
            }
        }

        return $moduleConfig;
    }

    private function setConfig(Application $app, array $items, string $target): void
    {
        foreach ($items as $item => $config) {

            $notExist = !current(array_filter($app->$target, fn($i) => strtolower(trim($i['class'] ?? 'unknown', '\\')) === strtolower(trim($config['class'] ?? '', '\\'))));

            $method = 'set' . ucfirst($target);

            if ($notExist) {
                $app->$method([ $item => $config ]);
            }
        }
    }
}