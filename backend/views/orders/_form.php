<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\extendedmodels\ExtendedOrders */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="extended-orders-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'memberid')->textInput() ?>

    <?= $form->field($model, 'membertype')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'institutionid')->textInput() ?>

    <?= $form->field($model, 'orderdate')->textInput() ?>

    <?= $form->field($model, 'ordertime')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'propertygroupid')->textInput() ?>

    <?= $form->field($model, 'orderstatus')->textInput() ?>

    <?= $form->field($model, 'note')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'createdby')->textInput() ?>

    <?= $form->field($model, 'createddatetime')->textInput() ?>

    <?= $form->field($model, 'modifiedby')->textInput() ?>

    <?= $form->field($model, 'modifieddatetime')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
