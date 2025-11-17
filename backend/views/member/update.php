<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\extendedmodels\ExtendedMember */

$this->title = $type;
// $this->params['breadcrumbs'][] = ['label' => 'Extended Members', 'url' => ['index']];
// $this->params['breadcrumbs'][] = ['label' => $model->memberid, 'url' => ['view', 'id' => $model->memberid]];
// $this->params['breadcrumbs'][] = 'Update';
?>
<div class="extended-member-update">
    <?= $this->render('_form', [
        'model'            =>  $model,
        		'dependantDetails'	       =>  $dependantDetails,
        		//'spouseModel'      =>  $spouseModel,
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
    			'formType' => "update",
                'roleCategories' => $roleCategories,
                'selectedSpouseCat' => $selectedSpouseCat,
				'selectedMemberCat' => $selectedMemberCat,
				'batches' => $batches
    ]) ?>

</div>
