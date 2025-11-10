<?php

namespace common\models\searchmodels;

use Yii;
use yii\base\Model;
use yii\data\ArrayDataProvider;
use common\models\extendedmodels\ExtendedRsvpdetails;

class ExtendedRsvpDetailSearch extends ExtendedRsvpdetails
{   
    public $searchParam;
    public $id;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'],'integer'],
            [['searchParam'],'integer']
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
        $provider = [];
        $this->load($params);
        if (!$this->validate()) {
            return $provider;
        } 
        $value = null;
        switch ($this->searchParam) {
            case '1':
                $value = false;
                break;
            case '2':
                $value= null;
                break;
            case '0':
                $value = true;
                break;
        }
        $boundParams = [':institutionid' => yii::$app->user->identity->institutionid, 
                ':rsvpid' => $this->id, 
                ':rsvpvalue' => $value
            ];
        $data = Yii::$app->db->createCommand("
                CALL getallrsvp(:rsvpid, :institutionid, :rsvpvalue) ")
                ->bindValues($boundParams)
                ->queryAll();

        $provider = new ArrayDataProvider([
            'allModels' => $data,
            'pagination' => [
                'pageSize' => 20,
            ],
            ]);
        
        return $provider;
    }
}
