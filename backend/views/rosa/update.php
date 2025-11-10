<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\extendedmodels\ExtendedRosa */

$this->title = 'Update Extended Rosa: {nameAttribute}';
$this->params['breadcrumbs'][] = ['label' => 'Extended Rosas', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->rosaid]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="extended-rosa-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
