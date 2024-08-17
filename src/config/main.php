<?php

use hesabro\errorlog\logs\MongoTarget;
use hesabro\errorlog\models\MGTarget;
use yii\log\Dispatcher;
use yii\mongodb\Connection;
use hesabro\helpers\Module as HesabroHelpersModule;

/**
 * @var array $config
 */

return [
    'components' => [
        'mongodb' => [
            'class' => Connection::class,
            'dsn' => $config['mongo_dsn'] ?? ''
        ],
        'log' => [
            'class' => Dispatcher::class,
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => MongoTarget::class,
                    'application' => $config['application'] ?? '',
                    'type' => MGTarget::ERROR_EXCEPTION,
                    'levels' => ['error'],
                ]
            ]
        ]
    ],
    'modules' => [
        'helpers' => [
            'class' => HesabroHelpersModule::class,
        ]
    ]
];
