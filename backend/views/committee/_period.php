<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use backend\assets\AppAsset;
?>

<div role="tabpanel" class="tab-pane fade active in" id="committeeperiod">
    <?php $form = ActiveForm::begin(['enableClientValidation'=>false]) ?>
        <?php echo Html::hiddenInput(
                        'designationid',0,
                        [
                            'id'=>'designationid'
                        ]
                    ); ?>
        <div class="inlinerow Mtop20">
            <div class="col-md-12 col-sm-12">
                <strong>Please select the committee period</strong>

            </div>
            <div class="inlinerow Mtop10">
                <div class="col-md-3 col-sm-3">
                    <div class="labelbox"><strong>Committee<span style="color: red;">*</span></strong></div>
                    <div class="inlinerow Mtop10 committeetype">
                        <?= $form->field($periodModel, 'committeegroupid'
                        )
                        ->dropDownList(
                            $committeTypeList,
                            [
                             'id' => 'committeeTypeList',
                            // 'prompt' => 'Please Select'
                           ]
                    )->label(false) ?>
                    </div>
                </div>
                <div class="col-md-3 col-sm-3">
                    <div class="labelbox"><strong>Start Date  <span style="color: red;">*</span></strong></div>
                    <div class="inlinerow Mtop10">
                        <?php $periodModel->period_from = date('d F Y'); ?>
                        <?= $form->field($periodModel, "period_from")->widget(DatePicker::classname(), [
                            'name' => 'period_from',
                            'id' => 'period_from',
                            'value' => date('d F Y'),
                            'removeButton' => false, 
                            'options' => [
                                'placeholder' => 'Select Start Date',
                                'class' => 'period_from',
                            ],
                            'pluginOptions' => [
                                'autoclose'=>true,
                                'format' => 'dd MM yyyy',
                                'startDate' =>'0d',
                                'clearBtn' => false
                            ]])->label(false); ?>
                    </div>
                </div>
                <div class="col-md-3 col-sm-3">
                    <div class="labelbox"><strong>End Date  <span style="color: red;">*</span></strong></div>
                    <div class="inlinerow Mtop10">
                        <?php $periodModel->period_to = date('d F Y'); ?>
                        <?= $form->field($periodModel, "period_to")->widget(DatePicker::classname(), [
                            'name' => 'period_to',
                            'id' => 'period_to',
                            'value' => date('d F Y'),
                            'removeButton' => false, 
                            'options' => [
                                'placeholder' => 'Select End Date',
                                 'class' => 'period_to',
                            ],
                            'pluginOptions' => [
                                'autoclose'=>true,
                                'format' => 'dd MM yyyy',
                                'startDate' =>'0d'
                            ]
                        ])->label(false); ?>
                    </div>
                </div>

                <div class="col-md-2 col-sm-2">
                    <div class="labelbox">&nbsp;</div>
                    <div class="inlinerow Mtop10">
                       <?= Html::button('<span class="glyphicon glyphicon-plus-sign"></span>&nbsp;Add', ['class' => 'btn btn-primary add-committee-period', 'title' => \Yii::t('yii', 'Add'),"id"=>"button-add"]) ?>
                       <?= Html::button('Update', ['class' => 'btn btn-primary add-committee-period update-committee-period', 'title' => \Yii::t('yii', 'Add'),'style'=>'display:none','id'=>'button-update']) ?>
                    </div>
                </div>
           
                <div class="inlinerow Mtop10">
                    <div class="col-md-6 col-sm-6" id="CommitteePeriodErrorDiv" style="display: none">
                        <div class="alert alert-danger text-center" id="CommitteePeriodErrorMessageLabel" role="alert"><strong>Error! Please check again</strong></div>
                    </div>
                </div>
            </div>
        </div>
    <?php ActiveForm::end() ?>
    
    <div class="col-md-12 col-sm-12 Mtop20" id="fill-data">
        <table class="table table-bordered" id="tablecommitteetype" cellspacing="0" cellpadding="0">
            <tbody id="committeetypebody">
               
            <tr>
                <th width="75%">Committee Period</th>
                <th class="text-center" width="25%">Actions</th>
            </tr>
             <?php 
                if($periodResult){
                    $descriptionList = array();
                    foreach($periodResult as $model){ 
                        $fromDate = date_format(date_create($model['period_from']),\Yii::$app->params['dateFormat']['viewDateFormat']);
                        $toDate = date_format(date_create($model['period_to']),\Yii::$app->params['dateFormat']['viewDateFormat']);
                        if(!in_array($model['description'], $descriptionList)) { ?>
                            <tr>
                                <td colspan="2" class="commperiod"><?= Html::encode($model['description'])?></td>
                            </tr>
                        <?php
                            array_push($descriptionList, $model['description']);
                        } ?>
                        

                        <tr>
                            <td><?= Html::encode($fromDate)?> - <?= Html::encode($toDate)?></td>
                            <td class="text-center">
                                <?= Html::button('Edit',
                                    ['class' => 'btn btn-primary edit-period', 'data-startDate'=> $fromDate, 
                                    'data-endDate'=>$toDate,
                                    'data-committe-type'=> $model['committeegroupid'], 'data-period_id' =>$model['committee_period_id'],
                                    'title' => \Yii::t('yii', 'Edit')
                                    ]
                                ); ?>
                                <?php 
                                if($model['active'] == 1){ ?>
                                    <?= Html::button('Deactivate',['class' => 'btn btn-danger btn-active', 'title' => \Yii::t('yii', 'Deactivate'),'data-committee_period_id' => 
                                        $model['committee_period_id'],
                                        'data-active' => $model['active'],
                                        'style' => 'width:50%']); ?>
                                <?php }
                                else{ ?>
                                    <?= Html::button('Activate',['class' => 'btn btn-success btn-active', 'title' => \Yii::t('yii', 'Activate'),
                                        'data-committee_period_id' => 
                                        $model['committee_period_id'],
                                        'data-active' => $model['active'],
                                        'style' => 'width:50%;']
                                    );?>
                                <?php } ?>      
                            </td>
                        </tr>
                    <?php } 
                }
                else{?>
                    <tr>
                        <td colspan="2" class="text-center"><?= Html::encode('No Records')?></td>
                    </tr>
                <?php } ?> 
            </tbody>
        </table>
    </div>
</div>



