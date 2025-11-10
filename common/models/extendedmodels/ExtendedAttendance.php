<?php

namespace common\models\extendedmodels;

use Yii;
use common\models\basemodels\Attendance;

/**
 * This is the model class for table "attendance".
 *
 * @property int $attendanceid
 * @property int $memberid
 * @property int $attendance
 * @property string $attandancedate
 * @property int $institutionid
 * @property int $createdby
 * @property string $createddatetime
 * @property int $modifiedby
 * @property string $modifieddatetime
 *
 * @property Usercredentials $createdby0
 * @property Institution $institution
 * @property Member $member
 * @property Usercredentials $modifiedby0
 */
class ExtendedAttendance extends Attendance
{
	
}
