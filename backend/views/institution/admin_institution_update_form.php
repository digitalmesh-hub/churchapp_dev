<?php 
   use yii\helpers\Html;
   use backend\assets\AppAsset;
   use yii\bootstrap\ActiveForm;
   use backend\components\widgets\FlashResult;
   use yii\helpers\Url;
   
   $assetName = AppAsset::register($this);
   $this->registerJsFile(
       $assetName->baseUrl . '/theme/js/Remember.institution.ui.js',
       [
           'depends' => [
                   AppAsset::className()
           ]
       ]
   );
   echo Html::hiddenInput(
           'defaultInsitutionLogo',
           $assetName->baseUrl.'/theme/images/institution-icon-grey.png',
           [
                   'id'=>'defaultInsitutionLogo'
           ]
   );
   echo Html::hiddenInput(
        'autoCountryCode',
        yii::$app->params['ajaxUrl']['auto-country-code'],
        [
                'id'=>'auto-country-code'
        ]
);
  $this->title = 'Institution';
   ?>
<!-- Header -->
<div class="col-md-12 col-sm-12 col-xs-12 pageheader Mtop15">
   <?= $this->title ?>
</div>
<!-- Content -->
<div class="col-md-12 col-sm-12 contentbg">
   <div class="col-md-12 col-sm-12 Mtopbot20">
      <?= FlashResult::widget(); ?>
      <!-- Tab Panels -->
      <div class="blockrow">
               <fieldset>
                <legend style="font-size: 20px;">Manage Institution</legend>
                  <div class="blockrow Mtop50">
                     <?php $form = ActiveForm::begin(
                        [
                            'options'=>[
                            'enctype' => 'multipart/form-data',
                            'id' =>'form',
                        ],
                        'fieldConfig' => [
                            'template' => "{beginWrapper} {input} {error} {endWrapper}",
                        ],
                        ]
                        ); ?>
                     <!-- Photo -->
                     <div class="inlinerow">
                        <div class="col-md-2 col-sm-2 col-md-offset-3 col-sm-offset-3">
                           <?php if(!empty($formModel->institutionlogo)) { ?>
                           <?= Html::img(yii::$app->params['imagePath'].$formModel->institutionlogo, 
                              ['alt' => 'Institution logo','style' => "width: 160px; border-radius: 25px; height: 150px;", 'id' => 'institutionImage']) ?>
                           <?php } else { ?>
                           <img style="width:160px; border-radius:25px; height:150px;" class="form-control" id = 'institutionImage' src="<?php echo $assetName->baseUrl; ?>/theme/images/institution-icon-grey.png"/>
                           <?php  }?>
                        </div>
                        <div class="col-md-4 col-sm-4 Mtop50">
                           <div class="inlinerow">Please upload an image</div>
                           <div class="inlinerow Mtop15">
                              <?= $form->field($formModel, 'institutionlogo')->fileInput(['class' => 'form-control','id' => 'institution-logo'])->label(false); ?> 
                           </div>
                        </div>
                     </div>
                     <!-- Details -->
                     <div class="inlinerow Mtop50">
                        <!-- Section 1 -->
                        <div class="col-md-6 col-sm-6">
                           <div class="inner-rows">
                              <div class="col-md-5 col-sm-5">Institution Name <span style="color: red;">*</span></div>
                              <div class="col-md-7 col-sm-7">
                                 <?= $form->field($formModel, 'name')->textInput(['maxlength' => true])->label(false); ?> 
                              </div>
                           </div>
                           <div class="inner-rows">
                              <div class="col-md-5 col-sm-5">Address Line1 <span style="color: red;">*</span></div>
                              <div class="col-md-7 col-sm-7">
                                 <?= $form->field($formModel, 'address1')->textArea(['maxlength' => true])->label(false); ?> 
                              </div>
                           </div>
                           <div class="inner-rows">
                              <div class="col-md-5 col-sm-5">State <span style="color: red;">*</span></div>
                              <div class="col-md-7 col-sm-7">
                                 <?= $form->field($formModel, 'state')->textInput(['maxlength' => true])->label(false); ?>
                              </div>
                           </div>
                           <div class="inner-rows">
                              <div class="col-md-5 col-sm-5">District <span style="color: red;">*</span></div>
                              <div class="col-md-7 col-sm-7">
                                 <?= $form->field($formModel, 'district')->textInput(['maxlength' => true])->label(false); ?>
                              </div>
                           </div>
                           <div class="inner-rows">
                              <div class="col-md-5 col-sm-5">Time Zone  <span style="color: red;">*</span></div>
                              <div class="col-md-7 col-sm-7">
                                 <?= $form->field($formModel, 'timezone') ->dropDownList(
                                    $formModel->getTimeZone(),
                                    [
                                    'prompt'=>'Please Select'
                                    ]
                                    )
                                    ->label(false);
                                    ?>
                              </div>
                           </div>
                           <div class="inner-rows">
                              <div class="col-md-5 col-sm-5">Institution Type <span style="color: red;">*</span></div>
                              <div class="col-md-7 col-sm-7">
                                 <?= $form->field($formModel, 'institutiontype') ->dropDownList(
                                    $formModel->getInstitutionTypes(),
                                    [
                                    'prompt'=>'Please Select', 
                                    ]
                                    )
                                    ->label(false);
                                    ?>
                              </div>
                           </div>
                           <div class="inner-rows">
                              <div class="col-md-5 col-sm-5">Website Url </div>
                              <div class="col-md-7 col-sm-7">
                                 <?= $form->field($formModel, 'url')->textInput(['maxlength' => true])->label(false); ?>
                              </div>
                           </div>
                           <div class="inner-rows">
                              <div class="col-md-5 col-sm-5">Facebook </div>
                              <div class="col-md-7 col-sm-7">
                                 <?= $form->field($formModel, 'facebook')->textInput(['maxlength' => true])->label(false); ?>
                              </div>
                           </div>
                           <div class="inner-rows">
                              <div class="col-md-5 col-sm-5">Instagram </div>
                              <div class="col-md-7 col-sm-7">
                                 <?= $form->field($formModel, 'instagram')->textInput(['maxlength' => true])->label(false); ?>
                              </div>

                           </div>
                        </div>
                        <!-- /. Section 1 -->
                        <!-- Section 2 -->
                        <div class="col-md-6 col-sm-6">
                           <div class="inner-rows">
                              <div class="col-md-5 col-sm-5">Email</div>
                              <div class="col-md-7 col-sm-7">
                                 <?= $form->field($formModel, 'email')->textInput(['maxlength' => true])->label(false); ?>
                              </div>
                           </div>
                           <div class="inner-rows">
                              <div class="col-md-5 col-sm-5">Address Line2 </div>
                              <div class="col-md-7 col-sm-7">
                                 <?= $form->field($formModel, 'address2')->textArea(['maxlength' => true])->label(false); ?>
                              </div>
                           </div>
                           <div class="inner-rows">
                              <div class="col-md-5 col-sm-5">Country <span style="color: red;">*</span></div>
                              <div class="col-md-7 col-sm-7">
                                 <?= $form->field($formModel, 'countryid') ->dropDownList(
                                    $formModel->getCountry(),
                                    [
                                    'prompt'=>'Please Select'
                                    ]
                                    )
                                    ->label(false);
                                    ?>
                              </div>
                           </div>
                           <div class="inner-rows">
                              <div class="col-md-5 col-sm-5">Pin <span style="color: red;">*</span></div>
                              <div class="col-md-7 col-sm-7">
                                 <?= $form->field($formModel, 'pin')->textInput(['maxlength' => true])->label(false); ?>
                              </div>
                           </div>
                           <div class="inner-rows">
                              <div class="col-md-5 col-sm-5">Telephone Number </div>
                              <div class="col-md-7 col-sm-7 phone-div">
                                 <?= $form->field($formModel, 'phone1_countrycode')->textInput(['maxlength' => 4 ,'class'=>'citycodes form-control number-check'])->label(false) ?>
                                 <?= $form->field($formModel, 'phone1_areacode')->textInput(['maxlength' => 5 ,'class'=>'citycodes form-control number-check'])->label(false) ?>
                                 <?= $form->field($formModel, 'phone1')->textInput(['maxlength' => 7, 'class' => 'fullnumber form-control number-check'])->label(false); ?>
                                 <div class="error-phone1 error-div  msg-div" style="display:none">Invalid Telephone Number</div>
                              </div>
                           </div>
                           <div class="inner-rows">
                              <div class="col-md-5 col-sm-5">Mobile Number </div>
                              <div class="col-md-7 col-sm-7 phone-div">
                                 <?= $form->field($formModel, 'phone2_countrycode')->textInput(['maxlength' => 4 ,'class'=>'citycodes form-control number-check'])->label(false) ?>
                                 <?= $form->field($formModel, 'phone2')->textInput(['maxlength' => 10, 'class' => 'fullnumber-affiliated form-control number-check'])->label(false); ?>
                                 <div class="error-phone2 msg-div  error-div" style="display:none">Invalid Mobile Number</div>
                              </div>
                           </div>
                           <div class="inner-rows">
                              <div class="col-md-5 col-sm-5">Twitter </div>
                              <div class="col-md-7 col-sm-7">
                                 <?= $form->field($formModel, 'twitter')->textInput(['twitter' => true])->label(false); ?>
                              </div>
                           </div>
                           <div class="inner-rows">
                              <div class="col-md-5 col-sm-5">Youtube </div>
                              <div class="col-md-7 col-sm-7">
                                 <?= $form->field($formModel, 'youtube')->textInput(['youtube' => true])->label(false); ?>
                              </div>
                           </div>
                        </div>
                        <!-- /. Section 2 -->
                     </div>
                     <div class="inlinerow Mtop30 text-center">
                        <?= Html::submitButton('Save', ['class' =>  'btn btn-success save-institution' , 'title' => 'Save']) ?>
                     </div>
                     <?php ActiveForm::end(); ?>
                  </div>
              </fieldset>
        </div>
   </div>
</div>
<!-- /. Blockrow closed -->
</div>
<!-- /. Blockrow closed -->  
</div>
</div>
<!-- Contents closed -->