<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\extendedmodels\ExtendedMember */

$this->title = 'Member';
// $this->params['breadcrumbs'][] = ['label' => 'Extended Members', 'url' => ['index']];
// $this->params['breadcrumbs'][] = ['label' => $model->memberid, 'url' => ['view', 'id' => $model->memberid]];
// $this->params['breadcrumbs'][] = 'Update';
?>
<div class="extended-member-update">
    <?= $this->render('memberapprovelform', [
        'model'            =>  $model,
        		'dependantDetails'	       =>  $dependantDetails,
				"dynamicImageManageModel"=> $dynamicImageManageModel,
        		'settingsModel'    =>  $settingsModel,
        		'addressTypes'     =>  $addressTypes,
        		'relations'	 	   =>  $relations,
        		'bloodGroup' 	   =>  $bloodGroup,
        		'titlesArray'      =>  $titlesArray,
        		'familyUnitsArray' =>  $familyUnitsArray,
        		'isMarried'	       => $isMarried,
        		'memberadditionalModal' => $memberadditionalModal,
        		'type' => 'Member',
    			'formType' => "editmember",
    		'dependantIds'  => $dependantIds,
    ]) ?>

</div>
