<?php

namespace common\models\extendedmodels;

use Yii;
use common\models\basemodels\Cart;

/**
 * This is the model class for table "cart".
 *
 * @property int $cartid
 * @property int $propertyid
 * @property string $price
 * @property int $quantity
 * @property int $memberid
 * @property int $institutionid
 * @property int $propertygroupid
 * @property int $createdby
 * @property string $createddatetime
 * @property int $modifiedby
 * @property string $modifieddatetime
 *
 * @property Usercredentials $createdby0
 * @property Institution $institution
 * @property Member $member
 * @property Usercredentials $modifiedby0
 * @property Property $property
 * @property Propertygroup $propertygroup
 */
class ExtendedCart extends Cart
{
	
}
