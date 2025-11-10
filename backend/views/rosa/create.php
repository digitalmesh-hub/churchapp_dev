<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\extendedmodels\ExtendedRosa */

$this->title = 'Rosa';
$this->params['breadcrumbs'][] = ['label' => 'Extended Rosas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="extended-rosa-create">


    <?= $this->render('_form', [
        'model' => $model,
    	'years' => $years,	
    ]) ?>

</div>
