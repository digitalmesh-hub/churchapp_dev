<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\extendedmodels\ExtendedOrders */

$this->title = 'Create Extended Orders';
$this->params['breadcrumbs'][] = ['label' => 'Extended Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="extended-orders-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
