<?php

namespace hesabro\errorlog\models;

use hesabro\errorlog\Module;
use Yii;
use yii\log\Logger;
use hesabro\errorlog\ActiveRecord;
use yii\web\Request;

/**
 * @property int $_id
 * @property int $level
 * @property string $category
 * @property double $log_time
 * @property string $userID
 * @property string $ip
 * @property string $sessionID
 * @property string $message
 * @property string $application
 * @property string $type
 * @property string $trace
 * @property int $status
 * @property int $client_id
 * @property string $user_full_name
 * @property object $user
 */
class MGTarget extends ActiveRecord
{
    const STATUS_ACTIVE = 1;
    const STATUS_DELETED = 0;


    const ERROR_EXCEPTION = 1;
    const HTTP_EXCEPTION = 2;
    const VALIDATION_EXCEPTION = 3;
    const DEPRECATED_EXCEPTION = 4;

    public $applicationError = null;

    public static function collectionName()
    {
        return 'log_target';
    }

    /**
     * @return array list of attribute names.
     */
    public function attributes()
    {
        return ['_id', 'level', 'log_time', 'message', 'trace', 'category', 'userID', 'ip', 'sessionID', 'application', 'type', 'status', 'client_id', 'user_full_name'];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['level', 'userID', 'type', 'status', 'client_id'], 'integer'],
            [['log_time'], 'number'],
            [['trace', 'message', 'user_full_name'], 'string'],
            [['category'], 'string', 'max' => 255],
            [['application'], 'string', 'max' => 32],
            [['ip', 'sessionID'], 'string', 'max' => 64],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            '_id' => \hesabro\errorlog\Module::t('module', 'ID'),
            'level' => \hesabro\errorlog\Module::t('module', 'Level'),
            'category' => \hesabro\errorlog\Module::t('module', 'Category'),
            'application' => \hesabro\errorlog\Module::t('module', 'Application'),
            'log_time' => \hesabro\errorlog\Module::t('module', 'Log Time'),
            'trace' => \hesabro\errorlog\Module::t('module', 'Trace'),
            'message' => \hesabro\errorlog\Module::t('module', 'Message'),
            'ip' => \hesabro\errorlog\Module::t('module', 'Ip'),
            'sessionID' => \hesabro\errorlog\Module::t('module', 'Session ID'),
            'userID' => \hesabro\errorlog\Module::t('module', 'User ID'),
            'type' => \hesabro\errorlog\Module::t('module', 'Type'),
            'status' => \hesabro\errorlog\Module::t('module', 'Status'),
            'client_id' => \hesabro\errorlog\Module::t('module', 'Client'),
            'user_full_name' => \hesabro\errorlog\Module::t('module', 'User Full Name'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Module::getInstance()->user, ['id' => 'userID']);
    }

	public function getClient()
	{
		return $this->hasOne(Module::getInstance()->client, ['id' => 'client_id']);
	}

    public function softDelete()
    {
        $this->status = self::STATUS_DELETED;
        return $this->save(true, ['status']);
    }

    public function getTime()
    {
        $parts = explode('.', sprintf('%F', $this->log_time));

        return Yii::$app->jdf->jdate("Y/m/d H:i:s", $parts[0]) . ' - ' . $parts[1];
    }

    public function convertPreFix($type = 'ip')
    {
        $explode = explode('][', $this->prefix);
        $ip = Yii::$app->phpNewVer->strReplace('[', '', $explode[0]);
        $user_id = $explode[1];
        $sessionID = Yii::$app->phpNewVer->strReplace(']', '', $explode[2]);
        if ($type == 'user') {
            if ($user_id > 0 && ($user = Module::getInstance()->user::findOne($user_id)) !== null) {
                return $user->getLink();
            }
            return $user_id;
        }
        if ($type == 'sessionID') {
            return $sessionID;
        }
        return $ip;
    }

    public function convertMessage($form, $to)
    {
        $model = $this;
        if (($startPos = strpos($model->message, "$form")) > 0) {
            $endPos = strpos($model->message, "$to");
            $REQUEST_URI = substr($model->message, $startPos, ($endPos - $startPos));
            $REQUEST_URI = Yii::$app->phpNewVer->strReplace("$form", '', $REQUEST_URI);
            return Yii::$app->phpNewVer->strReplace("'", '', $REQUEST_URI);
        }
        return '';
    }

    public function convertMessageApplication($form, $to)
    {
        $model = $this;
        if ($model !== null && ($startPos = strpos($model->trace, "$form")) > 0) {
            $endPos = strpos($model->trace, $to);
            $REQUEST_URI = substr($model->trace, $startPos, ($endPos - $startPos));
            $REQUEST_URI = Yii::$app->phpNewVer->strReplace($form, '', $REQUEST_URI);
            return Yii::$app->phpNewVer->strReplace($to, '', $REQUEST_URI);
        }
        return '';
    }

    public function canDelete()
    {
        return $this->status == self::STATUS_ACTIVE;
    }

    public static function itemAlias($type, $code = NULL)
    {
        $_items = [
            'Type' => [
                self::ERROR_EXCEPTION => 'Error Exception',
                self::HTTP_EXCEPTION => 'Http Exception',
                self::VALIDATION_EXCEPTION => 'Validation Exception',
                self::DEPRECATED_EXCEPTION => 'Deprecated Exception',
            ],
            'Application' => [
                'backend' => 'Backend',
                'console' => 'Console',
                'api' => 'Api',
                'branches' => 'Branches',
            ],
            'Level' => [
                Logger::LEVEL_ERROR => 'error',
                Logger::LEVEL_WARNING => 'warning',
                Logger::LEVEL_INFO => 'info',
                Logger::LEVEL_TRACE => 'trace',
                Logger::LEVEL_PROFILE => 'profile',
            ],
            'BaseUrl' => [
                'Backend' => Yii::$app->phpNewVer->trim(Yii::$app->urlManager->createAbsoluteUrl(''), '/'),
                'Console' => ''
            ],
            'ignoreSmsMonitoring' => [
                'api\modules\accounting\modules\v1\models\DocumentDetailsApiForm',

            ],
        ];
        if (isset($code))
            return isset($_items[$type][$code]) ? $_items[$type][$code] : false;
        else
            return isset($_items[$type]) ? $_items[$type] : false;
    }

    /**
     * @inheritdoc
     * @return MGTargetQuery the active query used by this AR class.
     */
    public static function find($default = 'active')
    {
        $query = new MGTargetQuery(get_called_class());
        if ($default == 'active') {
            return $query->active();
        } else if ($default == 'all') {
            return $query;
        } else {
            return $query->archive();
        }
    }

    public function beforeSave($insert)
    {
        if ($insert) {
            $this->status = self::STATUS_ACTIVE;
            $user = Yii::$app->has('user', true) ? Yii::$app->get('user') : null;
            if ($user && ($identity = $user->getIdentity(false))) {
                $this->userID = $identity->getId();
				$this->user_full_name = $user->identity->fullName;
            } else {
                $this->userID = null;
            }

            $request = Yii::$app->getRequest();
            $this->ip = $request instanceof Request ? $request->getUserIP() : '-';

			$this->client_id = Yii::$app->client->id;
            /* @var $session \yii\web\Session */
            $session = Yii::$app->has('session', true) ? Yii::$app->get('session') : null;
            $this->sessionID = $session && $session->getIsActive() ? $session->getId() : '-';
        }

        return parent::beforeSave($insert);
    }
}