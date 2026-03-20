<?php

namespace common\models\searchmodels;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\extendedmodels\ExtendedSundayService;

/**
 * SundayServiceSearch represents the model behind the search form of `common\models\extendedmodels\ExtendedSundayService`.
 */
class SundayServiceSearch extends ExtendedSundayService
{
    public $service_date_from;
    public $service_date_to;
    public $created_at_from;
    public $created_at_to;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['service_date_from', 'service_date_to', 'created_at_from', 'created_at_to'], 'safe'],
            [['service_date_from', 'service_date_to', 'created_at_from', 'created_at_to'], 'date', 'format' => 'php:d M Y'],
            [['active'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     * @param int $institutionId
     *
     * @return ActiveDataProvider
     */
    public function search($params, $institutionId)
    {
        $query = ExtendedSundayService::find();
        $this->institution_id = $institutionId;

        // add conditions that should always apply here
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'service_date' => SORT_DESC,
                ]
            ],
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'institution_id' => $this->institution_id,
        ]);

        // Filter by service_date range (DatePicker format: dd MM yyyy)
        if ($this->service_date_from && $this->service_date_to) {
            $query->andFilterWhere([
                'between', 'service_date',
                date(Yii::$app->params['dateFormat']['sqlDateFormat'], strtotimeNew($this->service_date_from . ' 00:00:00')),
                date(Yii::$app->params['dateFormat']['sqlDateFormat'], strtotimeNew($this->service_date_to . ' 23:59:59'))
            ]);
        } elseif ($this->service_date_from) {
            $query->andFilterWhere(['>=', 'service_date', 
                date(Yii::$app->params['dateFormat']['sqlDateFormat'], strtotimeNew($this->service_date_from . ' 00:00:00'))
            ]);
        } elseif ($this->service_date_to) {
            $query->andFilterWhere(['<=', 'service_date', 
                date(Yii::$app->params['dateFormat']['sqlDateFormat'], strtotimeNew($this->service_date_to . ' 23:59:59'))
            ]);
        }

        // Filter by created_at range (DatePicker format: dd MM yyyy)
        if ($this->created_at_from && $this->created_at_to) {
            $query->andFilterWhere([
                'between', 'created_at',
                date(Yii::$app->params['dateFormat']['sqlDandTFormat'], strtotimeNew($this->created_at_from . ' 00:00:00')),
                date(Yii::$app->params['dateFormat']['sqlDandTFormat'], strtotimeNew($this->created_at_to . ' 23:59:59'))
            ]);
        } elseif ($this->created_at_from) {
            $query->andFilterWhere(['>=', 'created_at', 
                date(Yii::$app->params['dateFormat']['sqlDandTFormat'], strtotimeNew($this->created_at_from . ' 00:00:00'))
            ]);
        } elseif ($this->created_at_to) {
            $query->andFilterWhere(['<=', 'created_at', 
                date(Yii::$app->params['dateFormat']['sqlDandTFormat'], strtotimeNew($this->created_at_to . ' 23:59:59'))
            ]);
        }

        return $dataProvider;
    }
}
