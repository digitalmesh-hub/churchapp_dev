<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\extendedmodels\ExtendedFamilyunit */

$this->title = 'Family Unit: {nameAttribute}';
$this->params['breadcrumbs'][] = ['label' => 'Extended Familyunits', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->familyunitid, 'url' => ['view', 'id' => $model->familyunitid]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="extended-familyunit-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
