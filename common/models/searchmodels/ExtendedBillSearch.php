<?php 

namespace common\models\searchmodels;

use yii\data\ActiveDataProvider;
use common\models\extendedmodels\ExtendedBills;
use yii\base\Model;
use yii;

class ExtendedBillSearch extends ExtendedBills
{
    
    public $year;
    public $month;
    public $memberNo;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['year', 'month'], 'integer'],
            [['memberNo'],'string'],
            [['year', 'month','memberNo'], 'trim'],
            [['year','month'], 'required']
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
        $this->load($params); 
        $memberNo = "";
        if ($this->memberNo) {
            $memberNo = preg_replace('/\s+/', "", $this->memberNo);
            if(strstr($memberNo, "/") !== false) {
                $n = strpos($memberNo, "/");
              //$memberNo = substr($memberNo, $n + 1);
                $memberNo = substr($memberNo,0, $n);
            }
        }
        $dataProvider = [];
        try {
            $dataProvider = Yii::$app->db->createCommand("
                CALL get_bills_preview(:memberNo,:month,:year,:institutionid) ")
                ->bindValue(':institutionid', yii::$app->user->identity->institutionid)
                ->bindValue(':memberNo', $memberNo)
                ->bindValue(':month', $this->month)
                ->bindValue(':year', $this->year)
                ->queryAll();
            
        } catch (Exception $e) {
            yii::error('Error while fetching bills'.$e->getMessage());
        }
        return $dataProvider;
    }
}
