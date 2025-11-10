<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\extendedmodels\ExtendedOrders */

$this->title = 'Update Extended Orders: {nameAttribute}';
$this->params['breadcrumbs'][] = ['label' => 'Extended Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->orderid, 'url' => ['view', 'id' => $model->orderid]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="extended-orders-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
