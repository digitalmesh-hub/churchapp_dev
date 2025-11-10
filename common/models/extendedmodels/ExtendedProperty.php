<?php

namespace common\models\extendedmodels;

use Yii;
use common\models\basemodels\Property;

/**
 * This is the model class for table "property".
 *
 * @property int $propertyid
 * @property string $property
 * @property string $description
 * @property int $propertycategoryid
 * @property string $price
 * @property string $thumbnailimage
 * @property int $institutionid
 * @property int $active
 *
 * @property Cart[] $carts
 * @property Orderitems[] $orderitems
 * @property Institution $institution
 * @property Propertycategory $propertycategory
 * @property Propertyimages[] $propertyimages
 */

class ExtendedProperty extends Property
{
	/**
     * @inheritdoc
     */
    public function rules()
    {
        return \yii\helpers\Arrayhelper::merge([
	        [['property'], 'required','message'=>'Product Name cannot be blank.'],
            [['propertycategoryid'], 'required','message'=>'Category cannot be blank.'],
            ['price', 'validatePrice'],
            [['price'], 'number', 'max' => 999999.99],
            [['institutionid'], 'required'],
	    ],parent::rules());
    }

    /**
     * Validate price
     */
    public function validatePrice($attribute, $params)
        {
            if ($this->$attribute <= 0){
                $this->addError($attribute, 'Price should be greater than 0');
            }
        }
}