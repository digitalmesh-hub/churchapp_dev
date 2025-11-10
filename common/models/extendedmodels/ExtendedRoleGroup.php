<?php

namespace common\models\extendedmodels;

use Yii;
use common\models\basemodels\RoleGroup;

class ExtendedRolegroup extends RoleGroup
{ 
	const ADMIN = 1;
	CONST MEMBER = 2;
	CONST STAFF = 3;   
}
