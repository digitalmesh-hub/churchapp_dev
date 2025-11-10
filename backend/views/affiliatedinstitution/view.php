<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use backend\assets\AppAsset;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $model common\models\extendedmodels\ExtendedAffiliatedinstitution */

$assetName = AppAsset::register ( $this );
$this->registerJsFile ( $assetName->baseUrl . '/theme/js/Remember.affiliatedInstitution.ui.js',[
        'depends' => [
            AppAsset::className ()
    ]
]);

$this->title = 'Affiliated Institutions';
$this->params['breadcrumbs'][] = ['label' => 'Extended Affiliatedinstitutions', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container">
    <div class="row">
        <div class="col-md-12 col-sm-12 pageheader Mtop15">Institution</div>
        <!-- Content -->
        <div class="col-md-12 col-sm-12 contentbg">
            <div class="col-md-12 col-sm-12 Mtopbot20">
                <!-- Section head -->
                <fieldset>
                    <legend style="font-size: 20px;">Affiliated Institutions
                    </legend>
                    <?php echo @Html::hiddenInput('homeUrl', Url::home(), array('id'=>'homeUrl'));?> 
                    <?php echo Html::hiddenInput(
                        'admin-delete-affiliated-institution-Url',
                        \Yii::$app->params['ajaxUrl']['admin-delete-affiliated-institution-Url'],
                        [
                            'id'=>'admin-delete-affiliated-institution-Url'
                        ]
                        ); ?>
                    <?php echo Html::hiddenInput(
                        'admin-get-countryCode-Url',
                        \Yii::$app->params['ajaxUrl']['admin-get-countryCode-Url'],
                        [
                            'id'=>'admin-get-countryCode-Url'
                        ]
                        ); ?>
                    <?= Html::a('Update', ['update', 'id' => $model->affiliatedinstitutionid], ['class' => 'btn btn-primary', 'title' => Yii::t('yii', 'Update') ]) ?>
                    <?= Html::button('Delete',[
                        'class' => 'btn btn-danger btn-delete-institution', 'data-affiliatedinstitutionid' => $model->affiliatedinstitutionid,
                            'data-url' => 'affiliatedinstitution/delete',
                        'title' => Yii::t('yii', 'Delete')]) ?>
                    <div class="Mtop20">
                        <?= DetailView::widget([
                            'model' => $model,
                            'attributes' => [
                                [
                                    'attribute'=>'name', 
                                    'label'=>'Institution Name',
                                    'value' => function ($model) {
                                        return ($model->name) ? $model->name : '';
                                    }
                                ],
                                [
                                    'attribute'=>'address1', 
                                    'label'=>'Address Line1',
                                    'value' => function ($model) {
                                        return ($model->address1) ? $model->address1 : '';
                                    }
                                ],
                                [
                                    'attribute'=>'address2', 
                                    'label'=>'Address Line2',
                                    'value' => function ($model) {
                                        return ($model->address2) ? $model->address2 : '';
                                    }
                                ],
                                [
                                    'attribute'=>'district', 
                                    'label'=>'District',
                                    'value' => function ($model) {
                                        return ($model->district) ? $model->district : '';
                                    }
                                ],
                                [
                                    'attribute'=>'state', 
                                    'label'=>'State',
                                    'value' => function ($model) {
                                        return ($model->state) ? $model->state : '';
                                    }
                                ],
                                [
                                    'attribute'=>'CountryID', 
                                    'label'=>'Country',
                                    'value' => function ($model) {
                                        return ($model->CountryID) ? $model->country->CountryName : '';
                                    }
                                ],
                                [
                                    'attribute'=>'pin', 
                                    'label'=>'Pin',
                                    'value' => function ($model) {
                                        return ($model->pin) ? $model->pin : '';
                                    }
                                ],
                                [
                                    'attribute' => 'phone1',
                                    'label'=>'Telephone Number 1',
                                    'value'=>function ($model) { 
                                        return ($model->phone1 && $model->phone1_areacode) ? $model->phone1_countrycode.'-'.$model->phone1_areacode.'-'.$model->phone1 : '';
                                    },
                                ],
                                [
                                    'attribute' => 'phone3',
                                    'label'=>'Telephone Number 2',
                                    'value'=>function ($model) { 
                                        return ($model->phone3 && $model->phone3_areacode) ? $model->phone3_countrycode.'-'.$model->phone3_areacode.'-'.$model->phone3 : '';
                                    },
                                ],
                                [
                                    'attribute' => 'phone2',
                                    'label'=>'Mobile Number',
                                    'value'=>function ($model) { 
                                        return $model->phone2 ? $model->mobilenocountrycode.'-'.$model->phone2 : '';
                                    },
                                ],
                                [
                                    'attribute' => 'email',
                                    'label'=>'Email',
                                    'value' => function ($model) {
                                        return ($model->email) ? $model->email: '';
                                    }
                                ],
                                [
                                    'attribute' => 'url',
                                    'label'=>'Website URL',
                                    'value' => function ($model) {
                                        return ($model->url) ? $model->url : 
                                        '';
                                    }
                                ],
                                [
                                    'attribute' => 'institutionlogo',
                                    'value'=> $model->institutionlogo ? 
                                        Yii::$app->params['imagePath'] .'/institutionlogo/'.$model->institutionid.'/'.$model->institutionlogo  : 
                                        $assetName->baseUrl. '/theme/images/institution-icon-grey.png',
                                    'format' => ['image',['min-width'=>'150px','min-height'=>'150px','width'=>'100px',
                                    'float'=>'left','border-radius' => '25px']],
                                ]
                            ],
                        ]) ?>
                </fieldset>
            </div>
        </div>
    </div>
</div>