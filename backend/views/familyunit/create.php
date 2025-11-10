<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\extendedmodels\ExtendedFamilyunit */

$this->title = 'Family Unit';
$this->params['breadcrumbs'][] = ['label' => 'Extended Familyunits', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="col-md-12 col-sm-12 contentbg">
                <div class="col-md-12 col-sm-12 Mtopbot20">

<div class="extended-familyunit-create">

    

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
