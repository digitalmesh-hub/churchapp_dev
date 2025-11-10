<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\extendedmodels\ExtendedMember */

$this->title = $model->memberid;
$this->params['breadcrumbs'][] = ['label' => 'Extended Members', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="extended-member-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->memberid], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->memberid], [
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
            'memberid',
            'institutionid',
            'memberno',
            'membershiptype',
            'membersince',
            'firstName',
            'middleName',
            'lastName',
            'business_address1',
            'business_address2',
            'business_address3',
            'business_district',
            'business_state',
            'business_pincode',
            'member_dob',
            'member_mobile1',
            'member_mobile2',
            'member_musiness_Phone1',
            'member_business_Phone2',
            'member_residence_Phone1',
            'member_residence_Phone2',
            'member_email:email',
            'spouse_firstName',
            'spouse_middleName',
            'spouse_lastName',
            'spouse_dob',
            'dom',
            'spouse_mobile1',
            'spouse_mobile2',
            'spouse_email:email',
            'residence_address1',
            'residence_address2',
            'residence_address3',
            'residence_district',
            'residence_state',
            'residence_pincode',
            'member_pic',
            'spouse_pic',
            'app_reg_member',
            'app_reg_spouse',
            'active',
            'businessemail:email',
            'membertitle',
            'spousetitle',
            'membernickname',
            'spousenickname',
            'lastupdated',
            'createddate',
            'homechurch',
            'occupation',
            'spouseoccupation',
            'countrycode',
            'areacode',
            'member_mobile1_countrycode',
            'spouse_mobile1_countrycode',
            'member_business_phone1_countrycode',
            'member_business_phone1_areacode',
            'member_business_phone2_countrycode',
            'memberImageThumbnail',
            'spouseImageThumbnail',
            'membertype',
            'staffdesignation',
            'member_business_Phone3',
            'member_business_phone3_countrycode',
            'member_business_phone3_areacode',
            'newmembernum',
            'familyunitid',
            'memberbloodgroup',
            'spousebloodgroup',
            'member_residence_phone1_areacode',
            'member_residence_Phone1_countrycode',
            'member_residence_phone2_areacode',
            'member_residence_Phone2_countrycode',
            'companyname',
            'member_business_phone2_areacode',
        ],
    ]) ?>

</div>
