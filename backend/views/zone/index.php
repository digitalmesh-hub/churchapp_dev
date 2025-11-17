<?php

use yii\helpers\Html;
use yii\grid\GridView;
use backend\assets\AppAsset;
use yii\widgets\ActiveForm;

$assetName = AppAsset::register($this);
$this->registerJsFile(
    $assetName->baseUrl . '/theme/js/Remember.zone.ui.js',
    [
        'depends' => [
                AppAsset::className()
        ]
    ]
);
$this->title = 'Zone';
?>
<div class="col-md-12 col-sm-12 pageheader Mtop15">Zone</div>
    <div class="col-md-12 col-sm-12 contentbg">
        <div class="col-md-12 col-sm-12 Mtopbot20">
<?php   
   $path   = ($update == 'add') ?  'zone/create' : 'zone/update/'.$model->zoneid ;
   $button = ($update == 'add') ?  'Add' : 'Update' ;
?>
  <div class="inlinerow Mtop20">
                        <div class="col-md-12 col-sm-12"><strong>Please enter Zone </strong></div>

    <?php $form = ActiveForm::begin(['action' => [$path],'options' => ['method' => 'post']]) ?>                                           
        <div class="inlinerow Mtop10">
            <div class="col-md-4 col-sm-4">
                <?= $form->field($model, 'description')->textInput(['maxlength' => true])->label(false); ?>
            </div>
            <div class="col-md-2 col-sm-2">
                <?= Html::submitButton( '<span class="glyphicon glyphicon-plus-sign"></span>'.'&nbsp'.$button, ['class' => 'btn btn-primary','id' => 'addzone', 'title' => $button]) ?>
                <a href="/zone/index" class="btn btn-primary clearbutton" title = 'Clear' id="clearbutton">&nbsp;Clear</a>
            </div>
              
    <?php ActiveForm::end(); ?>

        <!-- Validation errors -->
            <div class="col-md-6 col-sm-6" id="ErrorDiv">
                <div class="alert alert-danger text-center" hidden="hidden" id="ErrorMessageLabel" role="alert">Zone Cannot be blank</div>
            </div>
                            <!-- /.Validation errors -->
        </div>
    </div>
                
    <?= GridView::widget([
         'dataProvider' => $dataProvider,
         'layout' => '{items}</tbody></table><div class="table-btn text-right">{pager}</div>',
         'tableOptions' => ['class' => 'table table-bordered'],

         'columns' => [
            [
                'attribute' => 'description','contentOptions' => ['style' =>'width:70%'],
            ],
           
             [
              'contentOptions' => ['class' =>'text-center'],
              'class' => 'yii\grid\ActionColumn',
              'header' => 'Actions',
              'headerOptions' => ['style' => 'color:#337ab7','class' =>'text-center'],
              'template' => '{update} {active}',
              'buttons' => [
                'update' => function ($url, $model) {
                            return Html::a('<button class="btn btn-primary btn-sm edit">Edit</button>', $url, [
                                        'title' => Yii::t('app', 'Edit'),
                            ]);
                            },
                'active' => function ($url, $model) {
                        if($model->active == 0){

                            return Html::button('Activate', [
                                    'class' => 'btn btn-success btn-sm activate w80', 
                                    'id'    =>'btn-activate',
                                    'title' => Yii::t('app', 'Active'),
                                    'zoneid'    => $model->zoneid,
                                    'url' =>'zone/activate/',
                    ]);
                    }else{
                        return Html::button('Deactivate', [
                                     'class' => 'btn btn-danger btn-sm activate w80', 
                                     'id'    =>'btn-deactivate',
                                     'title' => Yii::t('app', 'Deactive'),
                                     'zoneid'    => $model->zoneid,
                                     'url'   =>'zone/deactivate/',
                                     'active' =>$model->active,
                    ]);        
                    }    
                }

              ],
  
            ],
        ],
    ]); ?>
</div>
</div>
