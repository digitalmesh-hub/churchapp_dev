<?php

use yii\helpers\Html;
use yii\grid\GridView;
use backend\assets\AppAsset;
use yii\widgets\ActiveForm;

$assetName = AppAsset::register($this);
$this->registerJsFile(
    $assetName->baseUrl . '/theme/js/Remember.designation.ui.js',
    [
        'depends' => [
                AppAsset::className()
        ]
    ]
);
?>
<div class="col-md-12 col-sm-12 pageheader Mtop15">Staff Designation</div>
    <div class="col-md-12 col-sm-12 contentbg">
        <div class="col-md-12 col-sm-12 Mtopbot20">
<?php   
   $path   = ($update == 'add') ?  'staffdesignation/create' : 'staffdesignation/update/'.$model->staffdesignationid ;
   $button = ($update == 'add') ?  'Add' : 'Update' ;
?>
  <div class="inlinerow Mtop20">
                        <div class="col-md-12 col-sm-12"><strong>Please enter Staff Designation </strong></div>

    <?php $form = ActiveForm::begin(['action' => [$path],'options' => ['method' => 'post']]) ?>                                           
        <div class="inlinerow Mtop10">
            <div class="col-md-4 col-sm-4">
                <?= $form->field($model, 'designation')->textInput(['maxlength' => true])->label(false); ?>
            </div>
            <div class="col-md-3 col-sm-3">
                <?= Html::submitButton( '<span class="glyphicon glyphicon-plus-sign"></span>'.'&nbsp'.$button, ['class' => 'btn btn-primary','id' => 'addstaffdesignation', 'title' => $button]) ?>
                <a href="/staffdesignation/index" class="btn btn-primary clearbutton" title = 'Clear' id="clearbutton">&nbsp;Clear</a>
            </div>
              
    <?php ActiveForm::end(); ?>

        <!-- Validation errors -->
            <div class="col-md-6 col-sm-6" id="ErrorDiv">
                <div class="alert alert-danger text-center" hidden="hidden" id="ErrorMessageLabel" role="alert">Staff Designation Cannot be blank</div>
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
                'attribute' => 'designation','contentOptions' => ['style' =>'width:70%'],
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
                                    'title' => Yii::t('app', 'Activate'),
                                    'staffdesignationid'    => $model->staffdesignationid,
                                    'url' =>'staffdesignation/activate',
                    ]);
                    }else{
                        return Html::button('Deactivate', [
                                     'class' => 'btn btn-danger btn-sm activate w80', 
                                     'id'    =>'btn-deactivate',
                                     'title' => Yii::t('app', 'Deactivate'),
                                     'staffdesignationid'    => $model->staffdesignationid,
                                     'url'   =>'staffdesignation/deactivate',
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


