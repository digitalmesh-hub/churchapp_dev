<?php
namespace common\models\custom;

use Yii;
use yii\base\Model;
use common\models\extendedmodels\ExtendedBevcoSlots;
use common\models\extendedmodels\ExtendedBevcoOrder;
use common\models\extendedmodels\ExtendedBevcoProducts;
use common\models\extendedmodels\ExtendedBevcoOrderItem;
use common\models\extendedmodels\ExtendedBevcoSettings;
use common\helpers\Utility;

/**
 */
class BeverageBookingForm extends Model
{
    public $institution_id;
    public $member_id;
    public $member_name;
    public $slot;
    public $order_date;
    
    public $settings;
    public $items;
    public $user;

    public function init()
    {
        parent::init();
        $this->user =  yii::$app->user->identity;
        $this->institution_id = $this->user->institutionid;
    }

    public function attributeLabels()
    {
        return [
            'member_id' => 'Member',
            'product_id' => 'Product',
            'order_date' => 'Order Date',
            'member_name' => 'Member'
        ];
    }
    
    public function rules()
    {
        return [
            [['order_date','member_id','slot','member_name'], 'required'],
            [['order_date'], 'safe'],
        ];
    }

    public function getMembers()
    {
        $response = [];
        try{
            $response = Yii::$app->db->createCommand("CALL get_members_for_bevco_booking(:institutionId)")
                ->bindValue(':institutionId', $this->institution_id)->queryAll();
        } catch(Exception $e){
            yii::error('Error while fetching data for auto suggest in getMemberForEventAutoSuggest');
        }
        return $response;
    }

    public function completeBooking()
    {
        $slots = $this->getSlots();

        $custom_data = $this->settings->_customDataObject;
        if(empty($slots)) {
            $slot_models   = [];
            $start_time = strtotimeNew($custom_data['start_time']); 
            $end_time = strtotimeNew($custom_data['end_time']);
            $duration = (int)$custom_data['slot_duration'];
            $slot_no = 1;
            
            while ($start_time < $end_time) {
                $t1 = $start_time;
                $start_time = strtotimeNew('+'.$duration.' '.'minutes', $t1);
                $slot_models[] = new ExtendedBevcoSlots([
                        'slot_date' => date('Y-m-d H:i:s', strtotimeNew($this->order_date.'00:00:00')),
                        'slot_number' => $slot_no,
                        'institution_id' => $this->institution_id,
                        'start_time' => date('H:i:s', $t1),
                        'end_time' => date('H:i:s', $start_time),
                        'settings' => json_encode($custom_data)
                    ]
                );
                if(strtotimeNew('+'.$duration.' '.'minutes', $start_time) > $end_time) {
                   $end_time = $start_time;
                }
                $slot_no++;
            }
            if(!empty($slot_models)) {
                foreach ($slot_models as $slot_model) {
                    $slot_model->save(false);
                }
            }
        }

        $slot = Utility::getValue($this->getSlots(), $this->slot);

        if(!$slot) 
            return ['success' => false, 'errors' => 'Invalid Slot'];

        return $slot->lock(function () use ($slot) {
                if($slot->orderCount >= $slot->_settings['sales_per_slot']) {
                    return ['success' => false, 
                        'errors' => ['Maximum number of sales for the selected slot exceeded. Please choose another slot.']];
                }
                $response = $this->canBookBevco($slot);
                if(!$response['valid']) {
                    return ['success' => false, 'errors' => $response['errors']];
                }
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    $order = new ExtendedBevcoOrder;
                    $order->member_id = $this->member_id;
                    $order->institution_id = $this->institution_id;
                    $order->slot_id = $slot->id;
                    $order->order_date = date('Y-m-d', strtotimeNew($this->order_date));
                    $order->status = ExtendedBevcoOrder::STATUS_PLACED;
                    $order->created_by = $this->user->id;

                    if($flag = $order->save(false)) {
                        foreach ($this->items as $item) {
                            $order_item =  new ExtendedBevcoOrderItem;
                            $order_item->order_id = $order->id;
                            $order_item->product_id = $item->product_id;
                            $order_item->quantity = $item->quantity;
                            if (! ($flag = $order_item->save(false))) {
                                $transaction->rollBack();
                                break;
                            }
                        }
                    }
                    if ($flag) {
                        $transaction->commit();
                        return ['success' => true];
                    }
                } catch (Exception $e) {
                    $transaction->rollBack();
                }
                return ['success' => false, 'errors' => []];
        },
        null,
        function () {
            return ['success' => false, 'errors' => "Looks like the chosen slot is locked by then.Either please wait a moment and try again or unlock the slot forcefully if you're sure none other is using it momentarily."];
        });

    }

    public function getSlots()
    {
        return ExtendedBevcoSlots::find()->where(['institution_id' => $this->institution_id])->andWhere([
               'between', 'slot_date', date('Y-m-d H:i:s', strtotimeNew($this->order_date.'00:00:00')),
                date('Y-m-d H:i:s',strtotimeNew($this->order_date.'23:59:59'))
        ])->indexBy('slot_number')->all();

    }

    public function canBookBevco($slot)
    {
        
        //check is a valid slot
        $orderDate = strtotimeNew($this->order_date);
        $dt = new \DateTime('', new \DateTimeZone('Asia/Kolkata'));
        $now = $dt->getTimestamp() + $dt->getOffset();
        $rt = $dt->modify('today');
        $tr = $rt->getTimestamp() + $rt->getOffset();
        $datediff = abs(round(($tr - $orderDate) / (60 * 60 * 24)));
        $errors = [];
        $valid = true;
        $lastOrderQuantity = [];

        $x = ($datediff > 0) ? strtotimeNew('+'.$datediff.'days', strtotimeNew($slot->end_time)) : strtotimeNew($slot->end_time);
        if($x < $now) {
            return ['errors' => ['Invalid Slot, selected slot might have been expired.'], 'valid' => false];
        }


        if($slot->_settings['interval_unit'] == ExtendedBevcoSettings::INTERVAL_UNIT_WEEK) {
            $q1 =  "SELECT bevco_order.id,bevco_order.order_date,bevco_order_items.product_id,bevco_order_items.quantity,
                    bevco_category.name as category_name,bevco_category.id as category_id
                    FROM  bevco_order
                    INNER JOIN bevco_order_items
                    ON bevco_order_items.order_id = bevco_order.id
                    INNER JOIN bevco_products
                    ON bevco_products.id = bevco_order_items.product_id
                    INNER JOIN bevco_category
                    ON bevco_products.category_id = bevco_category.id
                    WHERE YEARWEEK(`order_date`) = YEARWEEK(:order_date)
                    and YEAR(`order_date`) = YEAR(:order_date)
                    and member_id = :member_id
                    and bevco_order.institution_id = :institution_id
                    and status in (".implode(",",[ExtendedBevcoOrder::STATUS_PLACED, ExtendedBevcoOrder::STATUS_COMPLETED]).")
                        ";
        } else if($slot->_settings['interval_unit'] == ExtendedBevcoSettings::INTERVAL_UNIT_MONTH) {
            $q1 =  "SELECT bevco_order.id,bevco_order.order_date,bevco_order_items.product_id,bevco_order_items.quantity,
                    bevco_category.name as category_name,bevco_category.id as category_id
                    FROM  bevco_order
                    INNER JOIN bevco_order_items
                    ON bevco_order_items.order_id = bevco_order.id
                    INNER JOIN bevco_products
                    ON bevco_products.id = bevco_order_items.product_id
                    INNER JOIN bevco_category
                    ON bevco_products.category_id = bevco_category.id
                    WHERE MONTH(`order_date`) = MONTH(:order_date)
                    and YEAR(`order_date`) = YEAR(:order_date)
                    and member_id = :member_id
                    and bevco_order.institution_id = :institution_id
                    and status in (".implode(",",[ExtendedBevcoOrder::STATUS_PLACED, ExtendedBevcoOrder::STATUS_COMPLETED]).")
                ";
        }
   
        $orders = yii::$app->db->createCommand($q1)->bindValues([
            'member_id' => $this->member_id,
            'institution_id' => $this->institution_id,
            'order_date' => date('Y-m-d', strtotimeNew($this->order_date))
        ])->queryAll();
                
        if(!empty($orders)) {
            foreach($orders as $val) {
                $lastOrderQuantity[$val['category_id']][] = $val['quantity'];
            }
        }
        
        if(!empty($this->items)) {
            $oq = [];
            foreach ($this->items as $pk => $pv) {
                $product = ExtendedBevcoProducts::findOne($pv->product_id);
                $category = $product->category;
                $oq[$category->id] = isset($oq[$category->id])? $oq[$category->id] + $pv->quantity: $pv->quantity;
                $alreadyOrdered = (isset($lastOrderQuantity[$category->id]))? array_sum($lastOrderQuantity[$category->id]):0;
                $bal = $category->_customDataObject['maximum_allowed_per_interval'] - $alreadyOrdered;
                if ($oq[$category->id] > $category->_customDataObject['maximum_allowed_per_interval']) {
                    $valid = false;
                    $errors[] = 'Allowed'.' '.$category->name .' exceeded for this member';
                } else if($alreadyOrdered == $category->_customDataObject['maximum_allowed_per_interval'] ) {
                    $valid = false;
                    $errors[] = "This member already holds booking for {$category->name} this week";
                } else if($oq[$category->id] > $bal) {
                    $valid = false;
                    $errors[] = "This member can make booking for only {$bal} {$category->name}. This member already holds booking for {$alreadyOrdered} {$category->name} this week";
                } 
                
            }
        }
        return ['errors' => $errors, 'valid' => $valid];
    }
}
   