<?php

namespace common\models\extendedmodels;

use Yii;
use common\models\basemodels\Propertycategory;

/**
 * This is the model class for table "propertycategory".
 *
 * @property int $propertycategoryid
 * @property string $category
 * @property int $institutionid
 * @property int $propertygroupid
 * @property int $active
 * @property int $createdby
 * @property string $createddatetime
 * @property int $modifiedby
 * @property string $modifieddatetime
 *
 * @property Property[] $properties
 * @property Usercredentials $createdby0
 * @property Institution $institution
 * @property Usercredentials $modifiedby0
 * @property Propertygroup $propertygroup
 */

class ExtendedPropertycategory extends Propertycategory
{

}