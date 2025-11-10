<?php

namespace common\models\extendedmodels;

use Yii;
use common\models\basemodels\Appprivilege;

/**
 * This is the model class for table "appprivilege".
 *
 * @property string $PrivilegeID
 * @property string $Description
 * @property string $Code
 * @property string $sortorder
 *
 * @property Privilege $privilege
 */
class ExtendedAppprivilege extends Appprivilege
{
	
}
