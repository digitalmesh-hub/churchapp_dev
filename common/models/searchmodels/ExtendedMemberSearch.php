<?php

namespace common\models\searchmodels;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\extendedmodels\ExtendedMember;

/**
 * ExtendedMemberSearch represents the model behind the search form of `common\models\extendedmodels\ExtendedMember`.
 */
class ExtendedMemberSearch extends ExtendedMember
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
    	$this->membertype    = 0;
        $query = ExtendedMember::find()
                  ->where(['institutionid' => $institutionid,'membertype'=>0]);

        $query->orderBy('CONCAT_WS("",`firstName`, `middleName`, `lastName`) ASC');

        // add conditions that should always apply here
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        	'pagination' => [
        			'pageSize' => 20
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
        				['like', 'firstName', $this->searchParam],
        				['like', 'lastName', $this->searchParam],
        				['like', 'middleName', $this->searchParam],
                        ['like', 'CONCAT_WS("",firstName, middleName, lastName)', $this->searchParam],
        		        ['like', 'CONCAT_WS(" ",firstName, middleName, lastName)', $this->searchParam],
                        ['like', 'CONCAT_WS(" ",spouse_firstName, spouse_middleName, spouse_lastName)', $this->searchParam],
        		        ['like', 'CONCAT_WS("",spouse_firstName, spouse_middleName, spouse_lastName)', $this->searchParam],
                        ['like', 'CONCAT_WS(" ",firstName, lastName)', $this->searchParam],
        		        ['like', 'CONCAT_WS("",firstName, lastName)', $this->searchParam],
        		        ['like', 'CONCAT_WS(" ",spouse_firstName, spouse_lastName)', $this->searchParam],
        		        ['like', 'CONCAT_WS("",spouse_firstName, spouse_lastName)', $this->searchParam],
        				['like', 'membernickname', $this->searchParam],
        				['like', 'member_mobile1', $this->searchParam],
        				['like', 'spouse_firstName', $this->searchParam],
        				['like', 'spouse_lastName', $this->searchParam],
        				['like', 'spouse_middleName', $this->searchParam],
        				['like', 'spousenickname', $this->searchParam],
        				['like', 'spouse_mobile1', $this->searchParam],
        				['like', 'batch', $this->searchParam],
        		]
        );

    
        return $dataProvider;
    }
}
