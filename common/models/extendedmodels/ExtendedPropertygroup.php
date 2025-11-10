<?php

namespace common\models\extendedmodels;

use Yii;
use common\models\basemodels\Propertygroup;

/**
 * This is the model class for table "propertygroup".
 *
 * @property int $propertygroupid
 * @property string $description
 *
 * @property Cart[] $carts
 * @property Orders[] $orders
 * @property Propertycategory[] $propertycategories
 * @property Tax[] $taxes
 */
class ExtendedPropertygroup extends Propertygroup
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'propertygroup';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
				[['description'], 'required'],
				[['description'], 'string', 'max' => 100],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
				'propertygroupid' => 'Propertygroupid',
				'description' => 'Description',
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getCarts()
	{
		return $this->hasMany(Cart::className(), ['propertygroupid' => 'propertygroupid']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getOrders()
	{
		return $this->hasMany(Orders::className(), ['propertygroupid' => 'propertygroupid']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getPropertycategories()
	{
		return $this->hasMany(Propertycategory::className(), ['propertygroupid' => 'propertygroupid']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getTaxes()
	{
		return $this->hasMany(Tax::className(), ['propertygroupid' => 'propertygroupid']);
	}
}

