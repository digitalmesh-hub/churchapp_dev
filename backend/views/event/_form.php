<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use kartik\datetime\DateTimePicker;
use backend\assets\AppAsset;
use common\models\extendedmodels\ExtendedInstitution;
use yii\jui\AutoComplete;
use yii\web\JsExpression;

$assetName = AppAsset::register($this);
$this->registerJsFile(
    $assetName->baseUrl . '/theme/js/Remember.Event.ui.js',
    [
        'depends' => [
                AppAsset::className()
        ]
    ]
);

echo Html::hiddenInput('image-upload-url','news/upload',['id'=>'imageUpload']);

?>
<div class="extended-event-form">
    <div class="col-md-12 col-sm-12 contentbg">
        <div class="col-md-12 col-sm-12 Mtopbot20">
            <div class="blockrow Mtop50">
                <legend style="font-size: 20px;">Events </legend>
                    <div class="blockrow Mtop20">
                        <div class="inlinerow Mtop10">
                    <?php  $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
                        <div class="col-md-2 col-sm-2 L32">
                             Event Heading <span style="color: red;">*</span>
                        </div>    
                    <div class="col-md-8 col-sm-8">
                    <?= $form->field($model, 'notehead')->textInput(['maxlength' => true])->label(false) ?>
                    </div>
                        </div>
                     <div class="inlinerow Mtop10">
                  
                        <div class="col-md-2 col-sm-2 L32">
                              Event Body  <span style="color: red;">*</span>
                        </div>    
                    <div class="col-md-8 col-sm-8 z-index-11">
                    <?= $form->field($model, 'notebody')->textArea(['class' =>'input-description', 'id' => 'txtnotebody' ])->label(false) ?>
                    </div>
                        </div>

                        <div class="col-md-6 col-sm-6 Mtop20">
                            <!-- rows -->
                                <div class="inlinerow Mtop10">
                                    <div class="col-md-4 col-sm-5 L32">
                                        Event Venue
                                    </div>
                                <div class="col-md-8 col-sm-8">
                                    <?= $form->field($model, 'venue')->textInput(['maxlength' => true])->label(false) ?>
                                    </div>
                                </div>
                                <!--/.rows -->
                                <!-- rows -->
                                <div class="inlinerow Mtop10">
                                    <div class="col-md-4 col-sm-5 L32">Note URL</div>
                                    <div class="col-md-8 col-sm-8">
                                      <?= $form->field($model, 'noteurl')->textInput(['maxlength' => true])->label(false) ?>
                                    </div>
                                </div>
                                <!--/.rows -->

                                <!-- rows -->
                                <div class="inlinerow Mtop10">
                                    <div class="col-md-4 col-sm-5 L32">RSVP Available</div>
                                    <div class="col-md-8 col-sm-8">
                                        <?= $form->field($model, 'rsvpavailable')->radioList([1 => 'Yes', 0 => 'No'])->label(false); ?>
                                    </div>
                                </div>
                                <!--/.rows -->
                                <!-- rows -->
                                <?php if(yii::$app->user->identity->institution->institutiontype == ExtendedInstitution::INSTITUTION_TYPE_CHURCH) { ?>
                                    <div class="inlinerow Mtop10">
                                        <div class="col-md-4 col-sm-5 L32">Family Unit</div> 
                                        <div class="col-md-8 col-sm-8">
                                            <?= $form->field($model, 'familyunitid')->label(false)->dropDownList(
                                            $familyUnits,['prompt' =>'Please select']
                                    )?>
                                        </div>
                                    </div>
                               <?php } ?>
                                <?php

                                $model->batch = ($model->batch==null) ? ['All'] :  explode(',',$model->batch);
                                ?>
                               <?php if(yii::$app->user->identity->institution->institutiontype == ExtendedInstitution::INSTITUTION_TYPE_EDUCATION) { ?>
                                    <div class="inlinerow Mtop10">
                                        <div class="col-md-4 col-sm-5 L32">Batch <span style="color: red;">*</span> </div> 
                                        <div class="col-md-8 col-sm-8">
                                        <?= $form->field($model, 'batch') ->dropDownList(
                                            $batches,
                                            [
                                                'class' => 'form-control drop-select',
                                                'multiple'=> true
                                            ]
                                            )
                                        ->label(false);
                                        ?>
                                        </div>
                                    </div>
                               <?php } ?>

                            </div>
                            <div class="col-md-6 col-sm-6 Mtop20">
                                <!-- rows -->
                                <div class="inlinerow Mtop10">
                                    <div class="col-md-1 col-sm-1 L32"></div>
                                    <div class="col-md-4 col-sm-5 L32">Event Date and Time &nbsp;<span style="color: red;">*</span></div>
                                    <div class="col-md-7 col-sm-7">
                                       <?=$form->field($model, 'activitydate')->widget(DateTimePicker::classname(), [
                                            'options' => [
                                                'placeholder' => 'Choose date and time',
                                            ],
                                            'pluginOptions' => [
                                                'autoclose'=>true,
                                                'format' => 'dd MM yyyy hh:ii:ss',
                                                'startDate' => date('d M Y H:i:s'),
                                            ]
                                        ])->label(false); ?>
                                    </div>
                                </div>
                                <!--/.rows -->

                                <!-- rows -->
                                <div class="inlinerow Mtop10">
                                    <div class="col-md-1 col-sm-1 L32"></div>
                                    <div class="col-md-4 col-sm-5 L32">Activated On &nbsp;<span style="color: red;">*</span></div>
                                    <div class="col-md-7 col-sm-7">
                                        <?=$form->field($model, 'activatedon')->widget(DatePicker::classname(), [
                                            'options' => [
                                                'placeholder' => 'Choose date',
                                                'id' =>'activatedon',
                                            ],
                                            'pluginOptions' => [
                                                'autoclose'=>true,
                                                'format' => 'dd MM yyyy',
                                                'startDate' =>'0d',
                                            ]
                                        ])->label(false); ?>
                                    </div>
                                </div>
                                <!--/.rows -->
                                <!-- rows -->
                                <div class="inlinerow Mtop10">
                                    <div class="col-md-1 col-sm-1 L32"></div>
                                    <div class="col-md-4 col-sm-5 L32">Expiry Date</div>
                                    <div class="col-md-7 col-sm-7">
                                        <?=$form->field($model, 'expirydate')->widget(DatePicker::classname(), [
                                            'options' => [
                                                'placeholder' => 'Choose date',
                                                'id' =>'expriydate',
                                            ],
                                            'pluginOptions' => [
                                                'autoclose'=>true,
                                                'format' => 'dd MM yyyy',
                                                'startDate' =>'0d',
                                            ]
                                        ])->label(false); ?>
                                    </div>
                                </div>
                                    <!-- rows -->
                                    <div class="inlinerow Mtop10">
                                        <div class="col-md-1 col-sm-1 L32"></div>
                                         <div class="col-md-4 col-sm-5 L32">Select Member </div>
                                                <div class="col-md-7 col-sm-7">
                                       <?= $form->field($model, 'members')->widget(\yii\jui\AutoComplete::classname(),[
                                            'options' => [
                                                'class' => 'form-control member-append'
                                            ],
                                            'clientOptions' => [
                                            'source' => $members,
                                            'autoFill' => true,
                                            'minLength'=> 2, 
                                            'select' => new JsExpression("function( event, ui ) {
                                                    type = (ui.item.usertype == 'M') ? 'member' :'spouse'; 
                                                    _url = 're-member://'+type+'/'+ ui.item.id;
                                                    title =  ui.item.name;
                                                    $('#summernote').summernote('saveRange');
                                                    $('.input-description').summernote('createLink', {
                                                      text: title,
                                                      url: _url,
                                                      isNewWindow: true
                                                    });
                                                    $('#summernote').summernote('restoreRange');

                                                    
                                            }")
                                        ],
                                        ])->label(false);?>
                                        <!-- $('.input-description').summernote('createLink', {
                                                      text: title,
                                                      url: _url,
                                                      isNewWindow: true
                                                    }); -->
                                        <!-- $('.note-editable').append('&nbsp&nbsp<a href=' + _url + ' target=_blank >' + title + '</a>'); -->
                                        </div>
                                    </div>
                                    <!--/.rows -->
                                <!--/.rows -->
                                <div class="inlinerow Mtop10">
                                    <div class="col-md-1 col-sm-1 L32"></div>
                                    <div class="col-md-10 col-sm-10 L32"><strong class="expmsg">Event will cease to display at 12 midnight</strong></div>
                                </div>
                            </div>
                                <div class="inlinerow Mtop20 text-center">
                                      <?= Html::submitButton('Save', ['class' => 'btn btn-success btn-extra','id' =>'btn-save','title' => 'Save']) ?>&nbsp;&nbsp;
                                 <a href="/event/index" class="btn btn-danger btn-extra" title = 'Cancel' id="btnCancel">&nbsp;Cancel</a>
                                </div>
            <?php ActiveForm::end(); ?>
            </div>
        </div>
</div>
</div>
</div>
