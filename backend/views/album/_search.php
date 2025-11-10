<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\extendedmodels\ExtendedAlbumSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="extended-album-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'albumid') ?>

    <?= $form->field($model, 'eventid') ?>

    <?= $form->field($model, 'albumname') ?>

    <?= $form->field($model, 'createdby') ?>

    <?= $form->field($model, 'createddatetime') ?>

    <?php // echo $form->field($model, 'modifiedby') ?>

    <?php // echo $form->field($model, 'modifieddatetime') ?>

    <?php // echo $form->field($model, 'ispublished') ?>

    <?php // echo $form->field($model, 'isalbumchanged') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
