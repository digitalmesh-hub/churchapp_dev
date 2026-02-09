<?php

use yii\helpers\Html;
use backend\assets\AppAsset;
use backend\components\widgets\FlashResult;


/* @var $this yii\web\View */
/* @var $model common\models\extendedmodels\ExtendedMember */

$this->title = $type;
?>

<div class="extended-member-create">
    <?= $this->render('_form', [
            'model'            =>  $model,
        	'dependantDetails'	       =>  $dependantDetails,
        //	'spouseModel'      =>  $spouseModel,
        	'settingsModel'    =>  $settingsModel,
        	'addressTypes'     =>  $addressTypes,
        	'relations'	 	   =>  $relations,
        	'bloodGroup' 	   =>  $bloodGroup,
        	'titlesArray'      =>  $titlesArray,	
        	'familyUnitsArray' =>  $familyUnitsArray,
			'zonesArray'       =>  $zonesArray,
    		'isMarried'	       => $isMarried,
    		'memberadditionalModal' => $memberadditionalModal,
    		'type' => $type,
    		'formType' => "create",
			'roleCategories' => $roleCategories,
			'batches' => $batches,
			'maxRMMembership' => $maxRMMembership || 0,
			'maxFMMembership' => $maxFMMembership || 0
    ]) ?>
</div>

