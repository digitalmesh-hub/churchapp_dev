<?php 


use backend\assets\AppAsset;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use yii\helpers\Html;
use yii\web\view;

$assetName = AppAsset::register($this);

$this->title = 'Login Report';

$this->registerJs(<<<JS
    $('#startdate').on('change',function(e)
    {
        var start_date = $('#startdate').val();
        if(start_date != '')
        {
           $('#enddate-kvdate').kvDatepicker('setStartDate', start_date);
        }
        
    });
JS
,View::POS_READY);
?>

<!-- Header -->
<div class="col-md-12 col-sm-12 col-xs-12 pageheader Mtop15">
    <?= $this->title ?>
</div>
<div class="extended-userprofile-index">

                <!-- Content -->
                <div class="col-md-12 col-sm-12 contentbg">
                    <div class="col-md-12 col-sm-12 Mtopbot20">
                        <!-- Section head -->
                        <fieldset>
                         <?php $form = ActiveForm::begin([]); ?>
                            <legend style="font-size: 20px;">Generate LogIn Reports</legend>
                            <div class="inlinerow">
                                <div class="col-md-3 col-sm-3">
                                    <div class="labelbox"><strong>Institution</strong></div>
                                    <div class="inlinerow Mtop10"> 
                                    <?= $form->field($model, 'institutionId')
                                         ->dropDownList($institutionModel,
                                             [
                                             'id' => 'institution_id',
                                             'prompt' => 'All',
                                           ]
                                         )->label(false) ?>                                     
                                   
                                    </div>
                                </div>

                                <div class="col-md-3 col-sm-3">
                                    <div class="labelbox"><strong>Start Date</strong></div>
                                    <div class="inlinerow Mtop10">
                                       <?= DatePicker::widget([
                                        'name' => 'start_date',
                                        'id' => 'startdate',
                                        'value' => date('d F Y'),
                                        'readonly' => true, 
                                        'options' => [
                                            'placeholder' => 'Select Start Date',
                                        ],
                                    'pluginOptions' => [
                                            'autoclose'=>true,
                                            'format' => 'dd MM yyyy',
                                            'endDate' =>'0d'
                                        ]
                                    ]); ?> 
                                    </div>
                                </div>

                                <div class="col-md-3 col-sm-3">
                                    <div class="labelbox"><strong>End Date</strong></div>
                                    <div class="inlinerow Mtop10">
                                       <?= DatePicker::widget([
                                        'name' => 'end_date',
                                        'id' => 'enddate',
                                        'value' => date('d F Y'),
                                        'readonly' => true, 
                                        'options' => [
                                            'placeholder' => 'Select End Date',
                                        ],
                                    'pluginOptions' => [
                                            'autoclose'=>true,
                                            'format' => 'dd MM yyyy',
                                            'startDate' => date('d F Y'),
                                            'endDate' =>'0d'
                                        ]
                                    ]); ?>
                                    </div>
                                </div>

                                <div class="col-md-3 col-sm-3">
                                    <div class="labelbox">&nbsp;</div>
                                    <div class="inlinerow Mtop10">
                                        <?= Html::submitButton('Export as excel',['class' => 'btn btn-primary','title' => Yii::t('yii', 'Export excel report')]) ?>
                                    </div>
                                </div>

                            </div>
                            <?php ActiveForm::end(); ?>

                            <!-- /. Section 2 -->
                        </fieldset>

                    </div>

                </div>                   
</div>
  