<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\searchmodels\EventSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="extended-event-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'institutionid') ?>

    <?= $form->field($model, 'notehead') ?>

    <?= $form->field($model, 'notebody') ?>

    <?= $form->field($model, 'activitydate') ?>

    <?php // echo $form->field($model, 'createddate') ?>

    <?php // echo $form->field($model, 'activatedon') ?>

    <?php // echo $form->field($model, 'noteurl') ?>

    <?php // echo $form->field($model, 'eventtype') ?>

    <?php // echo $form->field($model, 'createduser') ?>

    <?php // echo $form->field($model, 'venue') ?>

    <?php // echo $form->field($model, 'time') ?>

    <?php // echo $form->field($model, 'expirydate') ?>

    <?php // echo $form->field($model, 'rsvpavailable') ?>

    <?php // echo $form->field($model, 'modifiedby') ?>

    <?php // echo $form->field($model, 'modifieddatetime') ?>

    <?php // echo $form->field($model, 'iseventpublishable') ?>

    <?php // echo $form->field($model, 'familyunitid') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
