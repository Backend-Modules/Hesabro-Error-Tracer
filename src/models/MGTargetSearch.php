<?php

namespace hesabro\errorlog\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;

/**
 * MGTargetSearch represents the model behind the search form of `common\models\mongo\MGTarget`.
 */
class MGTargetSearch extends MGTarget
{
    public $fromDate, $toDate;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['_id', 'level', 'log_time', 'message', 'trace', 'category', 'userID', 'ip', 'sessionID', 'application', 'type', 'status', 'client_id', 'user_full_name', 'fromDate', 'toDate'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function beforeValidate()
    {
        if ($this->type > 0) {
            $this->type = (int)$this->type;
        }
        if ($this->userID > 0) {
            $this->userID = (int)$this->userID;
        }

        if (is_array($this->client_id) && count($this->client_id) > 0) {
            $this->client_id = Yii::$app->helper::formatterIntegerArray($this->client_id);
        }
        return parent::beforeValidate();
    }

    public function attributeLabels()
    {
        return parent::attributeLabels() + [
                'fromDate' => 'از تاریخ',
                'toDate' => 'تا تاریخ',
            ];
    }

    /**
     * @param $params
     * @param string $default
     * @return ActiveDataProvider
     */
    public function search($params, $default = 'active')
    {
        $query = MGTarget::find($default);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['_id' => SORT_DESC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            '_id' => $this->_id,
            'type' => $this->type,
            'userID' => $this->userID,
        ]);

        // grid filtering conditions
        $query->andFilterWhere(['like', '_id', $this->_id])
            ->andFilterWhere(['like', 'level', $this->level])
            ->andFilterWhere(['like', 'log_time', $this->log_time])
            ->andFilterWhere(['like', 'message', $this->message])
            ->andFilterWhere(['like', 'trace', $this->trace])
            ->andFilterWhere(['like', 'category', $this->category])
            ->andFilterWhere(['like', 'ip', $this->ip])
            ->andFilterWhere(['like', 'sessionID', $this->sessionID])
            ->andFilterWhere(['like', 'application', $this->application])
            ->andFilterWhere(['like', 'user_full_name', $this->user_full_name])
            ->andFilterWhere(['IN', 'client_id', $this->client_id])
            ->andFilterWhere(['like', 'status', $this->status]);

        if ($this->fromDate) {
            $fromDateSplit = explode(' ', $this->fromDate);
            $query->andFilterWhere(['>=', 'log_time', strtotime(Yii::$app->jdf::Convert_jalali_to_gregorian($fromDateSplit[0]) . ' ' . $fromDateSplit[1])]);
        }

        if ($this->toDate) {
            $toDateSplit = explode(' ', $this->toDate);
            $query->andFilterWhere(['<=', 'log_time', strtotime(Yii::$app->jdf::Convert_jalali_to_gregorian($toDateSplit[0]) . ' ' . $toDateSplit[1])]);
        }

        return $dataProvider;
    }

    /**
     * @param $params
     * @param string $default
     * @return ArrayDataProvider
     */
    public function searchCategory($params)
    {
        $collection = Yii::$app->mongodb->getCollection('log_target');

        $authors = $collection->aggregate([
            [
                '$match' => [
                    'status' => ['$nin' => [MGTarget::STATUS_DELETED]],
                    'type' => ['$nin' => [MGTarget::HTTP_EXCEPTION, MGTarget::DEPRECATED_EXCEPTION]],
                ]
            ],
            [
                '$group' => [
                    '_id' => [
                        'category' => '$category',
                        'type' => '$type'
                    ],
                    'category' => ['$push' => '$category'],
                    'type' => ['$push' => '$type'],
                    'count' => ['$sum' => 1],
                ],
            ],
            [
                '$sort' => ['count' => -1]
            ],
        ]);


        $dataProvider = new ArrayDataProvider([
            'allModels' => $authors,
            //'sort' => ['defaultOrder' => ['amount' => SORT_DESC]]
        ]);

//        $dataProvider->sort->attributes['amount'] = [
//            'asc' => ['amount' => SORT_ASC],
//            'desc' => ['amount' => SORT_DESC],
//        ];

        return $dataProvider;
    }
}
