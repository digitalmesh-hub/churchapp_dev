<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\extendedmodels\ExtendedFamilyunit */

$this->title = 'Update Extended StaffDesignation: {nameAttribute}';
$this->params['breadcrumbs'][] = ['label' => 'Extended StaffDesignation', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->staffdesignationid, 'url' => ['view', 'id' => $model->staffdesignationid]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="extended-StaffDesignation-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
