<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\extendedmodels\ExtendedMember */


$this->params['breadcrumbs'][] = ['label' => 'Extended Album', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="extended-album-create">


    <?= $this->render('_form', [
            'model'            =>  $model,
    		'eventsArray' => $eventsArray,
    		'imageModel' => $imageModel
    		
    ]) ?>

</div>