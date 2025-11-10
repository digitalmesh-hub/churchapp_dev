<?php

use yii\helpers\Html;
use backend\assets\AppAsset;
use yii\bootstrap\ActiveForm;
use backend\components\widgets\FlashResult;
use yii\helpers\Url;
use kartik\date\DatePicker;

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
   $assetName->baseUrl . '/theme/images/institution-icon-grey.png',
   [
      'id' => 'defaultInsitutionLogo'
   ]
);
echo Html::hiddenInput(
   'dashboard-item-url',
   \Yii::$app->params['ajaxUrl']['dashboard-item-url'],
   [
      'id' => 'dashboard-item-url'
   ]
);
echo Html::hiddenInput(
   'autoCountryCode',
   yii::$app->params['ajaxUrl']['auto-country-code'],
   [
      'id' => 'auto-country-code'
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
         <!-- Nav tabs -->
         <ul class="nav nav-tabs" role="tablist" id="myTab">
            <li role="presentation" class="active">
               <a href="#manage_institution" aria-controls="home" role="tab" data-toggle="tab">Manage Institution</a>
            </li>
            <li role="presentation">
               <a href="#manage_dashboard" aria-controls="profile" role="tab" data-toggle="tab">Manage Dashboard</a>
            </li>
            <li role="presentation">
               <a href="#manage_survey" aria-controls="profile" role="tab" data-toggle="tab">Survey Credentials</a>
            </li>
         </ul>
         <!-- Section head -->
         <!-- Tab panes -->
         <div class="tab-content">
            <!-- Member list -->
            <div role="tabpanel" class="tab-pane fade in active" id="manage_institution">
               <fieldset>
                  <div class="blockrow Mtop50">
                     <?php $form = ActiveForm::begin(
                        [
                           'options' => [
                              'enctype' => 'multipart/form-data',
                              'id' => 'form',
                           ],
                           'fieldConfig' => [
                              'template' => "{beginWrapper} {input} {error} {endWrapper}",
                           ],
                        ]
                     ); ?>
                     <!-- Photo -->
                     <div class="inlinerow">
                        <div class="col-md-2 col-sm-2 col-md-offset-3 col-sm-offset-3">
                           <?php if (!empty($formModel->institutionlogo)) { ?>
                              <?= Html::img(
                                 yii::$app->params['imagePath'] . $formModel->institutionlogo,
                                 ['alt' => 'Institution logo', 'style' => "width: 160px; border-radius: 25px; height: 150px;", 'id' => 'institutionImage']
                              ) ?>
                           <?php } else { ?>
                              <img style="width: 160px; border-radius: 25px; height: 150px;" class="form-control" id='institutionImage' src="<?php echo $assetName->baseUrl; ?>/theme/images/institution-icon-grey.png" />
                           <?php  } ?>
                        </div>
                        <div class="col-md-4 col-sm-4 Mtop50">
                           <div class="inlinerow">Please upload an image</div>
                           <div class="inlinerow Mtop15">
                              <?= $form->field($formModel, 'institutionlogo')->fileInput(['class' => 'form-control', 'id' => 'institution-logo'])->label(false); ?>
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
                              <div class="col-md-5 col-sm-5">Time Zone <span style="color: red;">*</span></div>
                              <div class="col-md-7 col-sm-7">
                                 <?= $form->field($formModel, 'timezone')->dropDownList(
                                    $formModel->getTimeZone(),
                                    [
                                       'prompt' => 'Please Select'
                                    ]
                                 )
                                    ->label(false);
                                 ?>
                              </div>
                           </div>
                           <div class="inner-rows">
                              <div class="col-md-5 col-sm-5">Institution Type <span style="color: red;">*</span></div>
                              <div class="col-md-7 col-sm-7">
                                 <?= $form->field($formModel, 'institutiontype')->dropDownList(
                                    $formModel->getInstitutionTypes(),
                                    [
                                       'prompt' => 'Please Select',
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
                           <div class="inner-rows MBotton40">
                              <div class="col-md-5 col-sm-5">Demo Institution</div>
                              <div class="col-md-7 col-sm-7">
                                 <?= $form->field($formModel, 'demo')->checkbox(array('label' => '', 'id' => 'checkbox-demo', 'class' => 'Mleft5'))->label(false); ?>
                              </div>
                           </div>
                           <div class="inner-rows" id="demo-div" style="display:none;">
                              <div class="col-md-5 col-sm-5">Demo Expiry Date</div>
                              <div class="col-md-7 col-sm-7">
                                 <?= DatePicker::widget([
                                    'name' => 'ExtendedInstitution[demo_expiry]',
                                    'value' => $formModel->demo_expiry ? date(yii::$app->params['dateFormat']['viewDateFormat'], strtotimeNew($formModel->demo_expiry)) : '',
                                    'options' => [
                                       'placeholder' => 'Select Expiry Date',
                                       'readonly' => true,
                                    ],
                                    'pluginOptions' => [
                                       'autoclose' => true,
                                       'format' => 'dd MM yyyy',
                                       'startDate' => 'today',
                                    ]
                                 ]); ?>
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
                                 <?= $form->field($formModel, 'countryid')->dropDownList(
                                    $formModel->getCountry(),
                                    [
                                       'prompt' => 'Please Select'
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
                                 <?= $form->field($formModel, 'phone1_countrycode')->textInput(['maxlength' => 4, 'class' => 'citycodes form-control number-check'])->label(false) ?>
                                 <?= $form->field($formModel, 'phone1_areacode')->textInput(['maxlength' => 5, 'class' => 'citycodes form-control number-check'])->label(false) ?>
                                 <?= $form->field($formModel, 'phone1')->textInput(['maxlength' => 7, 'class' => 'fullnumber form-control number-check'])->label(false); ?>
                                 <div class="error-phone1 msg-div  error-div" style="display:none">Invalid Telephone Number</div>
                              </div>
                           </div>
                           <div class="inner-rows">
                              <div class="col-md-5 col-sm-5">Mobile Number </div>
                              <div class="col-md-7 col-sm-7 phone-div">
                                 <?= $form->field($formModel, 'phone2_countrycode')->textInput(['maxlength' => 4, 'class' => 'citycodes form-control number-check'])->label(false) ?>
                                 <?= $form->field($formModel, 'phone2')->textInput(['maxlength' => 10, 'class' => 'fullnumber-affiliated form-control number-check'])->label(false); ?>
                                 <div class="error-phone2 msg-div  error-div" style="display:none">Invalid Mobile Number</div>
                              </div>
                           </div>
                           <div class="inner-rows">
                              <div class="col-md-5 col-sm-5">Tag Cloud <span style="color: red;">*</span></div>
                              <div class="col-md-7 col-sm-7">
                                 <?= $form->field($formModel, 'tagcloud')->dropDownList(
                                    [
                                       1 => 'Yes',
                                       0 => 'No'
                                    ],
                                    [
                                       'prompt' => 'Please Select',
                                       'options' => [
                                          ($formModel->tagcloud == 1) ? $formModel->tagcloud : 0 => array('selected' => true)
                                       ]
                                    ]
                                 )
                                    ->label(false);
                                 ?>
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

                        <div class="inlinerow">
                           <legend style="font-size: 20px;">Settings</legend>
                           <div class="inlinerow">
                              <div class="col-md-3 col-sm-3">
                                 <?= $form->field($formModel, 'feedbackenabled')->checkbox() ?>
                              </div>
                              <div class="col-md-3 col-sm-3">
                                 <?= $form->field($formModel, 'paymentoptionenabled')->checkbox() ?>
                              </div>
                              <div class="col-md-3 col-sm-3">
                                 <?= $form->field($formModel, 'prayerrequestenabled')->checkbox() ?>
                              </div>
                              <div class="col-md-3 col-sm-3">
                                 <?= $form->field($formModel, 'isrotary')->checkbox() ?>
                              </div>
                              <div class="col-md-3 col-sm-3">
                                 <?= $form->field($formModel, 'advancedsearchenabled')->checkbox() ?>
                              </div>
                              <div class="col-md-3 col-sm-3">
                                 <?= $form->field($formModel, 'moreenabled')->checkbox(
                                    [
                                       'id' => 'checkbox-more'
                                    ]
                                 ) ?>
                              </div>
                           </div>
                           <div class="col-md-12 col-sm-12 Mtop20">
                              <?= $form->field($formModel, 'moreurl')->textInput(
                                 [
                                    'maxlength' => true,
                                    'style' => 'display:none',
                                    'id' => 'more-text'
                                 ]
                              )->label(false); ?>
                           </div>
                        </div>
                        <div class="inlinerow Mtop30 text-center">
                           <?= Html::submitButton('Save', ['class' =>  'btn btn-success save-institution', 'title' => 'Save']) ?>
                        </div>
                        <?php ActiveForm::end(); ?>
                     </div>
               </fieldset>
            </div>
            <div role="tabpanel" class="tab-pane fade" id="manage_survey">
               <!-- Employee Listing Table -->
               <?php $form = ActiveForm::begin(
                  [
                     'action' => ['institution/create-survey-credentials'],
                     'options' => [
                        'id' => 'form_2'
                     ]
                  ]
               ); ?>
               <fieldset>
                  <!-- Section head -->
                  <div class="blockrow Mtop50">
                     <div class="col-md-6 col-md-offset-2 col-sm-7">
                        <div class="inner-rows">
                           <div class="col-md-5 col-sm-5"></div>
                           <div class="col-md-7 col-sm-7">
                              <?= $form->field($surveyFormModel, 'username')->textInput(['maxlength' => true])->label('Username<span style="color: red;">*</span>'); ?>
                           </div>
                        </div>
                        <div class="inner-rows">
                           <div class="col-md-5 col-sm-5"></div>
                           <div class="col-md-7 col-sm-7">
                              <?= $form->field($surveyFormModel, 'password')->textInput(['maxlength' => true])->label('Password<span style="color: red;">*</span>'); ?>
                              <?= Html::activeHiddenInput($surveyFormModel, 'institutionid') ?>
                           </div>
                        </div>
                        <div class="inlinerow Mtop10 text-center">
                           <div class="col-md-5 col-sm-5"></div>
                           <?= Html::submitButton('Save', ['class' =>  'btn btn-primary', 'title' => 'Save']) ?>
                        </div>
                     </div>
                  </div>
                  <!-- /. Blockrow closed -->
               </fieldset>
               <?php ActiveForm::end(); ?>
            </div>
            <!-- start -->
            <?php if (!empty($dashboardItem) && count($dashboardItem) > 0) { ?>
               <!-- Dashboard tab -->
               <div role="tabpanel" class="tab-pane" id="manage_dashboard">
                  <div class="row contentbg">
                     <div class="inlinerow Mtop20">
                        <!-- Dashboard Icons -->
                        <div class="col-md-7 col-sm-6">
                           <div class="previewheads">Dashboard Icons</div>
                           <div class="inlinerow Mtop25">
                              <!-- Icons -->
                              <?php $i = 0; ?>
                              <?php foreach ($dashboardItem as $key => $item) {
                                 $length = count($dashboardItem) - 1;
                                 $previous = $i - 1;
                                 $next = $i + 1;
                                 $imgid = "img_" . $i;
                                 $label = "lab_" . $i;
                              ?>
                                 <div class="col-md-3 col-sm-6">
                                    <?php if ($item['isactive'] == 1) { ?>
                                       <div class="iconspanel dbicon dashenable" activestatus='<?= $item['isactive'] ?>' order="<?= $item['sortorder'] ?>" id="<?= $i ?>">
                                          <div class="iconnum"><?= $item['sortorder'] ?></div>
                                          <div class="sorticonbox">
                                             <?php if ($i == 0) { ?>
                                                <div class="sortlinks">&nbsp;</div>
                                             <?php } else { ?>
                                                <div class="sortlinks preicon" currentid="<?= $i ?>" previousid="<?= $previous ?>">
                                                   <img src="<?php echo $assetName->baseUrl; ?>/theme/images/sort-green.png" />
                                                </div>
                                             <?php } ?>
                                             <div class="sorticons">
                                                <div class="inlinerow" id="<?= $imgid ?>">
                                                   <?= Html::img(
                                                      yii::$app->params['imagePath'] . '/' . $item['imageurl'],
                                                      [
                                                         'style' => "width: 100%;",
                                                         'id' => 'ImageURL',
                                                         'dashboardid' => $item['dashboardid']
                                                      ]
                                                   ) ?>
                                                </div>
                                                <div id="<?= $label ?>" class="iconlabels"><?= $item['description'] ?></div>
                                             </div>
                                             <?php
                                             if ($i == $length) { ?>
                                                <div class="sortlinks">&nbsp;</div>
                                             <?php } else { ?>
                                                <div class="sortlinks nexticon" currentid="<?= $i ?>" nextid="<?= $next ?>">
                                                   <img src="<?php echo $assetName->baseUrl; ?>/theme/images/sort-red.png" />
                                                </div>
                                             <?php } ?>
                                          </div>
                                          <div class="iconenable dbstatus">Enabled</div>
                                       </div>
                                    <?php } else {
                                    ?>
                                       <div class="iconspanel-disable dbicon dashdisable" activestatus='<?= $item['isactive'] ?>' order="<?= $item['sortorder'] ?>" id="<?= $i ?>">
                                          <div class="iconnum"><?= $item['sortorder'] ?></div>
                                          <div class="sorticonbox">
                                             <?php if ($i == 0) { ?>
                                                <div class="sortlinks">&nbsp;</div>
                                             <?php } else { ?>
                                                <div class="sortlinks preicon" currentid="<?= $i ?>" previousid="<?= $previous ?>">
                                                   <img src="<?php echo $assetName->baseUrl; ?>/theme/images/sort-green.png" />
                                                </div>
                                             <?php } ?>
                                             <div class="sorticons">
                                                <div class="inlinerow" id="<?= $imgid ?>">
                                                   <?= Html::img(
                                                      yii::$app->params['imagePath'] . '/' . $item['imageurl'],
                                                      [
                                                         'style' => "width: 100%;",
                                                         'id' => 'ImageURL',
                                                         'dashboardid' => $item['dashboardid']
                                                      ]
                                                   ) ?>
                                                </div>
                                                <div id="<?= $label ?>" class="iconlabels"><?= $item['description'] ?></div>
                                             </div>
                                             <?php
                                             if ($i != $length) { ?>
                                                <div class="sortlinks nexticon" currentid="<?= $i ?>" nextid="<?= $next ?>">
                                                   <img src="<?php echo $assetName->baseUrl; ?>/theme/images/sort-red.png" />
                                                </div>
                                             <?php } else { ?>
                                                <div class="sortlinks">&nbsp;</div>
                                             <?php } ?>
                                          </div>
                                          <div class="icondisable dbstatus">Disabled</div>
                                       </div>
                                    <?php } ?>
                                 </div>
                                 <?php $i++; ?>
                              <?php  } ?>
                           </div>
                           <div class="inlinerow Mtop50 text-center">
                              <input type="hidden" id="hdnInstitutionId" value="<?= $formModel->id ?>" />
                              <input id="save-dashboard-item" type="button" class="btn btn-success" value="Save" title="save" />
                           </div>
                        </div>
                        <!-- Preview -->
                        <div class="col-md-5 col-sm-6">
                           <div class="previewheads"></div>
                           <div class="mobilepreview Mtop30">
                              <div class="mobilebox">
                                 <!-- mobile -->
                                 <div class="inlinerow contentbg-mobile">
                                    <!-- Header -->
                                    <div class="topheader">
                                       <div class="backbtn">
                                          <img class="backimg" src="<?php echo $assetName->baseUrl; ?>/theme/images/back.png">
                                       </div>
                                       <div class="clubname">CLUB Name</div>
                                    </div>
                                    <!-- /.Header -->
                                    <!-- Content 1 -->
                                    <div id="contentbox1">
                                       <div id="contentinsidebox1" class="contentbox">
                                          <?php $j = 0;
                                          $k = 0;
                                          ?>
                                          <?php foreach ($dashboardItem as $key => $item) {
                                             $mlength = count($dashboardItem) - 1;
                                             $mcurrentid = "mob_" . $j;
                                          ?>
                                             <?php if ($k < 9) {
                                             ?>
                                                <?php if ($item['isactive'] == 1) { ?>
                                                   <div class="col-xs-4 dashiconbox mobicon dashenable" id="<?= $mcurrentid ?>" order="<?= $j ?>">
                                                      <?= Html::img(
                                                         yii::$app->params['imagePath'] . '/' . $item['iconurl'],
                                                         []
                                                      ) ?>
                                                   </div>
                                                <?php $k++;
                                                } else { ?>
                                                   <div class="col-xs-4 dashiconbox mobicon nodisplay dashdisable" id="<?= $mcurrentid ?>" order="<?= $j ?>">
                                                      <?= Html::img(
                                                         yii::$app->params['imagePath'] . '/' . $item['iconurl'],
                                                         []
                                                      ) ?>
                                                   </div>
                                                <?php }
                                             } else { ?>
                                                <div class="col-xs-4 dashiconbox mobicon nodisplay dashdisable" id="<?= $mcurrentid ?>" order="<?= $j ?>">
                                                   <?= Html::img(
                                                      yii::$app->params['imagePath'] . '/' . $item['iconurl'],
                                                      []
                                                   ) ?>
                                                </div>
                                             <?php } ?>
                                          <?php $j++;
                                          } ?>
                                       </div>
                                       <!-- Footer 1 -->
                                       <div class="footerbox">
                                          <div class="col-xs-12">
                                             <div class="col-xs-3">
                                                <div class="inlinerow text-center">
                                                   <img src="<?php echo $assetName->baseUrl; ?>/theme/images/home-icon.png">
                                                </div>
                                                <div class="footericon-txt">HOME</div>
                                             </div>
                                             <div class="col-xs-3">
                                                <div class="inlinerow text-center">
                                                   <img src="<?php echo $assetName->baseUrl; ?>/theme/images/about-icon.png">
                                                </div>
                                                <div class="footericon-txt">ABOUT</div>
                                             </div>
                                             <div class="col-xs-3">
                                                <div class="inlinerow text-center">
                                                   <img src="<?php echo $assetName->baseUrl; ?>/theme/images/you-icon.png">
                                                </div>
                                                <div class="footericon-txt">YOU</div>
                                             </div>
                                             <div class="col-xs-3">
                                                <div class="inlinerow text-center">
                                                   <img src="<?php echo $assetName->baseUrl; ?>/theme/images/settings-icon.png">
                                                </div>
                                                <div class="footericon-txt">SETTINGS</div>
                                             </div>
                                          </div>
                                       </div>
                                       <!-- /.Footer -->
                                    </div>
                                    <!-- Content 1 -->
                                    <!-- Content 2 -->
                                    <div id="contentbox2" class="nodisplay">
                                       <div id="contentinsidebox2" class="contentbox">
                                          <?php $l = 0;
                                          $isActiveCount = 0;
                                          array_walk_recursive($dashboardItem, function ($item, $key) use (&$isActiveCount) {
                                             if ($key == 'isactive' && $item == 1) {
                                                $isActiveCount++;
                                             }
                                          }); ?>
                                          <?php foreach ($dashboardItem as $key => $item) {
                                             $length = $isActiveCount;
                                             $currentid = "mob2_" . $l; ?>
                                             <?php if ($k <= $l && $l > 8 && $length > 9) { ?>
                                                <?php if ($item['isactive'] == 1) { ?>
                                                   <div class="col-xs-4 dashiconbox mobicon dashenable" id="<?= $currentid ?>" order="<?= $l ?>">
                                                      <?= Html::img(
                                                         yii::$app->params['imagePath'] . '/' . $item['iconurl'],
                                                         []
                                                      ) ?>
                                                   </div>
                                                <?php } else { ?>
                                                   <div class="col-xs-4 dashiconbox mobicon nodisplay dashdisable" id="<?= $currentid ?>" order="<?= $l ?>">
                                                      <?= Html::img(
                                                         yii::$app->params['imagePath'] . '/' . $item['iconurl'],
                                                         []
                                                      ) ?>
                                                   </div>
                                                <?php }
                                             } else { ?>
                                                <div class="col-xs-4 dashiconbox mobicon nodisplay dashdisable" id="<?= $currentid ?>" order="<?= $l ?>">
                                                   <?= Html::img(
                                                      yii::$app->params['imagePath'] . '/' . $item['iconurl'],
                                                      []
                                                   ) ?>
                                                </div>
                                             <?php }
                                             ?>
                                          <?php $l++;
                                          } ?>
                                       </div>
                                       <!-- Footer 2 -->
                                       <div class="footerbox">
                                          <div class="col-xs-12">
                                             <div class="col-xs-3">
                                                <div class="inlinerow text-center">
                                                   <img src="<?php echo $assetName->baseUrl; ?>/theme/images/home-icon.png">
                                                </div>
                                                <div class="footericon-txt">HOME</div>
                                             </div>
                                             <div class="col-xs-3">
                                                <div class="inlinerow text-center">
                                                   <img src="<?php echo $assetName->baseUrl; ?>/theme/images/about-icon.png">
                                                </div>
                                                <div class="footericon-txt">ABOUT</div>
                                             </div>
                                             <div class="col-xs-3">
                                                <div class="inlinerow text-center">
                                                   <img src="<?php echo $assetName->baseUrl; ?>/theme/images/you-icon.png">
                                                </div>
                                                <div class="footericon-txt">YOU</div>
                                             </div>
                                             <div class="col-xs-3">
                                                <div class="inlinerow text-center">
                                                   <img src="<?php echo $assetName->baseUrl; ?>/theme/images/settings-icon.png">
                                                </div>
                                                <div class="footericon-txt">SETTINGS</div>
                                             </div>
                                          </div>
                                       </div>
                                       <!-- /.Footer -->
                                    </div>
                                    <!-- Content 2 -->
                                    <!-- mobile -->
                                 </div>
                              </div>
                           </div>
                           <!-- Pagination -->
                           <div class="inlinerow text-center Mtop30">
                              <input id="pageleft" type="button" class="pageleft" />
                              <input id="pageright" type="button" class="pageright" />
                           </div>
                           <!-- /.Pagination -->
                        </div>
                     </div>
                  </div>
               </div>
               <!-- Dashboard tab -->
            <?php } ?>
            <!-- end -->
         </div>
      </div>
   </div>
</div>
<!-- /. Blockrow closed -->
</div>
<!-- /. Blockrow closed -->
</div>
</div>
<!-- Contents closed -->