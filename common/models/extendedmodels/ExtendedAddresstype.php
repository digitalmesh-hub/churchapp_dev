<?php

namespace common\models\extendedmodels;

use Yii;
use common\models\basemodels\Addresstype;

/**
 * This is the model class for table "addresstype".
 *
 * @property int $id
 * @property string $type
 *
 * @property Settings[] $settings
 */
class ExtendedAddresstype extends Addresstype
{
	/**
	 * To get address types
	 */
	public static function getAddressTypes()
	{
		try {
			$addressType = Yii::$app->db->createCommand('SELECT addresstype.* FROM addresstype ')->queryAll();
			return $addressType;
		} catch (Exception $e) {
			return false;
		}
	}
}

