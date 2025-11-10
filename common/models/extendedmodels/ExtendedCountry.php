<?php

namespace common\models\extendedmodels;

use Yii;
use common\models\basemodels\Country;

/**
 * This is the model class for table "country".
 *
 * @property int $countryid
 * @property string $CountryName
 * @property string $countrycode
 * @property string $telephonecode
 *
 * @property Affiliatedinstitution[] $affiliatedinstitutions
 * @property Institution[] $institutions
 * @property Member[] $members
 * @property Member[] $members0
 * @property Member[] $members1
 * @property Tempmember[] $tempmembers
 * @property Tempmember[] $tempmembers0
 * @property Tempmember[] $tempmembers1
 * @property Tempmember[] $tempmembers2
 * @property Tempmember[] $tempmembers3
 * @property Tempmember[] $tempmembers4
 */
class ExtendedCountry extends Country
{
    
}
