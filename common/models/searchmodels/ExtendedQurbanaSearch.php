<?php

namespace common\models\searchmodels;

use common\models\basemodels\Qurbana;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ExtendedQurbanaSearch represents the model behind the search form of `common\models\basemodels\Qurbana`.
 */
class ExtendedQurbanaSearch extends Qurbana
{
    /**
     * @inheritdoc
     */
    public $qurbana_date;
    public $qurbana_type_id;
    public $institution_id;

    public function rules()
    {
        return [
            [['qurbana_type_id', 'institution_id'], 'integer'],
            [['qurbana_date'], 'date', 'format' => 'php:Y-m-d'],
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
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {

        $this->institution_id = yii::$app->user->identity->institutionid;
        $query = Qurbana::find()
            ->where(['institution_id' => $this->institution_id]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 25,
            ],
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_DESC,
                ]
            ],
        ]);
        $this->load($params);

        if (!$this->validate()) {
            throw new \yii\web\BadRequestHttpException('Validation failed: ' . json_encode($this->getErrors()));
        }

        if ($this->qurbana_date) {
            $formattedDate = date('Y-m-d', strtotime($this->qurbana_date));
            $query->andFilterWhere(['DATE(qurbana_date)' => $formattedDate]);
        }
        if ($this->qurbana_type_id) {
            $query->andFilterWhere(['qurbana_type_id' => $this->qurbana_type_id]);
        }
        return $dataProvider;
    }
}
