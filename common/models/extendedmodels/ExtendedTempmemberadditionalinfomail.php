<?php

namespace common\models\extendedmodels;

use Yii;
use common\models\basemodels\Tempmemberadditionalinfomail;
/**
 * This is the model class for table "tempmemberadditionalinfomail".
 *
 * @property int $id
 * @property int $memberid
 * @property string $temptagcloud
 * @property int $isapproved
 *
 * @property Member $member
 */
class ExtendedTempmemberadditionalinfomail extends Tempmemberadditionalinfomail
{
   
}
