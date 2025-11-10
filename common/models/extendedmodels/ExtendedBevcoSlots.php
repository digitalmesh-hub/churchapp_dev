<?php

namespace common\models\extendedmodels;

use Yii;
use common\helpers\Utility;
use common\models\basemodels\BevcoSlots;
use ArrayObject;

class ExtendedBevcoSlots extends BevcoSlots
{   
    const LOCK_NONE = 0;
    const LOCK_UPDATING = 1;
    protected $currentId;
    public $_settings;

    public function init()
    {
        parent::init();
        
        if(!$this->currentId)
            $this->currentId = $this->currentId();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrders()
    {
        return $this->hasMany(ExtendedBevcoOrder::className(), ['slot_id' => 'id'])
         	->andOnCondition(['in', 'status', [ExtendedBevcoOrder::STATUS_PLACED, ExtendedBevcoOrder::STATUS_COMPLETED]]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAllOrders()
    {
        return $this->hasMany(ExtendedBevcoOrder::className(), ['slot_id' => 'id']);
    }

    public function getOrderCount()
    {
    	return count($this->orders);
    }

    public function lock($lockedCallable = null,$unlockCallable = null,$alreadyLockedCallable = null,$exceptionCallable = null)
    {
        if (!$this->hasAttribute('lock'))
            return false;
        
        $count = self::updateAll(['lock' => $this->currentId], [
            'and', '`id` = ' . $this->id, 
            ['or', '`lock` = ' . self::LOCK_NONE, '`lock` = ' . $this->currentId]
        ]);
        
        $isLocked = $count == 1;
        if (!$isLocked) {
            $isLocked = self::findOne(['id' => $this->id,'lock' => $this->currentId]) != null;
        }
        
        if (!$isLocked) {
            $result = false;
            if ($alreadyLockedCallable) {
                if (is_callable($alreadyLockedCallable))
                    $result = call_user_func($alreadyLockedCallable, $this);
                else
                    $result = $alreadyLockedCallable;
            }

            return $result;
        }

        if (!$lockedCallable) {
            return $isLocked;
        }

        $return = null;
        try {
            $return = call_user_func($lockedCallable,$this);
            return $return;
        } catch (\Exception $ex) {
            if ($exceptionCallable)
                return call_user_func($exceptionCallable,$ex,$this);

            throw $ex;
        } finally {

            if ($unlockCallable)
                call_user_func($unlockCallable,$return,$this);

           $this->unlock();
        }
    }

    public function currentId()
    {
        return rand(1000, 2000000000);
    }

    public function unlock($force = false)
    {
        if (!$this->hasAttribute('lock'))
            return false;

        $criteria = ['id' => $this->id];
        if (!$force)
            $criteria['lock'] = $this->currentId;
        $unlocked = self::updateAll(['lock' => self::LOCK_NONE],$criteria) == 1;
        
        return $unlocked;

    }

      /**
     * @inheritdoc
     */
    public function afterFind()
    {
        parent::afterFind();
        if ($this->hasAttribute('settings')) {
            $startingData = $this->settings ? json_decode($this->settings,JSON_OBJECT_AS_ARRAY) : [];
            $this->_settings = new ArrayObject($startingData);
        }
    }

}
