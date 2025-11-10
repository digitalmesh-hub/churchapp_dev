<?php

namespace common\models\basemodels;
use common\models\extendedmodels\ExtendedTitle;

use Yii;

/**
 * This is the model class for table "new_registration".
 *
 *
 * @property NewMemberRegistration $newMemberRegistration
 */
class NewMemberRegistration extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'new_registration';
    }


	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getMembertitle0()
	{
	    return $this->hasOne(ExtendedTitle::className(), ['TitleId' => 'title']);
	}

}
