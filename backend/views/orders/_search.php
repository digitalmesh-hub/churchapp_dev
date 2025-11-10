<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\extendedmodels\ExtendedOrdersSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="extended-orders-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'orderid') ?>

    <?= $form->field($model, 'memberid') ?>

    <?= $form->field($model, 'membertype') ?>

    <?= $form->field($model, 'institutionid') ?>

    <?= $form->field($model, 'orderdate') ?>

    <?php // echo $form->field($model, 'ordertime') ?>

    <?php // echo $form->field($model, 'propertygroupid') ?>

    <?php // echo $form->field($model, 'orderstatus') ?>

    <?php // echo $form->field($model, 'note') ?>

    <?php // echo $form->field($model, 'createdby') ?>

    <?php // echo $form->field($model, 'createddatetime') ?>

    <?php // echo $form->field($model, 'modifiedby') ?>

    <?php // echo $form->field($model, 'modifieddatetime') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
