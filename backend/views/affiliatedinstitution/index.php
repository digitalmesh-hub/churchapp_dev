<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;
use backend\assets\AppAsset;
use backend\components\widgets\FlashResult;

/* @var $this yii\web\View */
/* @var $searchModel common\models\searchmodels\ExtendedAffiliatedinstitutionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$assetName = AppAsset::register($this);
$this->registerJsFile ( $assetName->baseUrl . '/theme/js/Remember.affiliatedInstitution.ui.js',[
        'depends' => [
            AppAsset::className ()
    ]
]);

$this->title = 'Affiliated Institutions';
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
                    <legend style="font-size: 20px;">Affiliated Institutions</legend>
                    <?= FlashResult::widget(); ?>
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
                    <p>
                        <?= Html::a('Create Affiliated Institution', ['create'], ['class' => 'btn btn-success', 'title' => Yii::t('yii', 'Create Affiliated Institution')])?>
                    </p>
                    <div class="inlinerow Mtop30">
                        <?php Pjax::begin(); ?>
                            <?= GridView::widget([
                                'dataProvider' => $dataProvider,
                                'filterModel' => $searchModel,
                                'layout' => '{items}<div class="text-right">{pager}</div>',
                                'pager' => [
                                    'prevPageLabel' => '<span aria-hidden="true">« Previous</span> </a>',
                                    'nextPageLabel' => '<span aria-hidden="true"> Next »</span>',
                                    'maxButtonCount' => 10,
                                ],
                                'columns' => [
                                    [
                                        'attribute' => 'name',
                                        'label'=>'Institution Name',
                                        'headerOptions'=> 
                                            ['style'=>'text-align:center'],
                                        'contentOptions' => ['class'=>'text-center'],
                                        'value'=>function ($model) { 
                                            return $model->name ? $model->name : '';
                                        },
                                    ],
                                    [
                                        'attribute' => 'district',
                                        'headerOptions'=> 
                                            ['style'=>'text-align:center'],
                                       'contentOptions' => ['class'=>'text-center'],
                                        'value'=>function ($model) { 
                                            return $model->district ? $model->district : '';
                                        },
                                    ],
                                    [
                                        'attribute' => 'state',
                                        'headerOptions'=> 
                                            ['style'=>'text-align:center'],
                                        'contentOptions' => ['class'=>'text-center'],
                                        'value'=>function ($model) { 
                                            return $model->state ? $model->state : '';
                                        },
                                    ],
                                    [
                                        'attribute' => 'phone1',
                                        'label'=>'Telephone Number',
                                        'value'=>function ($model) { 
                                            return ($model->phone1 && $model->phone1_areacode) ? $model->phone1_countrycode.'-'.$model->phone1_areacode .'-'.$model->phone1 : $model->phone1_countrycode.'-'.$model->phone1;
                                            },
                                        'headerOptions'=> 
                                            ['style'=>'text-align:center'],
                                        'contentOptions' => ['class'=>'text-center'],
                                    ],
                                    [
                                        'class' => 'yii\grid\ActionColumn',
                                        'contentOptions'=>['class' => 'text-center','style'=>'vertical-align: middle;'],
                                        'headerOptions' => ['class' => 'text-center','style'=>'vertical-align: middle;'],
                                        'header' => 'Actions',
                                        'template' => '{edit} {delete}',
                                        'buttons' => [
                                            'edit' => function($url, $model, $key) {
                                                return Html::a('',['update', 'id'=> $model->affiliatedinstitutionid],
                                                    ['class' => 'btn glyphicon glyphicon-pencil', 'title' => Yii::t('yii', 'Edit')
                                                    ]
                                                );
                                            },
                                            'delete' => function($url, $model, $key) {
                                                return Html::button('',
                                                    ['class' => 'btn glyphicon glyphicon-trash btn-delete-institution delete-icon-1',
                                                    'data-affiliatedinstitutionid' =>$model->affiliatedinstitutionid,'title' => Yii::t('yii', 'Delete')]
                                                );
                                            },
                                            // 'view' => function($url, $model, $key) { 
                                            //     return Html::a('',['view', 'id'=> $model->affiliatedinstitutionid],['class' => 'btn glyphicon glyphicon-eye-open', 'title' => Yii::t('yii', 'View')]
                                            //     );
                                            // }
                                        ],
                                    ],
                                ],
                            ]); ?>
                        <?php Pjax::end(); ?>
                    </div>
                </fieldset>
            </div>
        </div>
    </div>
</div>
