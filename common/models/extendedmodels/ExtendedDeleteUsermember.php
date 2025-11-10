<?php

namespace common\models\extendedmodels;

use Yii;
use common\models\basemodels\DeleteUsermember;

/**
 * This is the model class for table "delete_usermember".
 *
 * @property int $id
 * @property int $userid
 * @property int $memberid
 * @property int $institutionid
 * @property string $usertype
 *
 * @property Member $member
 * @property Usercredentials $user
 * @property Institution $institution
 */
class ExtendedDeleteUsermember extends DeleteUsermember
{
	
}
