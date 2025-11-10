<?php

namespace common\models\searchmodels;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\extendedmodels\ExtendedStaffdesignation;

/**
 * ExtendedStaffdesignationSearch represents the model behind the search form of `common\models\extendedmodels\ExtendedStaffdesignation`.
 */
class ExtendedStaffdesignationSearch extends ExtendedStaffdesignation
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['staffdesignationid', 'institutionid', 'createdby', 'modifiedby'], 'integer'],
            [['designation', 'createddatetime', 'modifieddatetime'], 'safe'],
            [['active'], 'boolean'],
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
    	$this->institutionid = $institutionId;
    	
        $query = ExtendedStaffdesignation::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'staffdesignationid' => $this->staffdesignationid,
            'institutionid' => $this->institutionid,
            'active' => $this->active,
            'createddatetime' => $this->createddatetime,
            'createdby' => $this->createdby,
            'modifieddatetime' => $this->modifieddatetime,
            'modifiedby' => $this->modifiedby,
        ]);

        $query->andFilterWhere(['like', 'designation', $this->designation]);

        return $dataProvider;
    }
}
