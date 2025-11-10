<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\extendedmodels\ExtendedDependant */
$this->title = 'Create Dependent';
?>
<div class="extended-dependant-create">
    <?= $this->render('dependantform', [
        'dependantModel' => $dependantModel,
    	'spouseModel'	=> $spouseModel,
    	'titlesArray' => $titlesArray,
    	'relations'	 	   =>  $relations,
    	'isMarried'=>$isMarried,
    	'memberId'       =>  $memberId,
    ]) ?>
</div>
