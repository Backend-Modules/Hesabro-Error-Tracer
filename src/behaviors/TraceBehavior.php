<?php

namespace hesabro\errorlog\behaviors;

use hesabro\helpers\components\Helper;
use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * Class TraceBehavior
 * @package common\behaviors
 * @author Nader <nader.bahadorii@gmail.com>
 * @property ActiveRecord $owner
 */
class TraceBehavior extends \yii\base\Behavior
{
    public $ownerClassName;

    public $excludeClass = [];

    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_VALIDATE => 'afterValidate',
        ];
    }

    public function afterValidate()
    {
        $helper = new Helper();
        if ($this->owner->hasErrors() && !in_array($this->ownerClassName, $this->excludeClass)) {
            $log = [
                'errors' => $this->owner->getErrors(),
                'data' => $this->owner->getAttributes()
            ];
            try {
                if (is_array($this->owner->getErrors())) {
                    foreach ($this->owner->getErrors() as $attribute => $errorMsg) {
                        if (ArrayHelper::isIn(Yii::$app->id, ['app-backend', 'app-managerBranch'])) {
                            if (Yii::$app->request->isAjax) {
                                Yii::$app->params['errorHiddenAjax'] .= $this->owner->getAttributeLabel($attribute) . '=>' . (is_array($errorMsg) ? $helper->arrayToString($errorMsg, false) : $errorMsg);
                            } else {
                                Yii::$app->params['errorHidden'] .= $this->owner->getAttributeLabel($attribute) . '=>' . (is_array($errorMsg) ? $helper->arrayToString($errorMsg, false) : $errorMsg);
                            }
                        } elseif (ArrayHelper::isIn(Yii::$app->id, ['app-api'])) {
                            Yii::$app->params['errorHidden'] .= (is_array($errorMsg) ? $helper->arrayToString($errorMsg, false) : $errorMsg);
                        }
                    }
                    if (ArrayHelper::isIn(Yii::$app->id, ['app-backend']) && !empty(Yii::$app->params['errorHidden'])) {
                        Yii::$app->getSession()->setFlash('info', Yii::$app->params['errorHidden']);
                    }
                }
            } catch (\Exception $e) {
                Yii::error($e->getMessage() . $e->getTraceAsString(), 'TraceBehavior');
            }
            Yii::error($log, $this->ownerClassName . " / 422 Data Validation Failed");
        }
        return true;
    }
}