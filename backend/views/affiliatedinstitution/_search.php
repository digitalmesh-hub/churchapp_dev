<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\searchmodels\ExtendedAffiliatedinstitutionSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="extended-affiliatedinstitution-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'affiliatedinstitutionid') ?>

    <?= $form->field($model, 'institutionid') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'address1') ?>

    <?= $form->field($model, 'address2') ?>

    <?php // echo $form->field($model, 'district') ?>

    <?php // echo $form->field($model, 'state') ?>

    <?php // echo $form->field($model, 'CountryID') ?>

    <?php // echo $form->field($model, 'pin') ?>

    <?php // echo $form->field($model, 'phone1_countrycode') ?>

    <?php // echo $form->field($model, 'phone1_areacode') ?>

    <?php // echo $form->field($model, 'phone1') ?>

    <?php // echo $form->field($model, 'location') ?>

    <?php // echo $form->field($model, 'mobilenocountrycode') ?>

    <?php // echo $form->field($model, 'phone2') ?>

    <?php // echo $form->field($model, 'email') ?>

    <?php // echo $form->field($model, 'active') ?>

    <?php // echo $form->field($model, 'createduser') ?>

    <?php // echo $form->field($model, 'modifieduser') ?>

    <?php // echo $form->field($model, 'phone3_countrycode') ?>

    <?php // echo $form->field($model, 'phone3_areacode') ?>

    <?php // echo $form->field($model, 'phone3') ?>

    <?php // echo $form->field($model, 'url') ?>

    <?php // echo $form->field($model, 'institutionlogo') ?>

    <?php // echo $form->field($model, 'presidentname') ?>

    <?php // echo $form->field($model, 'presidentmobile') ?>

    <?php // echo $form->field($model, 'presidentmobile_countrycode') ?>

    <?php // echo $form->field($model, 'secretaryname') ?>

    <?php // echo $form->field($model, 'secretarymobile') ?>

    <?php // echo $form->field($model, 'secretarymobile_countrycode') ?>

    <?php // echo $form->field($model, 'meetingvenue') ?>

    <?php // echo $form->field($model, 'meetingday') ?>

    <?php // echo $form->field($model, 'meetingtime') ?>

    <?php // echo $form->field($model, 'remarks') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
