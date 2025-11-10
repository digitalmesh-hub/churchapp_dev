<?php

namespace common\models\extendedmodels;

use Yii;
use common\models\basemodels\Propertyimages;

/**
 * This is the model class for table "propertyimages".
 *
 * @property int $propertyimageid
 * @property string $imageurl
 * @property int $propertyid
 * @property int $imageorder
 *
 * @property Property $property
 */

class ExtendedPropertyimages extends Propertyimages
{

}