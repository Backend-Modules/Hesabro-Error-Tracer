<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace hesabro\errorlog\logs;

use hesabro\errorlog\models\MGTarget;
use Yii;
use yii\base\InvalidConfigException;
use yii\di\Instance;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\log\LogRuntimeException;
use yii\log\Target;
use yii\mongodb\Connection;
use yii\mongodb\Exception;
use yii\web\Request;

/**
 * DbTarget stores log messages in a database table.
 *
 * The database connection is specified by [[db]]. Database schema could be initialized by applying migration:
 *
 * ```
 * yii migrate --migrationPath=@yii/log/migrations/
 * ```
 *
 * If you don't want to use migration and need SQL instead, files for all databases are in migrations directory.
 *
 * You may change the name of the table used to store the data by setting [[logTable]].
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class MongoTarget extends Target
{
    /**
     * @var Connection|array|string the DB connection object or the application component ID of the DB connection.
     * After the DbTarget object is created, if you want to change this property, you should only assign it
     * with a DB connection object.
     * Starting from version 2.0.2, this can also be a configuration array for creating the object.
     */
    public $db = 'mongodb';

    /**
     * @var string
     */
    public $application;

    /**
     * @var string
     */
    public $type;


    /**
     * @var array list of the PHP predefined variables that should be logged in a message.
     * Note that a variable must be accessible via `$GLOBALS`. Otherwise it won't be logged.
     *
     * Defaults to `['_GET', '_POST', '_FILES', '_COOKIE', '_SESSION', '_SERVER']`.
     *
     * Since version 2.0.9 additional syntax can be used:
     * Each element could be specified as one of the following:
     *
     * - `var` - `var` will be logged.
     * - `var.key` - only `var[key]` key will be logged.
     * - `!var.key` - `var[key]` key will be excluded.
     *
     * Note that if you need $_SESSION to logged regardless if session was used you have to open it right at
     * the start of your request.
     *
     * @see \yii\helpers\ArrayHelper::filter()
     */
    public $logVars = [
        '_GET',
        '_POST',
        '_FILES',
        '_COOKIE',
        '_SESSION'
    ];

    /**
     * Initializes the DbTarget component.
     * This method will initialize the [[db]] property to make sure it refers to a valid DB connection.
     * @throws InvalidConfigException if [[db]] is invalid.
     */
    public function init()
    {
        parent::init();
        $this->db = Instance::ensure($this->db, Connection::className());
    }

    /**
     * Generates the context information to be logged.
     * The default implementation will dump user information, system variables, etc.
     * @return string the context information. If an empty string, it means no context information.
     */
    protected function getContextMessage()
    {
        $context = ArrayHelper::filter($GLOBALS, $this->logVars);
        foreach ($this->maskVars as $var) {
            if (ArrayHelper::getValue($context, $var) !== null) {
                ArrayHelper::setValue($context, $var, '***');
            }
        }
        $result = [];
        foreach ($context as $key => $value) {
            $result[] = "\${$key} = " . VarDumper::dumpAsString($value);
        }

        if(($request = Yii::$app->getRequest()) instanceof Request)
        {
            $result[] = 'RAW BODY: ' . $request->rawBody;
            $result[] = 'REQUEST_URI: ' . $request->url. ' END_URI;';
            $result[] = 'HTTP_REFERER: ' . $request->referrer. ' END_REFERER;';
            $result[] = 'HTTP_USER_AGENT: ' . $request->userAgent. ' END_USER_AGENT;';
        }

        return implode("\n\n", $result);
    }

    /**
     * Stores log messages to DB.
     * Starting from version 2.0.14, this method throws LogRuntimeException in case the log can not be exported.
     * @throws Exception
     * @throws LogRuntimeException
     */
    public function export()
    {
        try {
            $model = null;

            if ($this->application == 'api' && Yii::$app->apiRequest) {
                try {
                    Yii::$app->apiRequest->saveApiRequestAfterRequest();
                } catch (Exception $e) {
                    throw new LogRuntimeException($e->getMessage() . $e->getTraceAsString());
                }
            }

            foreach ($this->messages as $message) {
                [$text, $level, $category, $timestamp] = $message;
                if (!is_string($text)) {
                    // exceptions may not be serializable if in the call stack somewhere is a Closure
                    if ($text instanceof \Throwable || $text instanceof \Exception) {
                        $text = (string)$text;
                    } else {
                        $text = VarDumper::export($text);
                    }
                }
				$httpExceptions = [
					'yii\web\HttpException:401',
					'yii\web\HttpException:404',
					'yii\web\HttpException:400',
					'yii\web\HttpException:403',
				];
                if ($category != "application" || !$model instanceof MGTarget) {
					if (in_array($category, $httpExceptions)) {
						$type = MGTarget::HTTP_EXCEPTION;
					} elseif (strpos($category, '422 Data Validation Failed') !== false) {
                        $type = MGTarget::VALIDATION_EXCEPTION;
                    } elseif (strpos($category, 'yii\web\HttpException:410') !== false) {
                        $type = MGTarget::DEPRECATED_EXCEPTION;
                    } else {
                        $type = $this->type;
                    }
                    $model = new MGTarget([
                        'level' => $level,
                        'category' => \Yii::$app->phpNewVer->strReplace(" / 422 Data Validation Failed", '', $category),
                        'message' => $text,
                        'trace' => '',
                        'log_time' => $timestamp,
                        'application' => $this->application,
                        'type' => $type
                    ]);

                } else {
                    $model->trace = $text;
                }
                if (!$model->save()) {
                    continue;
                }

            }

        } catch (\Exception $e) {
            throw new LogRuntimeException('Unable to export log through mongo database!');
        }

    }
}