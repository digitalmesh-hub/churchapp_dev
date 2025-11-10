<?php

namespace common\models\basemodels;

use Yii;

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
class Property extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'property';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['property', 'propertycategoryid', 'price', 'institutionid'], 'required'],
            [['propertycategoryid', 'institutionid'], 'integer'],
            [['price'], 'number'],
            [['property', 'thumbnailimage'], 'string', 'max' => 100],
            [['description'], 'string', 'max' => 250],
            [['active'], 'string', 'max' => 4],
            [['institutionid'], 'exist', 'skipOnError' => true, 'targetClass' => Institution::className(), 'targetAttribute' => ['institutionid' => 'id']],
            [['propertycategoryid'], 'exist', 'skipOnError' => true, 'targetClass' => Propertycategory::className(), 'targetAttribute' => ['propertycategoryid' => 'propertycategoryid']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'propertyid' => 'Propertyid',
            'property' => 'Property',
            'description' => 'Description',
            'propertycategoryid' => 'Propertycategoryid',
            'price' => 'Price',
            'thumbnailimage' => 'Thumbnailimage',
            'institutionid' => 'Institutionid',
            'active' => 'Active',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCarts()
    {
        return $this->hasMany(Cart::className(), ['propertyid' => 'propertyid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrderitems()
    {
        return $this->hasMany(Orderitems::className(), ['propertyid' => 'propertyid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInstitution()
    {
        return $this->hasOne(Institution::className(), ['id' => 'institutionid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPropertycategory()
    {
        return $this->hasOne(Propertycategory::className(), ['propertycategoryid' => 'propertycategoryid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPropertyimages()
    {
        return $this->hasMany(Propertyimages::className(), ['propertyid' => 'propertyid']);
    }
}
