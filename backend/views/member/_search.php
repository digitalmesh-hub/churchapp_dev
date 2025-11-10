<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\searchmodels\ExtendedMemberSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="extended-member-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'memberid') ?>

    <?= $form->field($model, 'institutionid') ?>

    <?= $form->field($model, 'memberno') ?>

    <?= $form->field($model, 'membershiptype') ?>

    <?= $form->field($model, 'membersince') ?>

    <?php // echo $form->field($model, 'firstName') ?>

    <?php // echo $form->field($model, 'middleName') ?>

    <?php // echo $form->field($model, 'lastName') ?>

    <?php // echo $form->field($model, 'business_address1') ?>

    <?php // echo $form->field($model, 'business_address2') ?>

    <?php // echo $form->field($model, 'business_address3') ?>

    <?php // echo $form->field($model, 'business_district') ?>

    <?php // echo $form->field($model, 'business_state') ?>

    <?php // echo $form->field($model, 'business_pincode') ?>

    <?php // echo $form->field($model, 'member_dob') ?>

    <?php // echo $form->field($model, 'member_mobile1') ?>

    <?php // echo $form->field($model, 'member_mobile2') ?>

    <?php // echo $form->field($model, 'member_musiness_Phone1') ?>

    <?php // echo $form->field($model, 'member_business_Phone2') ?>

    <?php // echo $form->field($model, 'member_residence_Phone1') ?>

    <?php // echo $form->field($model, 'member_residence_Phone2') ?>

    <?php // echo $form->field($model, 'member_email') ?>

    <?php // echo $form->field($model, 'spouse_firstName') ?>

    <?php // echo $form->field($model, 'spouse_middleName') ?>

    <?php // echo $form->field($model, 'spouse_lastName') ?>

    <?php // echo $form->field($model, 'spouse_dob') ?>

    <?php // echo $form->field($model, 'dom') ?>

    <?php // echo $form->field($model, 'spouse_mobile1') ?>

    <?php // echo $form->field($model, 'spouse_mobile2') ?>

    <?php // echo $form->field($model, 'spouse_email') ?>

    <?php // echo $form->field($model, 'residence_address1') ?>

    <?php // echo $form->field($model, 'residence_address2') ?>

    <?php // echo $form->field($model, 'residence_address3') ?>

    <?php // echo $form->field($model, 'residence_district') ?>

    <?php // echo $form->field($model, 'residence_state') ?>

    <?php // echo $form->field($model, 'residence_pincode') ?>

    <?php // echo $form->field($model, 'member_pic') ?>

    <?php // echo $form->field($model, 'spouse_pic') ?>

    <?php // echo $form->field($model, 'app_reg_member') ?>

    <?php // echo $form->field($model, 'app_reg_spouse') ?>

    <?php // echo $form->field($model, 'active') ?>

    <?php // echo $form->field($model, 'businessemail') ?>

    <?php // echo $form->field($model, 'membertitle') ?>

    <?php // echo $form->field($model, 'spousetitle') ?>

    <?php // echo $form->field($model, 'membernickname') ?>

    <?php // echo $form->field($model, 'spousenickname') ?>

    <?php // echo $form->field($model, 'lastupdated') ?>

    <?php // echo $form->field($model, 'createddate') ?>

    <?php // echo $form->field($model, 'homechurch') ?>

    <?php // echo $form->field($model, 'occupation') ?>

    <?php // echo $form->field($model, 'spouseoccupation') ?>

    <?php // echo $form->field($model, 'countrycode') ?>

    <?php // echo $form->field($model, 'areacode') ?>

    <?php // echo $form->field($model, 'member_mobile1_countrycode') ?>

    <?php // echo $form->field($model, 'spouse_mobile1_countrycode') ?>

    <?php // echo $form->field($model, 'member_business_phone1_countrycode') ?>

    <?php // echo $form->field($model, 'member_business_phone1_areacode') ?>

    <?php // echo $form->field($model, 'member_business_phone2_countrycode') ?>

    <?php // echo $form->field($model, 'memberImageThumbnail') ?>

    <?php // echo $form->field($model, 'spouseImageThumbnail') ?>

    <?php // echo $form->field($model, 'membertype') ?>

    <?php // echo $form->field($model, 'staffdesignation') ?>

    <?php // echo $form->field($model, 'member_business_Phone3') ?>

    <?php // echo $form->field($model, 'member_business_phone3_countrycode') ?>

    <?php // echo $form->field($model, 'member_business_phone3_areacode') ?>

    <?php // echo $form->field($model, 'newmembernum') ?>

    <?php // echo $form->field($model, 'familyunitid') ?>

    <?php // echo $form->field($model, 'memberbloodgroup') ?>

    <?php // echo $form->field($model, 'spousebloodgroup') ?>

    <?php // echo $form->field($model, 'member_residence_phone1_areacode') ?>

    <?php // echo $form->field($model, 'member_residence_Phone1_countrycode') ?>

    <?php // echo $form->field($model, 'member_residence_phone2_areacode') ?>

    <?php // echo $form->field($model, 'member_residence_Phone2_countrycode') ?>

    <?php // echo $form->field($model, 'companyname') ?>

    <?php // echo $form->field($model, 'member_business_phone2_areacode') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
