<?php

namespace common\models\searchmodels;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\extendedmodels\ExtendedMember;

/**
 * ExtendedMemberSearch represents the model behind the search form of `common\models\extendedmodels\ExtendedMember`.
 */
class ExtendedStaffSearch extends ExtendedMember
{  
	
	public $searchParam;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['memberno', 'searchParam'], 'string'],
            [['searchParam', 'memberno'], 'trim'],
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
    public function search($params,$institutionid)
    {
    	$this->institutionid = $institutionid;
    	$this->membertype    = 1;
        $query = ExtendedMember::find()
                  ->where(['institutionid' => $institutionid,'membertype'=>1]);
        

        // add conditions that should always apply here

       $dataProvider = new ActiveDataProvider([
            'query' => $query,
        	'pagination' => [
        			'pageSize' => 20
        	],
        	'sort' => [
        			'defaultOrder' => [
        					'firstName' => SORT_ASC
        			]
        	],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

  

        $query->andFilterWhere(['like', 'memberno', $this->memberno]);
        $query->andFilterWhere(
        		[
        				'or',
        				['like', 'firstname', $this->searchParam],
        				['like', 'lastname', $this->searchParam],
        				['like', 'middlename', $this->searchParam],
        				['like', 'membernickname', $this->searchParam],
        				['like', 'member_mobile1', $this->searchParam],
        				
        		]
        );

        return $dataProvider;
    }
}
