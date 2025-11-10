<?php 

use yii\grid\GridView;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\widgets\Pjax;
use backend\assets\AppAsset;
use backend\components\widgets\FlashResult;

$assetName = AppAsset::register($this);
$this->registerJsFile(
    $assetName->baseUrl . '/theme/js/Remember.account.ui.js',
    [
        'depends' => [
                AppAsset::className()
        ]
    ]
);
echo Html::hiddenInput(
    'admin-deactivation-Url',
    \Yii::$app->params['ajaxUrl']['admin-deactivation-Url'],
    [
        'id'=>'admin-deactivation-Url'
    ]
);
echo Html::hiddenInput(
    'admin-activation-Url',
    \Yii::$app->params['ajaxUrl']['admin-activation-Url'],
    [
        'id'=>'admin-activation-Url'
    ]
);

?>
 <div class="blockrow Mtop25">
            <?= FlashResult::widget(); ?>
           <?php $form = ActiveForm::begin(
                [
                'method' => 'get',
                ]
                ); ?>
           <div class="col-md-12 Mtop5">
                            
                            <div class="col-md-4 col-md-offset-3 col-sm-4 col-sm-offset-3">
                                <?= $form->field($searchModel, 'fullName')->textInput(['placeholder' => 'Search admin by name'])->label(false); ?>
                            </div>
                            <div class="col-md-2 col-sm-2">
                                <?= Html::submitButton('Search', ['class' => 'btn btn-primary','title' => Yii::t('yii', 'Search')]) ?>
                            </div>
                            <div class="col-md-3 col-sm-3">
                            <?= $form->field($searchModel, 'institutionid') ->dropDownList(
                                            $institutions,
                                            [
                                            'prompt' => 'All',
                                            'id' => 'institution-name'
                                            ]
                                            )
                                        ->label(false);
                                        ?> 
                            </div>

            </div>
        <?php ActiveForm::end(); ?>
       
        <?php Pjax::begin(); ?>
 <?= GridView::widget([
         'dataProvider' => $dataProvider,
         'layout' => '{items}</tbody></table><div class="table-btn text-right">{pager}</div>',
         'tableOptions' => ['class' => 'table employeelist'],
         'columns' => [
            [
                'attribute' => 'fullName',
                'header' => '<p class="text-center">Name</p>',
            ],
            [
                'attribute' => 'institution_name',
                'header' => '<p class="text-center">Institution</p>'
            ],
            [
                'attribute' =>'isActive',
                'header' => '<p class="text-center">Status</p>',
                'value' => function ($data) {
                    return ($data['isactive'] ==1 )  ? 'Active Admin' : 'Inactive Admin';
                }
            ],
            [
              'contentOptions' => ['class' =>'text-center'],
              'class' => 'yii\grid\ActionColumn',
              'template' => '{manage} {deactivate} {impersonate}',
              'buttons' => [
               'manage' => function ($url, $model) {
                            return  Html::a(
                                'Manage',
                                [
                                'account/update-admin', 'id' => $model['profileId']],
                                [
                                'class' => 'btn btn-success btn-sm manage',
                                'title' => Yii::t('yii', 'Manage')
                                ]
                            );
                    },
                    'deactivate' => function ($url, $model) {
                            if ($model['isactive'] == 1) {
                                 return  Html::button(
                                'Deactivate',
                                ['class' => 'btn btn-danger btn-sm activate w80',
                                'id' => 'btn-admin-deactivate',
                                'title' => Yii::t('yii', 'Deactivate'),
                                'data-profile-id' => $model['profileId'],
                                
                                ]
                            );
                            } else {
                                     return  Html::button(
                                'Activate',
                                [
                                'class' => 'btn btn-info btn-sm activate w80',
                                'id' =>'btn-admin-activate', 
                                'title' => Yii::t('yii', 'Activate'),
                                'data-profile-id' => $model['profileId'],
                                ]
                            );
                            }
                            
                    },
                    'impersonate' => function($url,$model) {
                        return  Html::a(
                                'Impersonate',
                                [
                                    'account/impersonate', 'id' => $model['userid']],
                                [
                                    'class' => 'btn btn-warning btn-sm',
                                    'title' => Yii::t('yii', 'Imerpsonate')
                                ]
                            );
                    }
              ],
  
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
    </div>