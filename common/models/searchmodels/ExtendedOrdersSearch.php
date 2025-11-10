<?php

namespace common\models\searchmodels;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\extendedmodels\ExtendedOrders;

/**
 * ExtendedOrdersSearch represents the model behind the search form of `common\models\extendedmodels\ExtendedOrders`.
 */
class ExtendedOrdersSearch extends ExtendedOrders
{
    /**
     * @inheritdoc
     */

    public $start_date;
    public $end_date;
  
    public function rules()
    {
        return [
            [['institutionid', 'propertygroupid','orderstatus'], 'integer'],
             [['start_date', 'end_date'], 'required'],
            [['start_date','end_date'], 'date', 'format' => 'php:d-M-Y'],
            ['end_date', 'validateExpiryDate'],
        ];
    }

    public function validateExpiryDate($attribute, $params, $validator)
    {   
        if (strtotimeNew($this->end_date.'23:59:00') < strtotimeNew($this->start_date)) {
            $this->addError('end_date', 'End Date should be greater or equal to Start Date');
        } 
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
        $dataArray = [];
    	$startDate = (isset($this->start_date)) ? gmdate('Y-m-d H:i:s',strtotimeNew($this->start_date)):gmdate('Y-m-d H:i:s');
    	$endDate = (isset($this->end_date)) ? gmdate('Y-m-d H:i:s',strtotimeNew($this->end_date)):gmdate('Y-m-d H:i:s');
    
        $this->load($params);
        if (!$this->validate()) {
            return $dataArray;
        } 
        switch ($this->orderstatus) {
            case '0':
                $status = 0;
                break;
            case '1':
                $status = 1;
                break;
            case '2':
                $status = 2;
                break;
            case '3':
                $status = 3;
                break;
            case '4':
                $status = 4;
                break;
            case '5':
                $status = 5;
                break;
            case '6':
                $status = 6;
                break;
            default:
                $status = null;
                break;
        }
    	//return sp result
    	$data = Yii::$app->db->createCommand("
    			CALL getallorders(:institutionid,:startdate,:enddate,:propertygroupid,:orderstatus) ")
    				->bindValue(':institutionid', $this->institutionid)
    				->bindValue(':startdate',$startDate)
    				->bindValue(':enddate', $endDate)
    				->bindValue(':propertygroupid', $this->propertygroupid)
    				->bindValue(':orderstatus', $status)
    				->queryAll(); 
    	
        foreach($data as $val) {
            $val['orderdate'] = date("Y-m-d",strtotimeNew($val['orderdate']));
            $dataArray[$val['orderdate']][] = $val;
        }
    	return $dataArray;
    }
  
}
