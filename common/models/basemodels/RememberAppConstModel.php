<?php 
namespace common\models\basemodels;

use yii;
use yii\base\Model;

class RememberAppConstModel extends Model
{
    const RESTAURANT = 1; 
    const DEFAULT_FEEDBACK_TYPE_ID = 1;
	const DEFAULT_FEEDBACK_TYPE = "General";
	const DEFAULT_SORT_ORDER = 0;
}