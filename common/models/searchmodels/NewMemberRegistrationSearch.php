<?php

namespace common\models\searchmodels;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\basemodels\NewMemberRegistration;

class NewMemberRegistrationSearch extends NewMemberRegistration
{
    public $searchKeyword;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['searchKeyword'], 'string'],
            [['searchKeyword'],'trim']
            
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
        $query = NewMemberRegistration::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                     
                ]
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }
      
       
        if($this->searchKeyword){}
        return $dataProvider;
    }
}
