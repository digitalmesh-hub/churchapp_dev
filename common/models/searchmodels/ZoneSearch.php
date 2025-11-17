<?php

namespace common\models\searchmodels;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\extendedmodels\ExtendedZone;

/**
 * ZoneSearch represents the model behind the search form of `common\models\extendedmodels\ExtendedZone`.
 */
class ZoneSearch extends ExtendedZone
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['zoneid', 'institutionid'], 'integer'],
            [['description', 'active'], 'safe'],
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
    public function search($params,$institutionId)
    {
        $query = ExtendedZone::find();
        $this->institutionid = $institutionId;
       
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        		'pagination' => [
        				'pageSize' => 20
        		],
        		'sort' => [
        				'defaultOrder' => [
        						'description' => SORT_ASC
        				]
        		],
        ]);
       
      
        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'zoneid' => $this->zoneid,
            'institutionid' => $this->institutionid,
        ]);

        $query->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'active', $this->active]);

        return $dataProvider;
    }
}
