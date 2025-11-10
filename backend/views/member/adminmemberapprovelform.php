<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\assets\AppAsset;
use common\models\extendedmodels\ExtendedInstitution;

$assetName = AppAsset::register($this);

$this->registerJsFile(
   $assetName->baseUrl . '/theme/js/Remember.memberApproval.ui.js?v=1.1',
   [
      'depends' => [
         AppAsset::className()
      ]
   ]
);

echo Html::hiddenInput(
   'base-url',
   $assetName->baseUrl,
   [
      'id' => 'base-url'
   ]
);


echo Html::hiddenInput(
   'store-pending-details',
   \Yii::$app->params['ajaxUrl']['store-pending-details'],
   [
      'id' => 'store-pending-details'
   ]
);

echo Html::hiddenInput(
   'totalModified',
   $totalModified ?? 0,
   [
      'id' => 'totalModified',
   ]
);


?>
<div class="col-md-12 col-sm-12 col-xs-12 contentbg Mtop15">
   <div class="row Mtopbot20">
      <?php $form = ActiveForm::begin(
         [
            'options' => [
               'enctype' => 'multipart/form-data',
               'id' => 'form',
            ],
            'enableClientValidation' => false
         ]
      );
      ?>
      <?= $form->field($model, 'memberid')->hiddenInput(['id' => 'hdnmemberid'])->label(false) ?>
      <?= $form->field($model, 'institutionid')->hiddenInput()->label(false) ?>
      <input type='hidden' value="<?= $countDependant ?>" id="HiddenDependantCount">
      <?= $form->field($model, 'member_mobile1')->hiddenInput(['id' => 'txtMemberNo'])->label(false) ?>
      <div class="col-md-6 col-sm-6 col-xs-12 Mtop20">
         <fieldset>
            <legend>Member Details</legend>
            <div class="blockrow">
               <div class="col-md-5 col-sm-5 col-xs-12 imagesize">
                  <img class="fix-size" width="184" height="184" id="tempmemberimage" src="<?php
                                                                                             $image  = $tempMember->temp_member_pic ? $tempMember->temp_member_pic : "/Member/default-user.png";
                                                                                             echo Yii::$app->params['imagePath'] . $image; ?>"
                     isapproved="<?php echo $tempMember->getPendingInfo($tempMember->temp_member_pic, $model->member_pic) ? 'False' : 'undefined' ?>" 'class'='form-control w80p <?= $tempMember->getPendingInfo($tempMember->temp_member_pic, $model->member_pic) ?>'>
               </div>
               <?php if ($tempMember->getPendingInfo($tempMember->temp_member_pic, $model->member_pic)) { ?>
                  <div class="col-md-7 col-sm-7">
                     <input type="button" class="infobtn" id="memberimageinfo" isapproved="<?php echo $tempMember->getPendingInfo($tempMember->temp_member_pic, $model->member_pic) ? 'False' : 'True' ?>">
                     <!-- approval box -->
                     <div class="approvalbox nodisplay">
                        <div class="prevdatahead">Previous Data</div>
                        <div class="prevdata">
                           <img class="fix-size" width="184" height="184" src="<?php
                                                                                 $image  = $model->member_pic ? $model->member_pic : "/Member/default-user.png";
                                                                                 echo Yii::$app->params['imagePath'] . $image; ?> " id="memberpic" />
                        </div>
                        <div class="inlinerow text-center">
                           <input type="button" class="approvebtn" value="Approve">
                           <input type="button" class="rejectbtn" value="Reject">
                        </div>
                     </div>
                     <!-- /.approval box -->
                  </div>
               <?php } ?>
            </div>
            <!-- rows -->
            <div class="inlinerow Mtop10">
               <div class="col-md-4 col-sm-5 col-xs-12 L32">Title<span style="color: red;">*</span></div>
               <div class="col-md-8 col-sm-7 col-xs-12">
                  <?= Html::activeTextInput(
                     $model,
                     'membertitle',
                     [
                        'maxlength' => true,
                        'id' => 'txtMemberTitle',
                        'class' => "form-control w80p " . $tempMember->getPendingInfo($tempMember->tempmembertitle0['Description'], $model->membertitle0['Description']),
                        "isapproved" => $tempMember->getPendingInfo($tempMember->tempmembertitle0['Description'], $model->membertitle0['Description']) ? 'False' : 'True',
                        'value' => $tempMember->tempmembertitle0['Description'],
                        'disabled' => true
                     ]
                  ); ?>
                  <?php if ($tempMember->getPendingInfo($tempMember->tempmembertitle0['Description'], $model->membertitle0['Description'])) { ?>
                     <input class="infobtn" isapproved="true" type="button">
                     <input type="hidden" id="MemberTitle" value='<?= $model->membertitle0['Description'] ?>'>
                     <?= $tempMember->getDetails($model->membertitle0['Description']) ?>
                  <?php } ?>
               </div>
            </div>
            <!--/.rows -->
            <!-- rows -->
            <div class="inlinerow Mtop10">
               <div class="col-md-4 col-sm-5 col-xs-12 L32">First Name <span style="color: red;">*</span></div>
               <div class="col-md-8 col-sm-7 ">
                  <?= Html::activeTextInput(
                     $model,
                     'firstName',
                     [
                        'maxlength' => true,
                        'id' => 'txtFirstName',
                        'class' => "form-control w80p " . $tempMember->getPendingInfo($tempMember->temp_firstName, $model->firstName),
                        "isapproved" => $tempMember->getPendingInfo($tempMember->temp_firstName, $model->firstName) ? 'False' : 'True',
                        'value' => $tempMember->temp_firstName,
                        'disabled' => true
                     ]
                  ); ?>
                  <?php if ($tempMember->getPendingInfo($tempMember->temp_firstName, $model->firstName)) { ?>
                     <input class="infobtn" isapproved="true" type="button">
                     <?= $tempMember->getDetails($model->firstName) ?>
                  <?php } ?>
               </div>
            </div>
            <!--/.rows -->
            <!-- rows -->
            <div class="inlinerow Mtop10">
               <div class="col-md-4 col-sm-5 col-xs-12 L32">Middle Name</div>
               <div class="col-md-8 col-sm-7 col-xs-12 col-xs-12">
                  <?= Html::activeTextInput(
                     $model,
                     'middleName',
                     [
                        'maxlength' => true,
                        'id' => 'txtMiddleName',
                        'class' => "form-control w80p " . $tempMember->getPendingInfo($tempMember->temp_middleName, $model->middleName),
                        "isapproved" => $tempMember->getPendingInfo($tempMember->temp_middleName, $model->middleName) ? 'False' : 'True',
                        'value' => $tempMember->temp_middleName,
                        'disabled' => true
                     ]
                  ); ?>
                  <?php if ($tempMember->getPendingInfo($tempMember->temp_middleName, $model->middleName)) { ?>
                     <input class="infobtn" isapproved="true" type="button">
                     <?= $tempMember->getDetails($model->middleName) ?>
                  <?php } ?>
               </div>
            </div>
            <!--/.rows -->
            <!-- rows -->
            <div class="inlinerow Mtop10">
               <div class="col-md-4 col-sm-5 col-xs-12 L32">Last Name <span style="color: red;">*</span></div>
               <div class="col-md-8 col-sm-7 col-xs-12">
                  <?= Html::activeTextInput(
                     $model,
                     'lastName',
                     [
                        'maxlength' => true,
                        'id' => 'txtLastName',
                        'class' => "form-control w80p " . $tempMember->getPendingInfo($tempMember->temp_lastName, $model->lastName),
                        "isapproved" => $tempMember->getPendingInfo($tempMember->temp_lastName, $model->lastName) ? 'False' : 'True',
                        'value' => $tempMember->temp_lastName,
                        'disabled' => true
                     ]
                  ); ?>
                  <?php if ($tempMember->getPendingInfo($tempMember->temp_lastName, $model->lastName)) { ?>
                     <input class="infobtn" isapproved="true" type="button">
                     <?= $tempMember->getDetails($model->lastName) ?>
                  <?php } ?>
               </div>
            </div>
            <!--/.rows -->
            <!-- rows -->
            <div class="inlinerow Mtop10">
               <div class="col-md-4 col-sm-5 col-xs-12 L32">Nick/AKA</div>
               <div class="col-md-8 col-sm-7 col-xs-12">
                  <?= Html::activeTextInput(
                     $model,
                     'membernickname',
                     [
                        'maxlength' => true,
                        'id' => 'txtMemberNickName',
                        'class' => "form-control w80p " . $tempMember->getPendingInfo($tempMember->temp_membernickname, $model->membernickname),
                        "isapproved" => $tempMember->getPendingInfo($tempMember->temp_membernickname, $model->membernickname) ? "False" : "True",
                        'value' => $tempMember->temp_membernickname,
                        'disabled' => true
                     ]
                  ); ?>
                  <?php if ($tempMember->getPendingInfo($tempMember->temp_membernickname, $model->membernickname)) { ?>
                     <input class="infobtn" isapproved="true" type="button">
                     <?= $tempMember->getDetails($model->membernickname) ?>
                  <?php } ?>
               </div>
            </div>
            <!--/.rows -->
            <!-- rows -->
            <div class="inlinerow Mtop10">
               <div class="col-md-4 col-sm-5 col-xs-12 L32">Email </div>
               <div class="col-md-8 col-sm-7 col-xs-12">
                  <?= Html::activeTextInput(
                     $model,
                     'member_email',
                     [
                        'maxlength' => true,
                        'id' => 'txtMemberEmail',
                        'class' => "form-control w80p " . $tempMember->getPendingInfo($tempMember->temp_member_email, $model->member_email),
                        "isapproved" => $tempMember->getPendingInfo($tempMember->temp_member_email, $model->member_email) ? "False" : "True",
                        'value' => $tempMember->temp_member_email,
                        'disabled' => true
                     ]
                  ); ?>
                  <?php if ($tempMember->getPendingInfo($tempMember->temp_member_email, $model->member_email)) { ?>
                     <input class="infobtn" isapproved="true" type="button">
                     <?= $tempMember->getDetails($model->member_email) ?>
                  <?php } ?>
               </div>
            </div>
            <div class="inlinerow Mtop10">
               <div class="col-md-4 col-sm-5 col-xs-12 L32">DOB</div>
               <div class="col-md-8 col-sm-7 col-xs-12">
                  <?php
                  $memberDob = $model->member_dob ? date(yii::$app->params['dateFormat']['viewDateFormat'], strtotimenew($model->member_dob)) : '';
                  $newMemberDob = $tempMember->temp_member_dob ? date(yii::$app->params['dateFormat']['viewDateFormat'], strtotimenew($tempMember->temp_member_dob)) : '';
                  ?>
                  <?= Html::activeTextInput(
                     $model,
                     'member_dob',
                     [
                        'maxlength' => true,
                        'id' => 'txtMemberDOB',
                        'class' => "form-control w80p " . $tempMember->getPendingInfo($newMemberDob, $memberDob),
                        "isapproved" => $tempMember->getPendingInfo($newMemberDob, $memberDob) ? "False" : "True",
                        'value' => $newMemberDob,
                        'disabled' => true
                     ]
                  ); ?>
                  <?php if ($tempMember->getPendingInfo($newMemberDob, $memberDob)) { ?>
                     <input class="infobtn" isapproved="true" type="button">
                     <?= $tempMember->getDetails($memberDob) ?>
                  <?php } ?>
               </div>
            </div>
            <!--/.rows -->
            <?php if (strtolower(ExtendedInstitution::getInstitutionTypeNameFromCode($model->institution->institutiontype)) == "church") { ?>
               <input type="hidden" id="HiddenInstitutionType" value="2">
               <div class="inlinerow Mtop10">
                  <div class="col-md-4 col-sm-5 L32">Home church</div>
                  <div class="col-md-8 col-sm-7">
                     <?= Html::activeTextInput(
                        $model,
                        'homechurch',
                        [
                           'maxlength' => true,
                           'id' => 'txtHomeChurch',
                           'class' => "form-control w80p " . $tempMember->getPendingInfo($tempMember->temp_homechurch, $model->homechurch),
                           "isapproved" => $tempMember->getPendingInfo($tempMember->temp_homechurch, $model->homechurch) ? "False" : "True",
                           'value' => $tempMember->temp_homechurch,
                           'disabled' => true
                        ]
                     ); ?>
                     <?php if ($tempMember->getPendingInfo($tempMember->temp_homechurch, $model->homechurch)) { ?>
                        <input class="infobtn" isapproved="true" type="button">
                        <?= $tempMember->getDetails($model->homechurch) ?>
                     <?php } ?>
                  </div>
               </div>
            <?php } else { ?>
               <input type="hidden" id="HiddenInstitutionType" value="1">
            <?php } ?>

            <div class="inlinerow Mtop10">
               <div class="col-md-4 col-sm-5 L32">Occupation</div>
               <div class="col-md-8 col-sm-7">
                  <?= Html::activeTextInput(
                     $model,
                     'occupation',
                     [
                        'maxlength' => true,
                        'id' => 'txtOccupation',
                        'class' => "form-control w80p " . $tempMember->getPendingInfo($tempMember->temp_occupation, $model->occupation),
                        "isapproved" => $tempMember->getPendingInfo($tempMember->temp_occupation, $model->homechurch) ? "False" : "True",
                        'value' => $tempMember->temp_occupation,
                        'disabled' => true
                     ]
                  ); ?>
                  <?php if ($tempMember->getPendingInfo($tempMember->temp_occupation, $model->occupation)) { ?>
                     <input class="infobtn" isapproved="true" type="button">
                     <?= $tempMember->getDetails($model->occupation) ?>
                  <?php } ?>
               </div>
            </div>
            <div class="inlinerow Mtop10">
               <div class="col-md-4 col-sm-5 L32">Blood Group </div>
               <div class="col-md-8 col-sm-7">
                  <?= Html::activeTextInput(
                     $model,
                     'memberbloodgroup',
                     [
                        'maxlength' => true,
                        'id' => 'txtMemberBloodGroup',
                        'class' => "form-control w80p " . $tempMember->getPendingInfo($tempMember->tempmemberBloodGroup, $model->memberbloodgroup),
                        "isapproved" => $tempMember->getPendingInfo($tempMember->tempmemberBloodGroup, $model->memberbloodgroup) ? "False" : "True",
                        'value' => $tempMember->tempmemberBloodGroup,
                        'disabled' => true
                     ]
                  ); ?>
                  <?php if ($tempMember->getPendingInfo($tempMember->tempmemberBloodGroup, $model->memberbloodgroup)) { ?>
                     <input class="infobtn" isapproved="true" type="button">
                     <?= $tempMember->getDetails($model->memberbloodgroup) ?>
                  <?php } ?>
               </div>
            </div>
            <?php if ($model->institution->tagcloud) { ?>
               <div class="inlinerow Mtop10">
                  <div class="col-md-4 col-sm-5 L32">TagCloud</div>
                  <div class="col-md-8 col-sm-7">
                     <?= Html::activeTextInput(
                        $memberadditionalModal,
                        'tagcloud',
                        [
                           'maxlength' => true,
                           'id' => 'txtTagCloud',
                           'class' => "form-control w80p " . $tempMember->getPendingInfo($tempMemberadditionalModal->temptagcloud, $memberadditionalModal->tagcloud),
                           "isapproved" => $tempMember->getPendingInfo($tempMemberadditionalModal->temptagcloud, $memberadditionalModal->tagcloud) ? "False" : "True",
                           'value' => $tempMemberadditionalModal->temptagcloud,
                           'disabled' => true
                        ]
                     ); ?>
                     <?php if ($tempMember->getPendingInfo($tempMemberadditionalModal->temptagcloud, $memberadditionalModal->tagcloud)) { ?>
                        <input class="infobtn" isapproved="true" type="button">
                        <?= $tempMember->getDetails($memberadditionalModal->tagcloud) ?>
                     <?php } ?>
                  </div>
               </div>
            <?php } ?>
         </fieldset>
      </div>
      <!-- /.Section 1 -->
      <!-- Section 2 -->
      <div class="col-md-6 col-sm-6 col-xs-12 Mtop20">
         <fieldset>
            <legend>Spouse Details</legend>
            <div class="blockrow">
               <div class="col-md-5 col-sm-5 col-xs-12 imagesize"> <img class="fix-size" width="184" height="184" id='tempspouseimage'
                     src="<?php
                           $image  = $tempMember->temp_spouse_pic ? $tempMember->temp_spouse_pic : "/Member/default-user.png";
                           echo Yii::$app->params['imagePath'] . $image; ?>"
                     isapproved="<?php echo $tempMember->getPendingInfo($tempMember->temp_spouse_pic, $model->spouse_pic) ? 'False' : 'undefined' ?>" 'class'='form-control w80p <?= $tempMember->getPendingInfo($tempMember->temp_spouse_pic, $model->spouse_pic) ?>'> </div>
               <?php if ($tempMember->getPendingInfo($tempMember->temp_spouse_pic, $model->spouse_pic)) { ?>
                  <div class="col-md-7 col-sm-7">
                     <input type="button" class="infobtn" id="spouseimageinfo" isapproved="<?php echo $tempMember->getPendingInfo($tempMember->temp_spouse_pic, $model->spouse_pic) ? 'False' : 'True' ?>">
                     <!-- approval box -->
                     <div class="approvalbox nodisplay">
                        <div class="prevdatahead">Previous Data</div>
                        <div class="prevdata">
                           <img class="fix-size" width="184" height="184" src="<?php
                                                                                 $image  = $model->spouse_pic ? $model->spouse_pic : "/Member/default-user.png";
                                                                                 echo Yii::$app->params['imagePath'] . $image; ?> " id="spouseimage" />
                        </div>
                        <div class="inlinerow text-center">
                           <input type="button" class="approvebtn" value="Approve">
                           <input type="button" class="rejectbtn" value="Reject">
                        </div>
                     </div>
                     <!-- /.approval box -->
                  </div>
               <?php } ?>
            </div>
            <!-- rows -->
            <div class="inlinerow Mtop10">
               <div class="col-md-4 col-sm-5 col-xs-12 L32">Title</div>
               <div class="col-md-8 col-sm-7 col-xs-12">
                  <?= Html::activeTextInput(
                     $model,
                     'spousetitle',
                     [
                        'maxlength' => true,
                        'id' => 'txtSpouseTitle',
                        'class' => "form-control w80p " . $tempMember->getPendingInfo(($tempMember->tempspousetitle0['Description'] ?? ''), ($model->spousetitle0['Description'] ?? '')),
                        "isapproved" => $tempMember->getPendingInfo(($tempMember->tempspousetitle0['Description'] ?? ''), ($model->spousetitle0['Description'] ?? '')) ? 'False' : 'True',
                        'value' => ($tempMember->tempspousetitle0['Description'] ?? ''),
                        'disabled' => true
                     ]
                  ); ?>
                  <?php if ($tempMember->getPendingInfo(($tempMember->tempspousetitle0['Description'] ?? ''), ($model->spousetitle0['Description'] ?? ''))) { ?>
                     <input class="infobtn" isapproved="true" type="button">
                     <input type="hidden" id="MemberTitle" value='<?= ($model->spousetitle0['Description'] ?? '') ?>'>
                     <?= $tempMember->getDetails(($model->spousetitle0['Description'] ?? '')) ?>
                  <?php } ?>
               </div>
            </div>
            <!-- rows -->
            <!-- rows -->
            <div class="inlinerow Mtop10">
               <div class="col-md-4 col-sm-5 col-xs-12 L32">First Name</div>
               <div class="col-md-8 col-sm-7 col-xs-12">
                  <?= Html::activeTextInput(
                     $model,
                     'spouse_firstName',
                     [
                        'maxlength' => true,
                        'id' => 'txtMemberSpouseFirstName',
                        'class' => "form-control w80p " . $tempMember->getPendingInfo($tempMember->temp_spouse_firstName, $model->spouse_firstName),
                        "isapproved" => $tempMember->getPendingInfo($tempMember->temp_spouse_firstName, $model->spouse_firstName) ? "False" : "True",
                        'value' => $tempMember->temp_spouse_firstName,
                        'disabled' => true
                     ]
                  ); ?>
                  <?php if ($tempMember->getPendingInfo($tempMember->temp_spouse_firstName, $model->spouse_firstName)) { ?>
                     <input class="infobtn" isapproved="true" type="button">
                     <?= $tempMember->getDetails($model->spouse_firstName) ?>
                  <?php } ?>
               </div>
            </div>
            <!--/.rows -->
            <!-- rows -->
            <div class="inlinerow Mtop10">
               <div class="col-md-4 col-sm-5 col-xs-12 L32">Middle Name</div>
               <div class="col-md-8 col-sm-7 col-xs-12">
                  <?= Html::activeTextInput(
                     $model,
                     'spouse_middleName',
                     [
                        'maxlength' => true,
                        'id' => 'txtMemberSpouseMiddleName',
                        'class' => "form-control w80p " . $tempMember->getPendingInfo($tempMember->temp_spouse_middleName, $model->spouse_middleName),
                        "isapproved" => $tempMember->getPendingInfo($tempMember->temp_spouse_middleName, $model->spouse_middleName) ? "False" : "True",
                        'value' => $tempMember->temp_spouse_middleName,
                        'disabled' => true
                     ]
                  ); ?>
                  <?php if ($tempMember->getPendingInfo($tempMember->temp_spouse_middleName, $model->spouse_middleName)) { ?>
                     <input class="infobtn" isapproved="true" type="button">
                     <?= $tempMember->getDetails($model->spouse_middleName) ?>
                  <?php } ?>
               </div>
            </div>
            <!--/.rows -->
            <!-- rows -->
            <div class="inlinerow Mtop10">
               <div class="col-md-4 col-sm-5 col-xs-12 L32">Last Name</div>
               <div class="col-md-8 col-sm-7 col-xs-12">
                  <?= Html::activeTextInput(
                     $model,
                     'spouse_lastName',
                     [
                        'maxlength' => true,
                        'id' => 'txtMemberSpouseLastName',
                        'class' => "form-control w80p " . $tempMember->getPendingInfo($tempMember->temp_spouse_lastName, $model->spouse_lastName),
                        "isapproved" => $tempMember->getPendingInfo($tempMember->temp_spouse_lastName, $model->spouse_lastName) ? "False" : "True",
                        'value' => $tempMember->temp_spouse_lastName,
                        'disabled' => true
                     ]
                  ); ?>
                  <?php if ($tempMember->getPendingInfo($tempMember->temp_spouse_lastName, $model->spouse_lastName)) { ?>
                     <input class="infobtn" isapproved="true" type="button">
                     <?= $tempMember->getDetails($model->spouse_lastName) ?>
                  <?php } ?>
               </div>
            </div>
            <!--/.rows -->
            <!-- rows -->
            <div class="inlinerow Mtop10">
               <div class="col-md-4 col-sm-5 col-xs-12 L32">Nick/AKA</div>
               <div class="col-md-8 col-sm-7 col-xs-12">
                  <?= Html::activeTextInput(
                     $model,
                     'spousenickname',
                     [
                        'maxlength' => true,
                        'id' => 'txtSpouseNickName',
                        'class' => "form-control w80p " . $tempMember->getPendingInfo($tempMember->temp_spousenickname, $model->spousenickname),
                        "isapproved" => $tempMember->getPendingInfo($tempMember->temp_spousenickname, $model->spousenickname) ? "False" : "True",
                        'value' => $tempMember->temp_spousenickname,
                        'disabled' => true
                     ]
                  ); ?>
                  <?php if ($tempMember->getPendingInfo($tempMember->temp_spousenickname, $model->spousenickname)) { ?>
                     <input class="infobtn" isapproved="true" type="button">
                     <?= $tempMember->getDetails($model->spousenickname) ?>
                  <?php } ?>
               </div>
            </div>
            <!--/.rows -->
            <!-- rows -->
            <div class="inlinerow Mtop10">
               <div class="col-md-4 col-sm-5 col-xs-12 L32">Email</div>
               <div class="col-md-8 col-sm-7 col-xs-12">
                  <?= Html::activeTextInput(
                     $model,
                     'spouse_email',
                     [
                        'maxlength' => true,
                        'id' => 'txtMemberspouseemail',
                        'class' => "form-control w80p " . $tempMember->getPendingInfo($tempMember->temp_spouse_email, $model->spouse_email),
                        "isapproved" => $tempMember->getPendingInfo($tempMember->temp_spouse_email, $model->spouse_email) ? "False" : "True",
                        'value' => $tempMember->temp_spouse_email,
                        'disabled' => true
                     ]
                  ); ?>
                  <?php if ($tempMember->getPendingInfo($tempMember->temp_spouse_email, $model->spouse_email)) { ?>
                     <input class="infobtn" isapproved="true" type="button">
                     <?= $tempMember->getDetails($model->spouse_email) ?>
                  <?php } ?>
               </div>
            </div>
            <!--/.rows -->
            <!-- rows -->
            <div class="inlinerow Mtop10">
               <div class="col-md-4 col-sm-5 col-xs-12 L32">Mobile </div>
               <div class="col-md-8 col-sm-7 col-xs-12 phone-div">
                  <?= Html::activeTextinput(
                     $model,
                     'spouse_mobile1_countrycode',
                     [
                        'maxlength' => 4,
                        'id' => 'txtspousemobile1_countrycode',
                        'class' => "form-control citycodes " . $tempMember->getPendingInfo($tempMember->temp_spouse_mobile1_countrycode, $model->spouse_mobile1_countrycode),
                        "isapproved" => $tempMember->getPendingInfo($tempMember->temp_spouse_mobile1_countrycode, $model->spouse_mobile1_countrycode) ? "False" : "True",
                        'value' => $tempMember->temp_spouse_mobile1_countrycode,
                        'disabled' => true
                     ]
                  ); ?>
                  <?= Html::activeTextinput(
                     $model,
                     'spouse_mobile1',
                     [
                        'maxlength' => 10,
                        'id' => 'txtspousemobile1',
                        'class' => "form-control mob-block " . $tempMember->getPendingInfo($tempMember->temp_spouse_mobile1, $model->spouse_mobile1),
                        "isapproved" => $tempMember->getPendingInfo($tempMember->temp_spouse_mobile1, $model->spouse_mobile1) ? "False" : "True",
                        'value' => $tempMember->temp_spouse_mobile1,
                        'disabled' => true
                     ]
                  ); ?>
                  <?php
                  $newPhone = $tempMember->temp_spouse_mobile1_countrycode . '-' . $tempMember->temp_spouse_mobile1;
                  $oldPhone = $model->spouse_mobile1_countrycode . '-' . $model->spouse_mobile1;

                  if ($tempMember->getPendingInfo($newPhone, $oldPhone)) {

                  ?>
                     <input class="infobtn" isapproved="true" type="button">
                     <?= $tempMember->getDetails($oldPhone) ?>
                  <?php } ?>
               </div>
            </div>
            <!--/.rows -->
            <!-- rows -->
            <!--/.rows -->
            <!-- rows -->
            <div class="inlinerow Mtop10">
               <div class="col-md-4 col-sm-5 col-xs-12 L32">DOB</div>
               <div class="col-md-8 col-sm-7 col-xs-12">
                  <?php
                  $memberDob = $model->spouse_dob ? date(yii::$app->params['dateFormat']['viewDateFormat'], strtotimenew($model->spouse_dob)) : '';
                  $newMemberDob = $tempMember->temp_spouse_dob ? date(yii::$app->params['dateFormat']['viewDateFormat'], strtotimenew($tempMember->temp_spouse_dob)) : '';
                  ?>
                  <?= Html::activeTextInput(
                     $model,
                     'spouse_dob',
                     [
                        'maxlength' => true,
                        'id' => 'txtSpouseDOB',
                        'class' => "form-control w80p " . $tempMember->getPendingInfo($newMemberDob, $memberDob),
                        "isapproved" => $tempMember->getPendingInfo($newMemberDob, $memberDob) ? "False" : "True",
                        'value' => $newMemberDob,
                        'disabled' => true
                     ]
                  ); ?>
                  <?php if ($tempMember->getPendingInfo($newMemberDob, $memberDob)) { ?>
                     <input class="infobtn" isapproved="true" type="button">
                     <?= $tempMember->getDetails($memberDob) ?>
                  <?php } ?>
               </div>
            </div>
            <div class="inlinerow Mtop10">
               <div class="col-md-4 col-sm-5 L32">Occupation</div>
               <div class="col-md-8 col-sm-7">
                  <?= Html::activeTextInput(
                     $model,
                     'spouseoccupation',
                     [
                        'maxlength' => true,
                        'id' => 'txtSpouseOccupation',
                        'class' => "form-control w80p " . $tempMember->getPendingInfo($tempMember->temp_spouseoccupation, $model->spouseoccupation),
                        "isapproved" => $tempMember->getPendingInfo($tempMember->temp_spouseoccupation, $model->spouseoccupation) ? "False" : "True",
                        'value' => $tempMember->temp_spouseoccupation,
                        'disabled' => true
                     ]
                  ); ?>
                  <?php if ($tempMember->getPendingInfo($tempMember->temp_spouseoccupation, $model->spouseoccupation)) { ?>
                     <input class="infobtn" isapproved="true" type="button">
                     <?= $tempMember->getDetails($model->spouseoccupation) ?>
                  <?php } ?>
               </div>
            </div>
            <div class="inlinerow Mtop10">
               <div class="col-md-4 col-sm-5 L32">Blood Group </div>
               <div class="col-md-8 col-sm-7">
                  <?= Html::activeTextInput(
                     $model,
                     'spousebloodgroup',
                     [
                        'maxlength' => true,
                        'id' => 'txtSpouseBloodGroup',
                        'class' => "form-control w80p " . $tempMember->getPendingInfo($tempMember->tempspouseBloodGroup, $model->spousebloodgroup),
                        "isapproved" => $tempMember->getPendingInfo($tempMember->tempspouseBloodGroup, $model->spousebloodgroup) ? "False" : "True",
                        'value' => $tempMember->tempspouseBloodGroup,
                        'disabled' => true
                     ]
                  ); ?>
                  <?php if ($tempMember->getPendingInfo($tempMember->tempspouseBloodGroup, $model->spousebloodgroup)) { ?>
                     <input class="infobtn" isapproved="true" type="button">
                     <?= $tempMember->getDetails($model->spousebloodgroup) ?>
                  <?php } ?>
               </div>
            </div>
            <!--/.rows -->
         </fieldset>
      </div>
      <!-- /.Section 2 -->
      <!-- Separator -->
      <div class="segment">&nbsp;</div>
      <!-- Section 1 -->
      <div class="col-md-6 col-sm-6 col-xs-12">
         <div class="inlinerow Mtop10">
            <div class="col-md-4 col-sm-5 col-xs-12 L32">Wedding Anniversary</div>
            <div class="col-md-8 col-sm-7 col-xs-12">
               <?php
               $memberDob = $model->dom ? date(yii::$app->params['dateFormat']['viewDateFormat'], strtotimenew($model->dom)) : '';
               $newMemberDob = $tempMember->temp_dom ? date(yii::$app->params['dateFormat']['viewDateFormat'], strtotimenew($tempMember->temp_dom)) : '';
               ?>
               <?= Html::activeTextInput(
                  $model,
                  'dom',
                  [
                     'maxlength' => true,
                     'id' => 'txtdom',
                     'class' => "form-control w80p " . $tempMember->getPendingInfo($newMemberDob, $memberDob),
                     "isapproved" => $tempMember->getPendingInfo($newMemberDob, $memberDob) ? "False" : "True",
                     'value' => $newMemberDob,
                     'disabled' => true
                  ]
               ); ?>
               <?php if ($tempMember->getPendingInfo($newMemberDob, $memberDob)) { ?>
                  <input class="infobtn" isapproved="true" type="button">
                  <?= $tempMember->getDetails($memberDob) ?>
               <?php } ?>
            </div>
         </div>
      </div>
      <!-- /. separate section -->
      <!-- /. location section -->
      <div class="segment">&nbsp;</div>
      <div class="col-md-8 col-sm-6 col-xs-12">
         <fieldset>
            <legend>Location Details</legend>
            <div class="inlinerow Mtop10">
               <div class="col-md-3 col-sm-5 L32">Location Details</div>
               <div class="col-md-8 col-sm-7">
                  <?= Html::activeTextInput(
                     $model,
                     'locationDetails',
                     [
                        'maxlength' => true,
                        'id' => 'txtlocation',
                        'class' => "form-control w80p " . $tempMember->getPendingLocationInfo($tempMember->latitude, $model->latitude, $tempMember->longitude, $model->longitude),
                        "isapproved" => $tempMember->getPendingLocationInfo($tempMember->latitude, $model->latitude, $tempMember->longitude, $model->longitude) ? "False" : "True",
                        'value' => (!empty($tempMember->latitude) && !empty($tempMember->longitude))
                           ? 'latitude: ' . "$tempMember->latitude" . ', longitude: ' . "$tempMember->longitude"
                           : 'latitude: "", longitude: ""',
                        'disabled' => true
                     ]
                  ); ?>
                  <?php if ($tempMember->getPendingLocationInfo($tempMember->latitude, $model->latitude, $tempMember->longitude, $model->longitude)) { ?>
                     <?= Html::a(
                        Html::img(
                           $assetName->baseUrl . '/theme/images/gmap.png',
                           ['alt' => 'Location Icon', 'style' => 'width: 25px; height: 25px; margin-left: 10px;', 'title' => 'View on Google Maps']
                        ),
                        'https://www.google.com/maps?q=' . $tempMember->latitude . ',' . $tempMember->longitude,
                        ['target' => '_blank']
                     ) ?>
                     <input class="infobtn" isapproved="true" type="button">
                     <?= $tempMember->getLocationDetails($model->latitude, $model->longitude) ?>

                  <?php } ?>
               </div>
            </div>
         </fieldset>
      </div>

      <!-- /. location section -->
      <!-- Separator -->
      <div class="segment">&nbsp;</div>
      <div class="col-md-6 col-sm-6 col-xs-12">
         <fieldset>
            <legend>Business Details</legend>
            <!--/.rows -->
            <!-- rows -->
            <div class="inlinerow Mtop10">
               <div class="col-md-4 col-sm-5 col-xs-12 L32">Address Line 1</div>
               <div class="col-md-8 col-sm-7 col-xs-12">
                  <?= Html::activeTextarea(
                     $model,
                     'business_address1',
                     [
                        'maxlength' => true,
                        'id' => 'txtBusinessAddress1',
                        'class' => "form-control w80p " . $tempMember->getPendingInfo($tempMember->temp_business_address1, $model->business_address1),
                        "isapproved" => $tempMember->getPendingInfo($tempMember->temp_business_address1, $model->business_address1) ? "False" : "True",
                        'value' => $tempMember->temp_business_address1,
                        'disabled' => true
                     ]
                  ); ?>
                  <?php if ($tempMember->getPendingInfo($tempMember->temp_business_address1, $model->business_address1)) { ?>
                     <input class="infobtn" isapproved="true" type="button">
                     <?= $tempMember->getDetails($model->business_address1) ?>
                  <?php } ?>
               </div>
            </div>
            <!--/.rows -->
            <!-- rows -->
            <div class="inlinerow Mtop10">
               <div class="col-md-4 col-sm-5 col-xs-12 L32">Address Line 2</div>
               <div class="col-md-8 col-sm-7 col-xs-12">
                  <?= Html::activeTextarea(
                     $model,
                     'business_address2',
                     [
                        'maxlength' => true,
                        'id' => 'txtBusinessAddress2',
                        'class' => "form-control w80p " . $tempMember->getPendingInfo($tempMember->temp_business_address2, $model->business_address2),
                        "isapproved" => $tempMember->getPendingInfo($tempMember->temp_business_address2, $model->business_address2) ? "False" : "True",
                        'value' => $tempMember->temp_business_address2,
                        'disabled' => true
                     ]
                  ); ?>
                  <?php if ($tempMember->getPendingInfo($tempMember->temp_business_address2, $model->business_address2)) { ?>
                     <input class="infobtn" isapproved="true" type="button">
                     <?= $tempMember->getDetails($model->business_address2) ?>
                  <?php } ?>
               </div>
            </div>
            <!--/.rows -->
            <!-- rows -->
            <div class="inlinerow Mtop10">
               <div class="col-md-4 col-sm-5 col-xs-12 L32">Postal Code</div>
               <div class="col-md-8 col-sm-7 col-xs-12">
                  <?= Html::activeTextinput(
                     $model,
                     'business_pincode',
                     [
                        'maxlength' => true,
                        'id' => 'txtBusinessPincode',
                        'class' => "form-control w80p " . $tempMember->getPendingInfo($tempMember->temp_business_pincode, $model->business_pincode),
                        "isapproved" => $tempMember->getPendingInfo($tempMember->temp_business_pincode, $model->business_pincode) ? "False" : "True",
                        'value' => $tempMember->temp_business_pincode,
                        'disabled' => true
                     ]
                  ); ?>
                  <?php if ($tempMember->getPendingInfo($tempMember->temp_business_pincode, $model->business_pincode)) { ?>
                     <input class="infobtn" isapproved="true" type="button">
                     <?= $tempMember->getDetails($model->business_pincode) ?>
                  <?php } ?>
               </div>
            </div>
            <!--/.rows -->
            <!-- rows -->
            <div class="inlinerow Mtop10">
               <div class="col-md-4 col-sm-5 col-xs-12 L32">District</div>
               <div class="col-md-8 col-sm-7 col-xs-12">
                  <?= Html::activeTextinput(
                     $model,
                     'business_district',
                     [
                        'maxlength' => true,
                        'id' => 'txtBusinessDistrict',
                        'class' => "form-control w80p " . $tempMember->getPendingInfo($tempMember->temp_business_district, $model->business_district),
                        "isapproved" => $tempMember->getPendingInfo($tempMember->temp_business_district, $model->business_district) ? "False" : "True",
                        'value' => $tempMember->temp_business_district,
                        'disabled' => true
                     ]
                  ); ?>
                  <?php if ($tempMember->getPendingInfo($tempMember->temp_business_district, $model->business_district)) { ?>
                     <input class="infobtn" isapproved="true" type="button">
                     <?= $tempMember->getDetails($model->business_district) ?>
                  <?php } ?>
               </div>
            </div>
            <!--/.rows -->
            <!-- rows -->
            <div class="inlinerow Mtop10">
               <div class="col-md-4 col-sm-5 col-xs-12 L32">State</div>
               <div class="col-md-8 col-sm-7 col-xs-12">
                  <?= Html::activeTextinput(
                     $model,
                     'business_state',
                     [
                        'maxlength' => true,
                        'id' => 'txtBusinessState',
                        'class' => "form-control w80p " . $tempMember->getPendingInfo($tempMember->temp_business_state, $model->business_state),
                        "isapproved" => $tempMember->getPendingInfo($tempMember->temp_business_state, $model->business_state) ? "False" : "True",
                        'value' => $tempMember->temp_business_state,
                        'disabled' => true
                     ]
                  ); ?>
                  <?php if ($tempMember->getPendingInfo($tempMember->temp_business_state, $model->business_state)) { ?>
                     <input class="infobtn" isapproved="true" type="button">
                     <?= $tempMember->getDetails($model->business_state) ?>
                  <?php } ?>
               </div>
            </div>
            <!--/.rows -->
            <!-- rows -->
            <div class="inlinerow Mtop10">
               <div class="col-md-4 col-sm-5 col-xs-12 L32">Office Phone</div>
               <div class="col-md-8 col-sm-7 col-xs-12 phone-div">
                  <?= Html::activeTextinput(
                     $model,
                     'member_business_phone1_countrycode',
                     [
                        'maxlength' => 4,
                        'id' => 'txtMemberBusinessPhone1_countrycode',
                        'class' => "form-control citycodes  " . $tempMember->getPendingInfo($tempMember->temp_member_business_phone1_countrycode, $model->member_business_phone1_countrycode),
                        "isapproved" => $tempMember->getPendingInfo($tempMember->temp_member_business_phone1_countrycode, $model->member_business_phone1_countrycode) ? "False" : "True",
                        'value' => $tempMember->temp_member_business_phone1_countrycode,
                        'disabled' => true
                     ]
                  ); ?>
                  <?= Html::activeTextinput(
                     $model,
                     'member_business_phone1_areacode',
                     [
                        'maxlength' => 5,
                        'id' => 'txtMemberBusinessPhone1_areacode',
                        'class' => "form-control citycodes  " . $tempMember->getPendingInfo($tempMember->temp_member_business_phone1_areacode, $model->member_business_phone1_areacode),
                        "isapproved" => $tempMember->getPendingInfo($tempMember->temp_member_business_phone1_areacode, $model->member_business_phone1_areacode) ? "False" : "True",
                        'value' => $tempMember->temp_member_business_phone1_areacode,
                        'disabled' => true
                     ]
                  ); ?>
                  <?= Html::activeTextinput(
                     $model,
                     'member_musiness_Phone1',
                     [
                        'maxlength' => 10,
                        'id' => 'txtMemberBusinessPhone1',
                        'class' => "form-control phone-block " . $tempMember->getPendingInfo($tempMember->temp_member_business_Phone1, $model->member_musiness_Phone1),
                        "isapproved" => $tempMember->getPendingInfo($tempMember->temp_member_business_Phone1, $model->member_musiness_Phone1) ? "False" : "True",
                        'value' => $tempMember->temp_member_business_Phone1,
                        'disabled' => true
                     ]
                  ); ?>
                  <?php
                  $newPhone = $tempMember->temp_member_business_phone1_countrycode . '-' . $tempMember->temp_member_business_phone1_areacode . '-' . $tempMember->temp_member_business_Phone1;
                  $oldPhone = $model->member_business_phone1_countrycode . '-' . $model->member_business_phone1_areacode . '-' . $model->member_musiness_Phone1;

                  if ($tempMember->getPendingInfo($newPhone, $oldPhone)) {

                  ?>
                     <input class="infobtn" isapproved="true" type="button">
                     <?= $tempMember->getDetails($oldPhone) ?>
                  <?php } ?>
               </div>
            </div>
            <div class="inlinerow Mtop10">
               <div class="col-md-4 col-sm-5 L32">Office Phone 2</div>
               <div class="col-md-8 col-sm-12 phone-div">
                  <?= Html::activeTextinput(
                     $model,
                     'member_business_phone3_countrycode',
                     [
                        'maxlength' => 4,
                        'id' => 'txtMemberBusinessPhone3_countrycode',
                        'class' => "form-control citycodes  " . $tempMember->getPendingInfo($tempMember->temp_member_business_phone3_countrycode, $model->member_business_phone3_countrycode),
                        "isapproved" => $tempMember->getPendingInfo($tempMember->temp_member_business_phone3_countrycode, $model->member_business_phone3_countrycode) ? "False" : "True",
                        'value' => $tempMember->temp_member_business_phone3_countrycode,
                        'disabled' => true
                     ]
                  ); ?>
                  <?= Html::activeTextinput(
                     $model,
                     'member_business_phone3_areacode',
                     [
                        'maxlength' => 5,
                        'id' => 'txtMemberBusinessPhone3_areacode',
                        'class' => "form-control citycodes  " . $tempMember->getPendingInfo($tempMember->temp_member_business_phone3_areacode, $model->member_business_phone3_areacode),
                        "isapproved" => $tempMember->getPendingInfo($tempMember->temp_member_business_phone3_areacode, $model->member_business_phone3_areacode) ? "False" : "True",
                        'value' => $tempMember->temp_member_business_phone3_areacode,
                        'disabled' => true
                     ]
                  ); ?>
                  <?= Html::activeTextinput(
                     $model,
                     'member_business_Phone3',
                     [
                        'maxlength' => 10,
                        'id' => 'txtMemberBusinessPhone3',
                        'class' => "form-control phone-block " . $tempMember->getPendingInfo($tempMember->temp_member_business_Phone3, $model->member_business_Phone3),
                        "isapproved" => $tempMember->getPendingInfo($tempMember->temp_member_business_Phone3, $model->member_business_Phone3) ? "False" : "True",
                        'value' => $tempMember->temp_member_business_Phone3,
                        'disabled' => true
                     ]
                  ); ?>
                  <?php
                  $newPhone = $tempMember->temp_member_business_phone3_countrycode . '-' . $tempMember->temp_member_business_phone3_areacode . '-' . $tempMember->temp_member_business_Phone3;
                  $oldPhone = $model->member_business_phone3_countrycode . '-' . $model->member_business_phone3_areacode . '-' . $model->member_business_Phone3;

                  if ($tempMember->getPendingInfo($newPhone, $oldPhone)) {

                  ?>
                     <input class="infobtn" isapproved="true" type="button">
                     <?= $tempMember->getDetails($oldPhone) ?>
                  <?php } ?>
               </div>
            </div>
            <!--/.rows -->
            <!-- rows -->
            <div class="inlinerow Mtop10">
               <div class="col-md-4 col-sm-5 col-xs-12 L32">Email</div>
               <div class="col-md-8 col-sm-7 col-xs-12">
                  <?= Html::activeTextinput(
                     $model,
                     'businessemail',
                     [
                        'maxlength' => true,
                        'id' => 'txtbusinessEmail',
                        'class' => "form-control w80p " . $tempMember->getPendingInfo($tempMember->temp_businessemail, $model->businessemail),
                        "isapproved" => $tempMember->getPendingInfo($tempMember->temp_businessemail, $model->businessemail) ? "False" : "True",
                        'value' => $tempMember->temp_businessemail,
                        'disabled' => true
                     ]
                  ); ?>
                  <?php if ($tempMember->getPendingInfo($tempMember->temp_businessemail, $model->businessemail)) { ?>
                     <input class="infobtn" isapproved="true" type="button">
                     <?= $tempMember->getDetails($model->businessemail) ?>
                  <?php } ?>
               </div>
            </div>
            <!--/.rows -->
         </fieldset>
      </div>
      <!-- /.Section 1 -->
      <!-- Section 2 -->
      <div class="col-md-6 col-sm-6 col-xs-12">
         <fieldset>
            <legend>Residence Details</legend>
            <!--/.rows -->
            <!-- rows -->
            <div class="inlinerow Mtop10">
               <div class="col-md-4 col-sm-5 col-xs-12 L32">Address Line 1</div>
               <div class="col-md-8 col-sm-7 col-xs-12">
                  <?= Html::activeTextarea(
                     $model,
                     'residence_address1',
                     [
                        'maxlength' => true,
                        'id' => 'txtresidenceaddress1',
                        'class' => "form-control w80p " . $tempMember->getPendingInfo($tempMember->temp_residence_address1, $model->residence_address1),
                        "isapproved" => $tempMember->getPendingInfo($tempMember->temp_residence_address1, $model->residence_address1) ? "False" : "True",
                        'value' => $tempMember->temp_residence_address1,
                        'disabled' => true
                     ]
                  ); ?>
                  <?php if ($tempMember->getPendingInfo($tempMember->temp_residence_address1, $model->residence_address1)) { ?>
                     <input class="infobtn" isapproved="true" type="button">
                     <?= $tempMember->getDetails($model->residence_address1) ?>
                  <?php } ?>
               </div>
            </div>
            <!--/.rows -->
            <!-- rows -->
            <div class="inlinerow Mtop10">
               <div class="col-md-4 col-sm-5 col-xs-12 L32">Address Line 2</div>
               <div class="col-md-8 col-sm-7 col-xs-12">
                  <?= Html::activeTextinput(
                     $model,
                     'residence_address2',
                     [
                        'maxlength' => true,
                        'id' => 'txtresidenceaddress2',
                        'class' => "form-control w80p " . $tempMember->getPendingInfo($tempMember->temp_residence_address2, $model->residence_address2),
                        "isapproved" => $tempMember->getPendingInfo($tempMember->temp_residence_address2, $model->residence_address2) ? "False" : "True",
                        'value' => $tempMember->temp_residence_address2,
                        'disabled' => true
                     ]
                  ); ?>
                  <?php if ($tempMember->getPendingInfo($tempMember->temp_residence_address2, $model->residence_address2)) { ?>
                     <input class="infobtn" isapproved="true" type="button">
                     <?= $tempMember->getDetails($model->residence_address2) ?>
                  <?php } ?>
               </div>
            </div>
            <!--/.rows -->
            <!-- rows -->
            <div class="inlinerow Mtop10">
               <div class="col-md-4 col-sm-5 col-xs-12 L32">Postal Code</div>
               <div class="col-md-8 col-sm-7 col-xs-12">
                  <?= Html::activeTextinput(
                     $model,
                     'residence_pincode',
                     [
                        'maxlength' => true,
                        'id' => 'txtresidence_pincode',
                        'class' => "form-control w80p " . $tempMember->getPendingInfo($tempMember->temp_residence_pincode, $model->residence_pincode),
                        "isapproved" => $tempMember->getPendingInfo($tempMember->temp_residence_pincode, $model->residence_pincode) ? "False" : "True",
                        'value' => $tempMember->temp_residence_pincode,
                        'disabled' => true
                     ]
                  ); ?>
                  <?php if ($tempMember->getPendingInfo($tempMember->temp_residence_pincode, $model->residence_pincode)) { ?>
                     <input class="infobtn" isapproved="true" type="button">
                     <?= $tempMember->getDetails($model->residence_pincode) ?>
                  <?php } ?>
               </div>
            </div>
            <!--/.rows -->
            <!-- rows -->
            <div class="inlinerow Mtop10">
               <div class="col-md-4 col-sm-5 col-xs-12 L32">District</div>
               <div class="col-md-8 col-sm-7 col-xs-12">
                  <?= Html::activeTextinput(
                     $model,
                     'residence_district',
                     [
                        'maxlength' => true,
                        'id' => 'txtresidencedistrict',
                        'class' => "form-control w80p " . $tempMember->getPendingInfo($tempMember->temp_residence_district, $model->residence_district),
                        "isapproved" => $tempMember->getPendingInfo($tempMember->temp_residence_district, $model->residence_district) ? "False" : "True",
                        'value' => $tempMember->temp_residence_district,
                        'disabled' => true
                     ]
                  ); ?>
                  <?php if ($tempMember->getPendingInfo($tempMember->temp_residence_district, $model->residence_district)) { ?>
                     <input class="infobtn" isapproved="true" type="button">
                     <?= $tempMember->getDetails($model->residence_district) ?>
                  <?php } ?>
               </div>
            </div>
            <!--/.rows -->
            <!-- rows -->
            <div class="inlinerow Mtop10">
               <div class="col-md-4 col-sm-5 col-xs-12 L32">State</div>
               <div class="col-md-8 col-sm-7 col-xs-12">
                  <?= Html::activeTextinput(
                     $model,
                     'residence_state',
                     [
                        'maxlength' => true,
                        'id' => 'txtresidencestate',
                        'class' => "form-control w80p " . $tempMember->getPendingInfo($tempMember->temp_residence_state, $model->residence_state),
                        "isapproved" => $tempMember->getPendingInfo($tempMember->temp_residence_state, $model->residence_state) ? "False" : "True",
                        'value' => $tempMember->temp_residence_state,
                        'disabled' => true
                     ]
                  ); ?>
                  <?php if ($tempMember->getPendingInfo($tempMember->temp_residence_state, $model->residence_state)) { ?>
                     <input class="infobtn" isapproved="true" type="button">
                     <?= $tempMember->getDetails($model->residence_state) ?>
                  <?php } ?>
               </div>
            </div>
            <!--/.rows -->
            <!-- rows -->
            <div class="inlinerow Mtop10">
               <div class="col-md-4 col-sm-5 L32">Land Line Number</div>
               <div class="col-md-8 col-sm-12">
                  <?= Html::activeTextinput(
                     $model,
                     'member_residence_Phone1_countrycode',
                     [
                        'maxlength' => 4,
                        'id' => 'txtMemberResidencePhone1_countrycode',
                        'class' => "form-control citycodes  " . $tempMember->getPendingInfo($tempMember->temp_member_residence_Phone1_countrycode, $model->member_residence_Phone1_countrycode),
                        "isapproved" => $tempMember->getPendingInfo($tempMember->temp_member_residence_Phone1_countrycode, $model->member_residence_Phone1_countrycode) ? "False" : "True",
                        'value' => $tempMember->temp_member_residence_Phone1_countrycode,
                        'disabled' => true
                     ]
                  ); ?>
                  <?= Html::activeTextinput(
                     $model,
                     'member_residence_phone1_areacode',
                     [
                        'maxlength' => 5,
                        'id' => 'txtMemberResidencePhone1_areacode',
                        'class' => "form-control citycodes  " . $tempMember->getPendingInfo($tempMember->temp_member_residence_Phone1_areacode, $model->member_residence_phone1_areacode),
                        "isapproved" => $tempMember->getPendingInfo($tempMember->temp_member_residence_Phone1_areacode, $model->member_residence_phone1_areacode) ? "False" : "True",
                        'value' => $tempMember->temp_member_residence_Phone1_areacode,
                        'disabled' => true
                     ]
                  ); ?>
                  <?= Html::activeTextinput(
                     $model,
                     'member_residence_Phone1',
                     [
                        'maxlength' => 10,
                        'id' => 'txtMemberResidencePhone1',
                        'class' => "form-control phone-block  " . $tempMember->getPendingInfo($tempMember->temp_member_residence_Phone1, $model->member_residence_Phone1),
                        "isapproved" => $tempMember->getPendingInfo($tempMember->temp_member_residence_Phone1, $model->member_residence_Phone1) ? "False" : "True",
                        'value' => $tempMember->temp_member_residence_Phone1,
                        'disabled' => true
                     ]
                  ); ?>
                  <?php
                  $newPhone = $tempMember->temp_member_residence_Phone1_countrycode . '-' . $tempMember->temp_member_residence_Phone1_areacode . '-' . $tempMember->temp_member_residence_Phone1;
                  $oldPhone = $model->member_residence_Phone1_countrycode . '-' . $model->member_residence_phone1_areacode . '-' . $model->member_residence_Phone1;

                  if ($tempMember->getPendingInfo($newPhone, $oldPhone)) {

                  ?>
                     <input class="infobtn" isapproved="true" type="button">
                     <?= $tempMember->getDetails($oldPhone) ?>
                  <?php } ?>
               </div>
            </div>
            <!--/.rows -->
            <!-- rows -->
            <div class="inlinerow Mtop10" style="display: none;">
               <div class="col-md-4 col-sm-5 col-xs-12 L32">TelePhone 2</div>
               <div class="col-md-8 col-sm-7 col-xs-12">
                  <input autocomplete="off" class="form-control" id="txtMemberResidencePhone2" maxlength="13" name="MemberResidencePhone2" type="text" value="">
               </div>
            </div>
            <div class="inlinerow Mtop10">
               <div class="col-md-4 col-sm-5 col-xs-12 L32"></div>
               <div class="col-md-8 col-sm-7 col-xs-12"> </div>
            </div>
            <div class="inlinerow Mtop10">
               <div class="col-md-4 col-sm-5 col-xs-12 L32"></div>
               <div class="col-md-8 col-sm-7 col-xs-12"> </div>
            </div>
            <div class="inlinerow Mtop10">
               <div class="col-md-4 col-sm-5 col-xs-12 L32"></div>
               <div class="col-md-8 col-sm-7 col-xs-12"> </div>
            </div>
         </fieldset>
         <!--/.rows -->
      </div>
      <!-- /.Section 2 -->
      <?php
      $count = count($dependantDetails);

      if ($count >= 1) {

      ?>
         <!-- Dependants -->
         <div class="col-md-12 col-sm-12 col-xs-12 Mtop30">
            <fieldset>
               <legend>Dependents</legend>
               <input type='hidden' value="<?= $dependantIds ?>" name='depentantIdslist'>
               <?php
               $i = 0;
               $j = 0;
               foreach ($dependantDetails as $data) {
                  if (!isset($tempDepentantDetails[$data['id']])) {
                     $tempDepentant = [
                        'dependantid' => $data['id'],
                        'tempmemberid' => "",
                        'dependanttitleid' => "",
                        'dependanttitle' => "",
                        'dependantname' => "",
                        'dependantmobilecountrycode' => "",
                        'dependantmobile' => "",
                        'relation' => "",
                        'dob' => "",
                        'ismarried' => "",
                        'weddinganniversary' => "",
                        'DependantSpouseId' => "",
                        'spousedependantid' => "",
                        'spousetitleid' => "",
                        'spousetitle' => "",
                        'spousename' => "",
                        'dependantspousemobilecountrycode' => "",
                        'dependantspousemobile' => "",
                        'spousedob' => "",
                        'tempimage' => "",
                        'tempdependantspouseimage' => "",
                        'tempimagethumbnail' => "",
                        'tempdependantspouseimagethumbnail' => ""
                     ];
                  } else {
                     $tempDepentant = $tempDepentantDetails[$data['id']];
                     $tempDepentant = $tempDepentant[0];
                  }

                  $i++;

               ?>
                  <div class="<?= $i % 2 == 2 ? 'row' : '' ?>">
                     <div class="col-md-6 col-sm-6 col-xs-12 <?= $i % 2 == 1 ? "splitup" : '' ?>">
                        <div class="inlinerow Mtop10">
                           <div class="col-md-4 col-sm-5 col-xs-12 L32">Dependent Photo</div>
                           <div class="col-md-8 col-sm-7 col-xs-12">
                              <div class="col-md-5 col-sm-5 col-xs-12 imagesize"> <img class="fix-size" width="184" height="184" id='txtDependantImage<?= $j ?>' src="<?php
                                                                                                                                                                        $image  = $tempDepentant['tempimage'] ? $tempDepentant['tempimage'] : "/Member/default-user.png";
                                                                                                                                                                        echo Yii::$app->params['imagePath'] . $image; ?>"
                                    isapproved="<?php echo $tempMember->getPendingInfo($tempDepentant['tempimage'], $data['dependantimage']) ? 'False' : 'True' ?>"> </div>
                              <?php if ($tempMember->getPendingInfo($tempDepentant['tempimage'], $data['dependantimage'])) { ?>
                                 <div class="col-md-7 col-sm-7">
                                    <input type="button" class="infobtn" id='dependantimageinfo<?= $j ?>' isapproved="<?php echo $tempMember->getPendingInfo($tempDepentant['tempimage'], $data['dependantimage']) ? 'False' : 'True' ?>">
                                    <!-- approval box -->
                                    <div class="approvalbox nodisplay">
                                       <div class="prevdatahead">Previous Data</div>
                                       <div class="prevdata">
                                          <img class="fix-size" width="184" height="184" src="<?php
                                                                                                $image  = $data['dependantimage'] ? $data['dependantimage'] : "/Member/default-user.png";
                                                                                                echo Yii::$app->params['imagePath'] . $image; ?> " id="dependantpic<?= $j ?>" />
                                       </div>
                                       <div class="inlinerow text-center">
                                          <input type="button" class="approvebtn" value="Approve">
                                          <input type="button" class="rejectbtn" value="Reject">
                                       </div>
                                    </div>
                                    <!-- /.approval box -->
                                 </div>
                              <?php } ?>
                           </div>
                           <div class="inlinerow Mtop10">
                              <div class="col-md-4 col-sm-5 col-xs-12 L32">Dependent Title</div>
                              <div class="col-md-8 col-sm-7 col-xs-12">
                                 <?= Html::activeTextInput(
                                    $dynamicDepentantModel,
                                    'title',
                                    [
                                       'maxlength' => true,
                                       'id' => 'txtDependantTitle' . $j,
                                       'class' => "form-control w80p " . $tempMember->getPendingInfo($titlesArray[$tempDepentant['dependanttitleid']], $titlesArray[$data['dependanttitleid']]),
                                       "isapproved" => $tempMember->getPendingInfo($titlesArray[$tempDepentant['dependanttitleid']], $titlesArray[$data['dependanttitleid']]) ? 'False' : 'True',
                                       'value' => $titlesArray[$tempDepentant['dependanttitleid']],
                                       'disabled' => true
                                    ]
                                 ); ?>
                                 <?php if ($tempMember->getPendingInfo($titlesArray[$tempDepentant['dependanttitleid']], $titlesArray[$data['dependanttitleid']])) { ?>
                                    <input class="infobtn" isapproved="true" type="button">
                                    <?= $tempMember->getDetails($titlesArray[$data['dependanttitleid']]) ?>
                                 <?php } ?>
                              </div>
                           </div>
                           <div class="inlinerow Mtop10">
                              <div class="col-md-4 col-sm-5 col-xs-12 L32">Dependent Name</div>
                              <div class="col-md-8 col-sm-7 col-xs-12">
                                 <?= Html::activeTextinput(
                                    $dynamicDepentantModel,
                                    'dependantname',
                                    [
                                       'maxlength' => true,
                                       'id' => 'txtDependantName' . $j,
                                       'class' => "form-control w80p " . $tempMember->getPendingInfo($tempDepentant['dependantname'], $data['dependantname']),
                                       "isapproved" => $tempMember->getPendingInfo($tempDepentant['dependantname'], $data['dependantname']) ? "False" : "True",
                                       'value' => $tempDepentant['dependantname'],
                                       "name" => "dependantname_" . $data['id'],
                                       'dependantid' =>  $data['id'],
                                       'disabled' => true
                                    ]
                                 ); ?>
                                 <?php if ($tempMember->getPendingInfo($tempDepentant['dependantname'], $data['dependantname'])) { ?>
                                    <input class="infobtn" isapproved="true" type="button">
                                    <?= $tempMember->getDetails($data['dependantname']) ?>
                                 <?php } ?>
                              </div>
                           </div>
                           <div class="inlinerow Mtop10">
                              <div class="col-md-4 col-sm-5 col-xs-12 L32">Dependent Mobile</div>
                              <div class="col-md-8 col-sm-7 col-xs-12 phone-div">
                                 <?= Html::activeTextinput(
                                    $dynamicDepentantModel,
                                    'dependantmobilecountrycode',
                                    [
                                       'minlength' => 6,
                                       'maxlength' => 13,
                                       'id' => 'txtDependantMobileCountryCode' . $j,
                                       'class' => "form-control citycodes " . $tempMember->getPendingInfo($tempDepentant['dependantmobilecountrycode'], $data['dependantmobilecountrycode']),
                                       "isapproved" => $tempMember->getPendingInfo($tempDepentant['dependantmobilecountrycode'], $data['dependantmobilecountrycode']) ? "False" : "True",
                                       'value' => $tempDepentant['dependantmobilecountrycode'],
                                       "name" => "dependantmobilecountrycode" . $data['id'],
                                       'dependantid' =>  $data['id'],
                                       'disabled' => true
                                    ]
                                 ); ?>
                                 <?= Html::activeTextinput(
                                    $dynamicDepentantModel,
                                    'dependantmobile',
                                    [
                                       'minlength' => 6,
                                       'maxlength' => 13,
                                       'id' => 'txtDependantMobile' . $j,
                                       'class' => "form-control mob-block " . $tempMember->getPendingInfo($tempDepentant['dependantmobile'], $data['dependantmobile']),
                                       "isapproved" => $tempMember->getPendingInfo($tempDepentant['dependantmobile'], $data['dependantmobile']) ? "False" : "True",
                                       'value' => $tempDepentant['dependantmobile'],
                                       "name" => "dependantmobile" . $data['id'],
                                       'dependantid' =>  $data['id'],
                                       'disabled' => true
                                    ]
                                 ); ?>
                                 <?php
                                 $newPhoneDependant = $tempDepentant['dependantmobilecountrycode'] . '-' . $tempDepentant['dependantmobile'];
                                 $oldPhoneDependant = $data['dependantmobilecountrycode'] . '-' . $data['dependantmobile'];

                                 if ($tempMember->getPendingInfo($newPhoneDependant, $oldPhoneDependant)) {

                                 ?>
                                    <input class="infobtn" isapproved="true" type="button">
                                    <?= $tempMember->getDetails($oldPhoneDependant) ?>
                                 <?php } ?>
                              </div>
                           </div>
                           <div class="inlinerow Mtop10">
                              <div class="col-md-4 col-sm-5 col-xs-12 L32">DOB</div>
                              <div class="col-md-8 col-sm-7 col-xs-12">
                                 <?php
                                 $memberDob = $data['dob'] ? date(yii::$app->params['dateFormat']['viewDateFormat'], strtotimenew($data['dob'])) : '';
                                 $newMemberDob = $tempDepentant['dob'] ? date(yii::$app->params['dateFormat']['viewDateFormat'], strtotimenew($tempDepentant['dob'])) : '';
                                 ?>
                                 <?= Html::activeTextInput(
                                    $dynamicDepentantModel,
                                    'dob',
                                    [
                                       'maxlength' => true,
                                       'id' => 'txtDependantDOB' . $j,
                                       'class' => "form-control w80p " . $tempMember->getPendingInfo($newMemberDob, $memberDob),
                                       "isapproved" => $tempMember->getPendingInfo($newMemberDob, $memberDob) ? "False" : "True",
                                       'value' => $newMemberDob,
                                       'disabled' => true
                                    ]
                                 ); ?>
                                 <?php if ($tempMember->getPendingInfo($newMemberDob, $memberDob)) { ?>
                                    <input class="infobtn" isapproved="true" type="button">
                                    <?= $tempMember->getDetails($memberDob) ?>
                                 <?php } ?>
                              </div>
                           </div>
                           <div class="inlinerow Mtop10">
                              <div class="col-md-4 col-sm-5 col-xs-12 L32">Relation</div>
                              <div class="col-md-8 col-sm-7 col-xs-12">
                                 <?= Html::activeTextinput(
                                    $dynamicDepentantModel,
                                    'relation',
                                    [
                                       'maxlength' => true,
                                       'id' => 'txtRelation' . $j,
                                       'class' => "form-control w80p " . $tempMember->getPendingInfo($tempDepentant['relation'], $data['relation']),
                                       "isapproved" => $tempMember->getPendingInfo($tempDepentant['relation'], $data['relation']) ? "False" : "True",
                                       'value' => $tempDepentant['relation'],
                                       "name" => "dependantrelation_" . $data['id'],
                                       'disabled' => true
                                    ]
                                 ); ?>
                                 <?php if ($tempMember->getPendingInfo($tempDepentant['relation'], $data['relation'])) { ?>
                                    <input class="infobtn" isapproved="true" type="button">
                                    <?= $tempMember->getDetails($data['relation']) ?>
                                 <?php } ?>
                                 </select>
                              </div>
                           </div>
                           <div class="inlinerow Mtop10">
                              <div class="col-md-4 col-sm-5 col-xs-12 L32">Marital Status</div>
                              <div class="col-md-8 col-sm-7 col-xs-12">
                                 <?= Html::activeTextinput(
                                    $dynamicDepentantModel,
                                    'ismarried',
                                    [
                                       'maxlength' => true,
                                       'id' => 'txtMartialStatus' . $j,
                                       'class' => "form-control w80p " . $tempMember->getPendingInfo($isMarried[$tempDepentant['ismarried']], $isMarried[$data['ismarried']]),
                                       "isapproved" => $tempMember->getPendingInfo($isMarried[$tempDepentant['ismarried']], $isMarried[$data['ismarried']]) ? "False" : "True",
                                       'value' => $isMarried[$tempDepentant['ismarried']],
                                       "name" => "dependantmartialstatus_" . $data['id'],
                                       'disabled' => true
                                    ]
                                 ); ?>
                                 <?php if ($tempMember->getPendingInfo($isMarried[$tempDepentant['ismarried']], $isMarried[$data['ismarried']])) { ?>
                                    <input class="infobtn" isapproved="true" type="button">
                                    <?= $tempMember->getDetails($isMarried[$data['ismarried']]) ?>
                                 <?php } ?>
                              </div>
                           </div>
                           <!-- Hidden spouse info -->
                           <div id="dependentspousediv_<?= $data['id'] ?>" class="inlinerow maritalspouse Mtop20 ">
                              <div class="inlinerow Mtop10">
                                 <div class="col-md-4 col-sm-5 col-xs-12 L32">Spouse Photo</div>
                                 <div class="col-md-8 col-sm-7 col-xs-12">
                                    <div class="col-md-5 col-sm-5 col-xs-12 imagesize"> <img class="fix-size" width="184" height="184" id='txtDependantSpouseImage<?= $j ?>' src="<?php
                                                                                                                                                                                    $image  = $tempDepentant['tempdependantspouseimage'] ? $tempDepentant['tempdependantspouseimage'] : "/Member/default-user.png";
                                                                                                                                                                                    echo Yii::$app->params['imagePath'] . $image; ?>"
                                          isapproved="<?php echo $tempMember->getPendingInfo($tempDepentant['tempdependantspouseimage'], $data['dependantspouseimage']) ? 'False' : 'True' ?>"> </div>
                                    <?php if ($tempMember->getPendingInfo($tempDepentant['tempdependantspouseimage'], $data['dependantspouseimage'])) { ?>
                                       <div class="col-md-7 col-sm-7">
                                          <input type="button" class="infobtn" id='dependantspouseimageinfo<?= $j ?>'
                                             isapproved="<?php echo $tempMember->getPendingInfo($tempDepentant['tempdependantspouseimage'], $data['dependantspouseimage']) ? 'False' : 'True' ?>">
                                          <!-- approval box -->
                                          <div class="approvalbox nodisplay">
                                             <div class="prevdatahead">Previous Data</div>
                                             <div class="prevdata">
                                                <img class="fix-size" width="184" height="184" src="<?php
                                                                                                      $image  = $data['dependantspouseimage'] ? $data['dependantspouseimage'] : "/Member/default-user.png";
                                                                                                      echo Yii::$app->params['imagePath'] . $image; ?> " id="dependantspousepic<?= $j ?>" />
                                             </div>
                                             <div class="inlinerow text-center">
                                                <input type="button" class="approvebtn" value="Approve">
                                                <input type="button" class="rejectbtn" value="Reject">
                                             </div>
                                          </div>
                                          <!-- /.approval box -->
                                       </div>
                                    <?php } ?>
                                 </div>
                              </div>
                              <input type='hidden' name='spousedependantid_<?= $data['id'] ?>' value="<?= $data['dependantspouseid'] ?>">
                              <div class="inlinerow Mtop10">
                                 <div class="col-md-4 col-sm-5 col-xs-12 L32">Spouse Title</div>
                                 <div class="col-md-8 col-sm-7 col-xs-12">
                                    <?= Html::activeTextInput(
                                       $dynamicDepentantModel,
                                       'spousetitle',
                                       [
                                          'maxlength' => true,
                                          'id' => 'txtDependantSpouseTitle' . $j,
                                          'class' => "form-control w80p " . $tempMember->getPendingInfo($titlesArray[$tempDepentant['spousetitleid']], $titlesArray[$data['spousetitleid']]),
                                          "isapproved" => $tempMember->getPendingInfo($titlesArray[$tempDepentant['spousetitleid']], $titlesArray[$data['spousetitleid']]) ? 'False' : 'True',
                                          'value' => $titlesArray[$tempDepentant['spousetitleid']],
                                          'disabled' => true
                                       ]
                                    ); ?>
                                    <?php if ($tempMember->getPendingInfo($titlesArray[$tempDepentant['spousetitleid']], $titlesArray[$data['spousetitleid']])) { ?>
                                       <input class="infobtn" isapproved="true" type="button">
                                       <?= $tempMember->getDetails($titlesArray[$data['spousetitleid']]) ?>
                                    <?php } ?>
                                 </div>
                              </div>
                              <div class="inlinerow Mtop10">
                                 <div class="col-md-4 col-sm-5 col-xs-12 L32">Spouse Name</div>
                                 <div class="col-md-8 col-sm-7 col-xs-12">
                                    <?= Html::activeTextinput(
                                       $dynamicDepentantModel,
                                       'spousename',
                                       [
                                          'maxlength' => true,
                                          'id' => 'txtSpouseName' . $j,
                                          'class' => "form-control w80p " . $tempMember->getPendingInfo($tempDepentant['spousename'], $data['spousename']),
                                          "isapproved" => $tempMember->getPendingInfo($tempDepentant['spousename'], $data['spousename']) ? "False" : "True",
                                          'value' => $tempDepentant['spousename'],
                                          "name" => "tempDependantSpousName_" . $data['id'],
                                          'disabled' => true
                                       ]
                                    ); ?>
                                    <?php if ($tempMember->getPendingInfo($tempDepentant['spousename'], $data['spousename'])) { ?>
                                       <input class="infobtn" isapproved="true" type="button">
                                       <?= $tempMember->getDetails($data['spousename']) ?>
                                    <?php } ?>
                                 </div>
                              </div>
                              <div class="inlinerow Mtop10">
                                 <div class="col-md-4 col-sm-5 col-xs-12 L32">Spouse Mobile</div>
                                 <div class="col-md-8 col-sm-7 col-xs-12 phone-div">
                                    <?= Html::activeTextinput(
                                       $dynamicDepentantModel,
                                       'dependantspousemobilecountrycode',
                                       [
                                          'maxlength' => true,
                                          'id' => 'txtSpouseMobileCountryCode' . $j,
                                          'class' => "form-control citycodes " . $tempMember->getPendingInfo($tempDepentant['dependantspousemobilecountrycode'], $data['dependantspousemobilecountrycode']),
                                          "isapproved" => $tempMember->getPendingInfo($tempDepentant['dependantspousemobilecountrycode'], $data['dependantspousemobilecountrycode']) ? "False" : "True",
                                          'value' => $tempDepentant['dependantspousemobilecountrycode'],
                                          "name" => "tempDependantSpouseMobileCountryCode_" . $data['id'],
                                          'disabled' => true
                                       ]
                                    ); ?>
                                    <?= Html::activeTextinput(
                                       $dynamicDepentantModel,
                                       'dependantspousemobile',
                                       [
                                          'maxlength' => true,
                                          'id' => 'txtSpouseMobile' . $j,
                                          'class' => "form-control mob-block " . $tempMember->getPendingInfo($tempDepentant['dependantspousemobile'], $data['dependantspousemobile']),
                                          "isapproved" => $tempMember->getPendingInfo($tempDepentant['dependantspousemobile'], $data['dependantspousemobile']) ? "False" : "True",
                                          'value' => $tempDepentant['dependantspousemobile'],
                                          "name" => "tempDependantSpouseMobile_" . $data['id'],
                                          'disabled' => true
                                       ]
                                    ); ?>
                                    <?php
                                    $newPhoneDependantSpouse = $tempDepentant['dependantspousemobilecountrycode'] . '-' . $tempDepentant['dependantspousemobile'];
                                    $oldPhoneDependantSpouse = $data['dependantspousemobilecountrycode'] . '-' . $data['dependantspousemobile'];

                                    if ($tempMember->getPendingInfo($newPhoneDependantSpouse, $oldPhoneDependantSpouse)) {

                                    ?>
                                       <input class="infobtn" isapproved="true" type="button">
                                       <?= $tempMember->getDetails($oldPhoneDependantSpouse) ?>
                                    <?php } ?>
                                 </div>
                              </div>
                              <div class="inlinerow Mtop10">
                                 <div class="col-md-4 col-sm-5 col-xs-12 L32">Spouse DOB</div>
                                 <div class="col-md-8 col-sm-7 col-xs-12">
                                    <?php
                                    $memberDob = $data['spousedob'] ? date(yii::$app->params['dateFormat']['viewDateFormat'], strtotimenew($data['spousedob'])) : '';
                                    $newMemberDob = $tempDepentant['spousedob'] ? date(yii::$app->params['dateFormat']['viewDateFormat'], strtotimenew($tempDepentant['spousedob'])) : '';
                                    ?>
                                    <?= Html::activeTextInput(
                                       $dynamicDepentantModel,
                                       'dob',
                                       [
                                          'maxlength' => true,
                                          'id' => 'txtSpouseDOB' . $j,
                                          'class' => "form-control w80p " . $tempMember->getPendingInfo($newMemberDob, $memberDob),
                                          "isapproved" => $tempMember->getPendingInfo($newMemberDob, $memberDob) ? "False" : "True",
                                          'value' => $newMemberDob,
                                          'disabled' => true
                                       ]
                                    ); ?>
                                    <?php if ($tempMember->getPendingInfo($newMemberDob, $memberDob)) { ?>
                                       <input class="infobtn" isapproved="true" type="button">
                                       <?= $tempMember->getDetails($memberDob) ?>
                                    <?php } ?>
                                 </div>
                              </div>
                              <div class="inlinerow Mtop10">
                                 <div class="col-md-4 col-sm-5 col-xs-12 L32">Wedding Anniversary</div>
                                 <div class="col-md-8 col-sm-7 col-xs-12">
                                    <?php
                                    $memberDob = $data['weddinganniversary'] ? date(yii::$app->params['dateFormat']['viewDateFormat'], strtotimenew($data['weddinganniversary'])) : '';
                                    $newMemberDob = $tempDepentant['weddinganniversary'] ? date(yii::$app->params['dateFormat']['viewDateFormat'], strtotimenew($tempDepentant['weddinganniversary'])) : '';
                                    ?>
                                    <?= Html::activeTextInput(
                                       $dynamicDepentantModel,
                                       'weddinganniversary',
                                       [
                                          'maxlength' => true,
                                          'id' => 'txtWeddingAnniversary' . $j,
                                          'class' => "form-control w80p " . $tempMember->getPendingInfo($newMemberDob, $memberDob),
                                          "isapproved" => $tempMember->getPendingInfo($newMemberDob, $memberDob) ? "False" : "True",
                                          'value' => $newMemberDob,
                                          'disabled' => true
                                       ]
                                    ); ?>
                                    <?php if ($tempMember->getPendingInfo($newMemberDob, $memberDob)) { ?>
                                       <input class="infobtn" isapproved="true" type="button">
                                       <?= $tempMember->getDetails($memberDob) ?>
                                    <?php } ?>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <?php if ($i % 2 == 2) { ?>
                           <!--   </div>-->
                        <?php } ?>
                     </div>
                  </div>
               <?php $j++;
               } ?>
            </fieldset>
         </div>
      <?php } ?>
      <!-- /.Dependants -->
   </div>
   <!-- /.Member Tab -->
   <!-- save -->
   <div class="inlinerow text-center Mtop20">
      <?= Html::a('Back', Yii::$app->request->referrer ?: Yii::$app->homeUrl, ['class' => 'btn btn-primary btn-lg']); ?>
      <?= Html::submitButton('Save', ['class' => 'btn btn-success btn-lg valid', 'title' => 'Save']) ?>
   </div>
   <!-- /.save -->

   <?php ActiveForm::end(); ?>
</div>
<!-- Hide this div till here -->
