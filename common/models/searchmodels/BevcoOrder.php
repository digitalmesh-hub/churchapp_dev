<?php

namespace common\models\searchmodels;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\extendedmodels\ExtendedBevcoOrder;


class BevcoOrder extends ExtendedBevcoOrder
{
    public $institution_id;
    public $member_name;

    public function init()
    {
        parent::init();
        $this->institution_id = yii::$app->user->identity->institutionid;
    }
  
    public function rules()
    {
        return [
            [['id','status', 'member_id'], 'integer'],
            [['member_name'],'string'],
            [['created_at', 'order_date', 'member_name'], 'safe']
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = ExtendedBevcoOrder::find();
        $query->joinWith('member');

        $dataProvider = new ActiveDataProvider(
            [
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_DESC,
                ]
            ],
        ]);

        $this->load($params);
       
        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'institution_id' => $this->institution_id,
            'id' => $this->id,
            'status' => $this->status,
            'member_id' => $this->member_id
        ]);


        $query->andFilterWhere(['or',
            ['like', 'CONCAT(CONCAT_WS(" ", member.firstName, member.middleName, member.lastName), "(",member.memberno, ")")', $this->member_name],
            ['like', 'CONCAT(CONCAT_WS(" ", member.spouse_firstName, member.spouse_middleName, member.spouse_lastName), "(",member.memberno, ")")', 
            $this->member_name],
        ]); 

        if($this->order_date) {
            $query->andFilterWhere(['between', 'order_date', date('Y-m-d H:i:s',strtotimeNew($this->order_date.'00:00:00')),date('Y-m-d H:i:s',strtotimeNew($this->order_date.'23:59:59'))
            ]);
        }
        
        if($this->created_at) {
            $query->andFilterWhere(['between', 'created_at', date('Y-m-d H:i:s',strtotimeNew($this->created_at.'00:00:00')),
                date('Y-m-d H:i:s',strtotimeNew($this->created_at.'23:59:59'))
            ]);
        }
        
        return $dataProvider;
    }

    public function getMembers()
    {
        $response = [];
        try{
            $response = Yii::$app->db->createCommand("CALL get_members_for_bevco_booking(:institutionId)")
                ->bindValue(':institutionId', $this->institution_id)->queryAll();
        } catch(\Exception $e){
            yii::error('Error while fetching data for auto suggest in getMemberForEventAutoSuggest');
        }
        return $response;
    }
}
