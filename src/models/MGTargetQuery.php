<?php

namespace hesabro\errorlog\models;

use yii\mongodb\ActiveQuery;

/**
 * This is the ActiveQuery class for [[MGTarget]].
 *
 * @see MGFactor
 */
class MGTargetQuery extends ActiveQuery
{
    /**
     * @inheritdoc
     * @return MGTarget[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return MGTarget|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function active()
    {
        return $this->andWhere(['<>', 'status', MGTarget::STATUS_DELETED]);
    }

    public function archive()
    {
        return $this->andWhere(['status' => MGTarget::STATUS_DELETED]);
    }

    public function byType($type)
    {
        return $this->andWhere(['type' => (int)$type]);
    }

    public function byId($id)
    {
        return $this->andWhere(['_id' => $id]);
    }

    public function byCategory($category)
    {
        return $this->andWhere(['category' => $category]);
    }
}