<?php

namespace common\models\extendedmodels;

use Yii;
use common\models\basemodels\InstitutionType;

/**
 * This is the model class for table "institutiontype".
 *
 * @property int $institutiontypeid
 * @property string $institutiontype
 *
 * @property Institution[] $institutions
 */
class ExtendedInstitutionType extends InstitutionType
{
    const INSTITUTION_TYPE_FAMILYUNIT = 1;
    const INSTITUTION_TYPE_CHURCH = 2;
    const INSTITUTION_TYPE_APARTMENT = 3;
}
