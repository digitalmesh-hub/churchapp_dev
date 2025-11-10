<?php

namespace common\models\extendedmodels;

use Yii;
use common\models\basemodels\DeleteDependant;

/**
 * This is the model class for table "delete_dependant".
 *
 * @property int $id
 * @property int $memberid
 * @property string $dependantname
 * @property string $dob
 * @property string $relation
 *
 * @property Member $member
 */
class ExtendedDeleteDependant extends DeleteDependant
{
	
}
