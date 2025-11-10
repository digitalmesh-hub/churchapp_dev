<?php

namespace common\models\extendedmodels;

use Yii;
use common\models\basemodels\Contactfilters;

/**
 * This is the model class for table "contactfilters".
 *
 * @property int $contactfilterid
 * @property string $description
 * @property int $filteroptiontypeid
 *
 * @property Institutioncontactfilters[] $institutioncontactfilters
 */
class ExtendedContactfilters extends Contactfilters
{
	const CONTACT_FILTER_FAMILYUNIT = 0;
	const CONTACT_FILTER_TAGSEARCH = 1;
	const CONTACT_FILTER_BLOODGROUP = 2;
	const CONTACT_FILTER_BATCH = 3;
}
