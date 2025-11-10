<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\searchmodels\ExtendedCountrySearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="extended-institution-search">

    <?php $form = ActiveForm::begin([
        'action' => ['list-institution'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'name') ?>
    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>

</div>
