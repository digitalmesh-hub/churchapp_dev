<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\extendedmodels\ExtendedOrders */

$this->title = $model->orderid;
$this->params['breadcrumbs'][] = ['label' => 'Extended Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="extended-orders-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->orderid], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->orderid], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'orderid',
            'memberid',
            'membertype',
            'institutionid',
            'orderdate',
            'ordertime',
            'propertygroupid',
            'orderstatus',
            'note',
            'createdby',
            'createddatetime',
            'modifiedby',
            'modifieddatetime',
        ],
    ]) ?>

</div>
