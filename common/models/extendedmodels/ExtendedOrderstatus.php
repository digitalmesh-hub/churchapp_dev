<?php

namespace common\models\extendedmodels;

use Yii;
use common\models\basemodels\Orderstatus;

/**
 * This is the model class for table "orderstatus".
 *
 * @property int $id
 * @property string $status
 * @property int $statusid
 */
class ExtendedOrderstatus extends Orderstatus
{
	const  PLACED = 0;
	const  CONFIRMED = 1;
	const  READY = 2;
	const  HANDOVER = 3;
	const  REJECTED = 4;
	const  CANCELLED = 5;
	const  REMOVE_FROM_MY_ORDER = 6;
	
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'orderstatus';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status'], 'required'],
            [['statusid'], 'integer'],
            [['status'], 'string', 'max' => 45],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'status' => 'Status',
            'statusid' => 'Statusid',
        ];
    }
    /**
     * To get all order status
     * @return boolean
     */
    public static function getAllOrderstatus()
    {
    	try {
    		$orderStatus = Yii::$app->db->createCommand("select * from orderstatus")
    					->queryAll();
    		return $orderStatus;
    		
    	} catch (Exception $e) {
    		return [];
    	}
    }
}
