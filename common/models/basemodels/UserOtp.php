<?php

namespace common\models\basemodels;

use Yii;

/**
 * This is the model class for table "user_otp".
 *
 *
 * @property UserOtp $userOtp
 */
class UserOtp extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_otp';
    }

}
