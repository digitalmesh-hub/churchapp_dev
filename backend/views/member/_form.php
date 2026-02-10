<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\assets\AppAsset;
use kartik\date\DatePicker; 
use backend\components\widgets\FlashResult;
use common\models\extendedmodels\ExtendedInstitution;

/* @var $this yii\web\View */
/* @var $model common\models\extendedmodels\ExtendedMember */
/* @var $form yii\widgets\ActiveForm */

$assetName = AppAsset::register($this);
$this->registerJsFile(
		$assetName->baseUrl . '/theme/js/Remember.memberCreate.ui.js',
		[
				'depends' => [
						AppAsset::className()
				]
		]
);

$this->registerCss('.error-border { border: 1px solid red !important; }');

$this->registerCss('
.tooltip-inner {
    max-width: 400px !important;
    text-align: left;
    white-space: normal;
}
.contentbg {
    overflow: visible !important;
}
');

$this->registerJs("
$(function () {
    $('[data-toggle=\"tooltip\"]').tooltip({
        container: '.contentbg',
        boundary: 'viewport'
    });
});
", \yii\web\View::POS_READY);

echo Html::hiddenInput(
		'dependant-edit-url',
		\Yii::$app->params['ajaxUrl']['dependant-edit-url'],
		[
				'id'=>'dependant-edit-url'
		]
);
echo Html::hiddenInput(
		'dependant-delete-url',
		\Yii::$app->params['ajaxUrl']['dependant-delete-url'],
		[
				'id'=>'dependant-delete-url'
		]
);

echo Html::hiddenInput(
		'sotre-depentdant-url',
		\Yii::$app->params['ajaxUrl']['sotre-depentdant-url'],
		[
				'id'=>'sotre-depentdant-url'
		]
);
echo Html::hiddenInput(
		'generate-member-url',
		\Yii::$app->params['ajaxUrl']['generate-member-url'],
		[
				'id'=>'generate-member-url'
		]
);

echo Html::hiddenInput(
		'get-member-profile-url',
		\Yii::$app->params['ajaxUrl']['get-member-profile-url'],
		[
				'id'=>'get-member-profile-url'
		]
);

echo Html::hiddenInput(
		'sent-genarated-link',
		\Yii::$app->params['ajaxUrl']['sent-genarated-link'],
		[
				'id'=>'sent-genarated-link'
		]
);
echo Html::hiddenInput(
		'remove-spouse',
		\Yii::$app->params['ajaxUrl']['remove-spouse'],
		[
				'id'=>'remove-spouse'
		]
);


echo Html::hiddenInput(
		'remove-member',
		\Yii::$app->params['ajaxUrl']['remove-member'],
		[
				'id'=>'remove-member'
		]
);
echo Html::hiddenInput(
		'base-url',
		$assetName->baseUrl,
		[
				'id'=>'base-url'
		]
);
echo Html::hiddenInput(
    'member-role-dep-drop-Url',
    \Yii::$app->params['ajaxUrl']['member-role-dep-drop-Url'],
    [
        'id'=>'member-role-dep-drop-Url'
    ]
);
echo Html::hiddenInput(
    'remove-member-pic',
    \Yii::$app->params['ajaxUrl']['remove-member-pic'],
    [
        'id'=>'remove-member-pic'
    ]
);

echo Html::hiddenInput(
    'remove-member-spousepic',
    \Yii::$app->params['ajaxUrl']['remove-member-spousepic'],
    [
        'id'=>'remove-member-spousepic'
    ]
);

echo Html::hiddenInput(
		'isStaff',
		$type,
		[
				'id'=>'isStaff'
		]
		);
?>

<div class="col-md-12 col-sm-12 pageheader Mtop15"><?= $type ?> Registration</div>
<div class="extended-member-form">

    <?php $form = ActiveForm::begin(
    		
    		[
            'options'=>[
               'enctype' => 'multipart/form-data',
               'id' =>'form',
            ],
    		   'enableClientValidation'=>false
			]				
    ); 
  
    ?>
      <?= $form->field($model, 'memberid')->hiddenInput(['id'=> 'hdnmemberid'])->label(false) ?>
     <div class="col-md-12 col-sm-12 contentbg">
     <div class="Mtop10">
      <?= FlashResult::widget(); ?>
      </div>
      
      <?php if ($model->memberid && $model->updated_by): ?>
      <div class="col-md-12 col-sm-12 Mtop10">
          <div class="alert alert-info" style="margin-bottom: 10px;">
              <i class="fa fa-info-circle"></i> 
              <strong>Last Updated:</strong> 
              <?php 
                  $updatedBy = \common\models\extendedmodels\ExtendedUserCredentials::findOne($model->updated_by);
                  if ($updatedBy) {
                      $userName = $updatedBy->emailid; // default to email
                      
                      // Try to get the member's name through UserMember
                      $usermember = \common\models\extendedmodels\ExtendedUserMember::find()
                          ->where(['userid' => $updatedBy->id])
                          ->one();
                      if ($usermember && $usermember->member) {
                          $member = $usermember->member;
                          // Check if user is spouse or member
                          if ($usermember->usertype === 'S') {
                              // Spouse user - use spouse name fields
                              $userName = trim(implode(' ', array_filter([
                                  $member->spouse_firstName,
                                  $member->spouse_middleName,
                                  $member->spouse_lastName
                              ])));
                          } else {
                              // Member user - use primary member name fields
                              $userName = trim(implode(' ', array_filter([
                                  $member->firstName,
                                  $member->middleName,
                                  $member->lastName
                              ])));
                          }
                      } elseif ($updatedBy->userprofile) {
                          // If not a member, try userprofile
                          $userName = trim(implode(' ', array_filter([
                              $updatedBy->userprofile->firstname,
                              $updatedBy->userprofile->middlename,
                              $updatedBy->userprofile->lastname
                          ])));
                      }
                      
                      echo Html::encode($userName);
                      if ($model->lastupdated) {
                          $timezone = Yii::$app->user->identity->institution->timezone ?? 'Asia/Kolkata';
                          $dateTime = \common\models\basemodels\BaseModel::convertToUserTimezone($model->lastupdated, $timezone, true);
                          if ($dateTime) {
                              echo ' on ' . $dateTime->format('d M Y, h:i A');
                          }
                      }
                  } else {
                      echo 'Unknown';
                  }
              ?>
          </div>
      </div>
      <?php endif; ?>
      
      <?php if (!$model->memberid): ?>
      <div class="col-md-12 col-sm-12 Mtop10">
          <div class="alert alert-success" style="margin-bottom: 10px;">
              <i class="fa fa-info-circle"></i> 
              <strong>Latest Membership Numbers:</strong>
              <?php if (isset($maxRMMembership) && $maxRMMembership): ?>
                  <span style="margin-left: 10px;"><strong>RM:</strong> <?= Html::encode($maxRMMembership) ?></span>
              <?php else: ?>
                  <span style="margin-left: 10px;"><strong>RM:</strong> None</span>
              <?php endif; ?>
              <?php if (isset($maxFMMembership) && $maxFMMembership): ?>
                  <span style="margin-left: 20px;"><strong>FM:</strong> <?= Html::encode($maxFMMembership) ?></span>
              <?php else: ?>
                  <span style="margin-left: 20px;"><strong>FM:</strong> None</span>
              <?php endif; ?>
          </div>
      </div>
      <?php endif; ?>
      
               <div class="col-md-12 col-sm-12 Mtopbot20"> 
               
                    <!-- Section head -->
                    <!--<div class="pagesubhead">Please select a period for adding performance data</div>-->
                    
                    <div class="blockrow">
                    
                         <!-- Tabs -->
                         <!-- Nav tabs -->
                          <ul class="nav nav-tabs" role="tablist">
                            <li role="presentation" class="active"><a href="#member" aria-controls="member" role="tab" data-toggle="tab"><?= $type=='Staff'?'Staff/Spouse':'Member/Spouse'?></a></li>
                             <?php if ($type != 'Staff'){?>  
                            <li role="presentation"><a href="#dependants" aria-controls="dependants" role="tab" data-toggle="tab">Dependents</a></li>   
                           <?php } ?>
                           <?php if ( $formType !='editmember' ) {?>                        
                            <li role="presentation"><a href="#settings" aria-controls="settings" role="tab" data-toggle="tab">Settings</a></li>
                          <?php }?>
                          </ul>
                         <input type="hidden" id='formType' name='formType' value="<?= $formType ?>">
                          <!-- Tab panes -->
                          <div class="tab-content tabcontentborder">

                                <!-- Member Tab -->
                                <div id="member" class="tab-pane fade active in" role="tabpanel">

                                 <!-- Section 1 -->


                                        <?php if ($type !="Staff"){?>
                                            <div class="col-md-12 col-sm-12">
                                            <legend>Membership Details</legend>
                                        </div>
                                    <div class="col-md-6 col-sm-6 Mtop20">
                                        <fieldset>
                                        <!-- rows -->
                                        <div class="inlinerow Mtop10">
                                            <div class="col-md-4 col-sm-5 L32">
                                                Membership no <span style="color: red;">*</span>
                                                <span class="membership-info-icon" style="display: inline-block; width: 16px; height: 16px; line-height: 16px; text-align: center; 
                                                      border-radius: 50%; background-color: #3498db; color: white; font-size: 12px; 
                                                      font-weight: bold; cursor: help; margin-left: 3px;" 
                                                      data-toggle="tooltip" data-placement="auto bottom" 
                                                      title="Membership number must start with either RM- or FM- followed by numbers. Examples: RM-1001, FM-1001">i</span>
                                            </div>
                                            <div class="col-md-8 col-sm-7">
                                            <?= $form->field($model, 'memberno')->textInput([
                                                'maxlength' => 20,
                                                'id'=>'txtMemberNo',
                                                'placeholder' => 'e.g., RM-1001 or FM-1001'
                                            ])->label(false) ?>
                                            </div>
                                        </div>
                                        <!--/.rows -->
                                        <!-- rows -->
                                      


                                        <?php if (strtolower(ExtendedInstitution::getInstitutionTypeNameFromCode($model->institution->institutiontype)) =="education"){?>
                                              <div class="inlinerow Mtop10">
                                                <div class="col-md-4 col-sm-5 L32">Batch <span style="color: red;">*</span> </div>
                                                
                                                <div class="col-md-8 col-sm-7">
                                                   
 											<?= $form->field($model, 'batch')->dropDownList(
                                                    $batches,
                                                    [
                                                    'prompt'=>'Please Select',
                                                    'id'=>'batch'
                                                    ]
                                                    )
                                                ->label(false);?>

                                                </div>
                                            </div>
                                            <?php } else  {
                                                ?>
                                                  <div class="inlinerow Mtop10">
                                                    <div class="col-md-4 col-sm-5 L32">Member since</div>
                                                    <div class="col-md-8 col-sm-7">
                                                            <?= DatePicker::widget([
                                                                'name' => 'membersince',
                                                                'id' => 'membersince',
                                                                'value' => $model->membersince?date(yii::$app->params['dateFormat']['viewDateFormat'], strtotimeNew($model->membersince)):'',
                                                                'options' => [
                                                                // 'placeholder' => 'Select End Date',
                                                                ],
                                                                'pluginOptions' => [
                                                                    'autoclose'=>true,
                                                                'format' => 'dd MM yyyy',
                                                                    'endDate' =>'0d'
                                                                ]
                                                                ]);?>
                                                    </div>
                                                </div>
                                                <?php
                                            }?>

                                        <!-- rows -->
                                        </fieldset>
                                    </div>
                                    <div class="col-md-6 col-sm-6 Mtop20">
                                        <div class="inlinerow Mtop10">
                                            <div class="col-md-4 col-sm-5 L32">Membership type <span style="color: red;">*</span></div>
                                            <div class="col-md-8 col-sm-7">
                                                 <?= $form->field($model, 'membershiptype')->dropDownList(
                                                    [
                                                        'Primary' => 'Primary',
                                                        'Fellowship' => 'Fellowship'
                                                    ],
                                                    [
                                                        'id' => 'txtMemberShip_Type',
                                                        'prompt' => 'Please Select'
                                                    ]
                                                )->label(false) ?>
                                                </div>
                                        </div>
                                                    </div>
                                                    <div class="segment">&nbsp;</div>
                                        <?php }?>
                                    <!-- Section 1 -->

                                    <div class="col-md-6 col-sm-6 Mtop20">
                                        <fieldset>
                                            <legend><?= $type =="Staff"?'Staff Details':'Member Details'?></legend>
                                            <div class="blockrow">
                                                <div class="col-md-5 col-sm-5 imagesize">
                                                    <img class="fix-size" width="184" height="184" id="memberimage" src="<?php
                                                       $image  = $model->member_pic?$model->member_pic: "/Member/default-user.png";
																			
                                                    echo Yii::$app->params['imagePath'].$image; ?>">
                                                </div>
                                                <div class="col-md-7 col-sm-7">
                                                    <div class="inlinerow">Upload <?= $type =="Staff" ? ' Staff' : 'Member'?> Photo</div>
                                                    <div class="inlinerow Mtop10">
                                                        
                                                        <?= $form->field($model, 'memberImageThumbnail')->fileInput(['class' => 'form-control','id' => 'memberfile'])->label(false); ?> 
                                                        <input type="button" title = "Remove" id="btnremovemember" value="Remove">

                                                    </div>
                                                     <?php if ($model->memberid){?>
                                                    <div class="inlinerow Mtop10">
                                                        <br>
                                                        <?php if($type !='Staff' && ( $model->spouse_firstName !='' || $model->spouse_firstName !=null)){?>
                                                        <input value="Remove Member" title = "Remove" id="btnremoveprimarymember" type="button">
                                                        <?php }?>
                                                    </div>
                                                    <?php }?>
                                                    <!-- profile url -->
                                                    <?php if ($formType =='update' && $type !="Staff"){ ?>
                                                        <div class="inlinerow Mtop10">
                                                        <input type="button" data-membertype="member" title = "Get Profile URL" value="Get Profile URL" class="btn btn-primary btngenerateprofileurl" />
                                                        </div>
                                                    <?php } ?>
                                                    <!-- /profile url -->
                                                </div>
                                            </div>
                                            <!-- rows -->
                                            

                                            <div class="inlinerow Mtop10">
                                                <div class="col-md-4 col-sm-5 L32">Title<span style="color: red;"> *</span></div>
                                                
                                                <div class="col-md-8 col-sm-7">
                                                   
 											<?= $form->field($model, 'membertitle')->dropDownList(
                                            $titlesArray,
                                            [
                                                'prompt'=>'Please Select',
                                                'id' => 'MemberTitle',		
                                            ]
                                            )
                                        ->label(false);?>

                                                </div>
                                            </div>
                                            <!--/.rows -->
                                            <!-- rows -->
                                            <div class="inlinerow Mtop10">
                                                <div class="col-md-4 col-sm-5 L32">First Name <span style="color: red;">*</span></div>
                                                <div class="col-md-8 col-sm-7">
                                                     <?= $form->field($model, 'firstName')->textInput(['maxlength' => true , 'id' => 'txtFirstName', ])->label(false) ?>
                                                </div>
                                            </div>
                                            <!--/.rows -->
                                            <!-- rows -->
                                            <div class="inlinerow Mtop10">
                                                <div class="col-md-4 col-sm-5 L32">Middle Name</div>
                                                <div class="col-md-8 col-sm-7">
                                                <?= $form->field($model, 'middleName')->textInput(['maxlength' => true,'id'=>"txtMiddleName"])->label(false) ?>    
                                                </div>
                                            </div>
                                            <!--/.rows -->
                                            <!-- rows -->
                                            <div class="inlinerow Mtop10">
                                                <div class="col-md-4 col-sm-5 L32">Last Name <span style="color: red;">*</span></div>
                                                <div class="col-md-8 col-sm-7">
                                                 <?= $form->field($model, 'lastName')->textInput(['maxlength' => true , 'id' => 'txtLastName' ])->label(false) ?>
                                                 </div>
                                            </div>
                                            <!--/.rows -->
                                            <!-- rows -->
                                            <div class="inlinerow Mtop10">
                                                <div class="col-md-4 col-sm-5 L32">Nick/AKA</div>
                                                <div class="col-md-8 col-sm-7">
                                                   
    												<?= $form->field($model, 'membernickname')->textInput(['maxlength' => true,'id'=>'txtMemberNickName'])->label(false) ?>
                                                   </div>
                                            </div>
                                            <!--/.rows -->
                                            <!-- rows -->
                                            <div class="inlinerow Mtop10">
                                                <div class="col-md-4 col-sm-5 L32">Email </div>
                                                <div class="col-md-8 col-sm-7">
                                                 <?= $form->field($model, 'member_email')->textInput(['maxlength' => true ,'id' => 'txtMemberEmail'])->label(false) ?>
                                                </div>
                                            </div>
                                    <?php if ($type !="Staff"){ ?>
                                            <div class="inlinerow Mtop10">
                                                <div class="col-md-4 col-sm-5 L32">Keep number private</div>
                                                <div class="col-md-8 col-sm-7">
                                                    
                                                    <?= $form->field($settingsModel, 'membermobilePrivacyEnabled')->checkbox(array('label'=>''))->label(false); ?>
                                                  
                                                </div>
                                            </div>
                                         <?php }?>   
                                            <!--/.rows -->
                                            <!-- rows -->
                                            <div class="inlinerow Mtop10">
                                                <div class="col-md-4 col-sm-5 L32">Mobile <span style="color: red;">*</span> </div>
                                                    <div class="col-md-8 col-sm-12 phone-div">
                                                <?= $form->field($model, 'member_mobile1_countrycode')->textInput(['maxlength' => '4' ,'class' => 'form-control citycodes' , 'id' => 'txtMemberMobile1_countrycode'])->label(false) ?>
                                                <?= $form->field($model, 'member_mobile1')->textInput(['maxlength' => '10', 'class' =>'form-control fullnumber-affiliated'  , 'id' => 'txtMemberMobile1' ])->label(false) ?> 
                                            
                                                </div>
                                            </div>
                                            <!--/.rows -->
                                            <!-- rows -->
                                            <!--/.rows -->
                                            <!-- rows -->
                                            <div class="inlinerow Mtop10">
                                                <div class="col-md-4 col-sm-5 L32">DOB</div>
                                                <div class="col-md-8 col-sm-7">          
                                        <?= DatePicker::widget([
				                            'name' => 'member_dob',
                                     	    'id' => 'txtMemberDOB',
                                            'value' => $model->member_dob?date(yii::$app->params['dateFormat']['viewDateFormat'], strtotimeNew($model->member_dob)):'',
				                        'options' => [
				                           // 'placeholder' => 'Select End Date',
				                        ],
				                        'pluginOptions' => [
				                            'autoclose'=>true,
				                            'format' => 'dd MM yyyy',
				                            'endDate' =>'0d'
				                    	]
				                        ]); ?>
				                    
                                                    </div>
                                            </div>
                                            <?php if ($type !="Staff" && strtolower(ExtendedInstitution::getInstitutionTypeNameFromCode($model->institution->institutiontype)) == "church" ) {?>
                                            <div class="inlinerow Mtop10">
                                                <div class="col-md-4 col-sm-5 L32">Home church</div> 
                                                <div class="col-md-8 col-sm-7">
                                                    <?= $form->field($model, 'homechurch')->textInput(['maxlength' => true])->label(false) ?>
                                                    </div>
                                            </div>
                                          <?php }?>
                                            <div class="inlinerow Mtop10">
                                                <div class="col-md-4 col-sm-5 L32">Occupation</div>
                                                <div class="col-md-8 col-sm-7">
                                                    <?= $form->field($model, 'occupation')->textInput(['maxlength' => true,'id'=>'txtOccupation'])->label(false) ?>
                                                    </div>
                                            </div>
                                            
                                            <?php if ($type !="Staff"){?>
                                              <div class="inlinerow Mtop10">
                                                <div class="col-md-4 col-sm-5 L32">Active</div>
                                                <div class="col-md-8 col-sm-7">
                                                    <?= $form->field($model, 'active')->checkbox(['id'=>'chkMemberActive', 'label' => ''])->label(false) ?>
                                                </div>
                                            </div>
                                            
                                              <div class="inlinerow Mtop10">
                                                <div class="col-md-4 col-sm-5 L32">Confirmed</div>
                                                <div class="col-md-8 col-sm-7">
                                                    <?= $form->field($model, 'confirmed')->checkbox(['id'=>'chkMemberConfirmed', 'label' => ''])->label(false) ?>
                                                </div>
                                            </div>
                                            <?php }?>
                                              
                                              <?php if ($type !="Staff"){?>
                                              
                                              <?php if (strtolower(ExtendedInstitution::getInstitutionTypeNameFromCode($model->institution->institutiontype)) == "church"){?>
                                              <div class="inlinerow Mtop10">
                                                <div class="col-md-4 col-sm-5 L32">Family Unit </div>
                                                
                                                <div class="col-md-8 col-sm-7">
                                                   
 											<?= $form->field($model, 'familyunitid')->dropDownList(
                                                    $familyUnitsArray,
                                                    [
                                                    'prompt'=>'Please Select'
                                                    ]
                                                    )
                                                ->label(false);?>

                                                </div>
                                            </div>

                                            <div class="inlinerow Mtop10">
                                                <div class="col-md-4 col-sm-5 L32">Zone </div>
                                                
                                                <div class="col-md-8 col-sm-7">
                                                   
 											<?= $form->field($model, 'zone_id')->dropDownList(
                                                    $zonesArray,
                                                    [
                                                    'prompt'=>'Please Select'
                                                    ]
                                                    )
                                                ->label(false);?>

                                                </div>
                                            </div>
                                            <?php }?>

                                           


                                              <div class="inlinerow Mtop10">
                                                <div class="col-md-4 col-sm-5 L32">Blood Group </div>
                                                
                                                <div class="col-md-8 col-sm-7">
                                                   
 											<?= $form->field($model, 'memberbloodgroup')->dropDownList(
                                            $bloodGroup,
                                            [
                                            'prompt'=>'Please Select',
                                             'id' => 'txtMemberBloodgroup'		
                                            ]
                                            )
                                        ->label(false);?>

                                                </div>
                                            </div>


                                            <div class="inlinerow Mtop10">
                                                <div class="col-md-4 col-sm-5 L32">Directory Number </div>
                                                
                                                <div class="col-md-8 col-sm-7">
                                                   
 											<?= $form->field($model, 'directory_number')->textInput(
                                            [
                                            'maxlength' => true,
                                            'id' => 'txtMemberDirectoryNumber'
                                            ])->label(false);?>

                                                </div>
                                            </div>
                                        <?php  if ($model->institution->tagcloud) { ?>
                                             <div class="inlinerow Mtop10">
                                                <div class="col-md-4 col-sm-5 L32">TagCloud</div>
                                                   <div class="col-md-8 col-sm-7">
                                                    <?= $form->field($memberadditionalModal, 'tagcloud')->textInput(
                                                    [
                                                        'maxlength' => true,
                                                        'data-role' => 'tagsinput',
                                                        'id' => 'tag-cloud-input',
                                                        'value' => $memberadditionalModal->tagcloud
                                                    ])->label(false);?>           
                                                </div>
                                            </div>
                                        <?php }
                                        } else {?>
                                             <div class="inlinerow Mtop10">
                                                <div class="col-md-4 col-sm-5 L32">Designation  <span style="color: red;">*</span></div>
                                                <div class="col-md-8 col-sm-7">
 											<?= $form->field($model, 'staffdesignation')->dropDownList(
                                            $bloodGroup,
                                            [
                                                'prompt'=> 'Please Select',
                                                'id' => 'StaffDesignation'		
                                            ]
                                            )->label(false);?>
                                            </div>
                                            </div>
                                            <?php }?>
                                            <!--/.rows -->
                                        </fieldset>
                                    </div>
                                    <!-- /.Section 1 -->

                                    <!-- Section 2 -->
                                    <div class="col-md-6 col-sm-6 Mtop20">
                                        <fieldset>

                                            <legend>Spouse Details</legend>
                                            <div class="blockrow">
                                                <div class="col-md-5 col-sm-5 imagesize">
                                                    <img class="fix-size" width="184" height="184" id="spouseimage" src= "<?php $image  = $model->spouse_pic?$model->spouse_pic: "/Member/default-user.png";
																			
                                                    echo Yii::$app->params['imagePath'].$image; ?>">
                                                </div>
                                                <div class="col-md-7 col-sm-7">
                                                    <div class="inlinerow ">Upload Spouse Photo</div>
                                                    <div class="inlinerow Mtop10">
                                                       <?= $form->field($model, 'spouseImageThumbnail')->fileInput(['maxlength' => true , 'id' => 'spousefile' ,'class' => 'form-control' ])->label(false) ?>
                                                        <!-- <br> -->
                                                        <input type="button" title = "Remove" id="btnspouseremove" value="Remove">
                                                        <?php if ($model->memberid){?>
                                                        <div class="inlinerow Mtop10">
                                                        <br>
                                                         <?php if($type !='Staff' && ( $model->spouse_firstName !='' || $model->spouse_firstName !=null)){?>
                                                        <input value="Remove Spouse" title = "Remove" id="btnremovespouse" type="button">
                                                        <?php }?>
                                                        </div>
                                                        <?php }?>

                                                    </div>
                                                    <div class="inlinerow Mtop10">
                                                        <br>
                                                        
                                                    </div>


                                                    <!-- profile url -->
                                                    <?php if ($formType =='update' && $type !="Staff" && ($model->spouse_firstName !='' || $model->spouse_firstName !=null)){ ?>
                                                        <div class="inlinerow Mtop10">
                                                            <input type="button" data-membertype="spouse" title = "Get Profile URL" value="Get Profile URL" class="btn btn-primary btngenerateprofileurl" />
                                                        </div>
                                                    <?php } ?>
                                                    <!-- /profile url -->
                                                    
                                                </div>
                                            </div>

                                            <!-- rows -->
                                            <div class="inlinerow Mtop10">
                                                <div class="col-md-4 col-sm-5 L32">Title<span id="spouse-title-required" style="color: red; display: none;"> *</span></div>
                                                <div class="col-md-8 col-sm-7">

    
     											<?= $form->field($model, 'spousetitle')->dropDownList(
                                            $titlesArray,
                                            [
                                            'prompt'=>'Please Select',
                                            'id' =>'SpouseTitle'		
                                            ]
                                            )
                                        ->label(false);?>
    
                                                    </div>
                                            </div>
                                            <!-- rows -->

                                            <!-- rows -->
                                            <div class="inlinerow Mtop10">
                                                <div class="col-md-4 col-sm-5 L32">First Name <span id="spouse-firstname-required" style="color: red; display: none;">*</span></div>
                                                <div class="col-md-8 col-sm-7">
                                                	<?= $form->field($model, 'spouse_firstName')->textInput(['maxlength' => true, 'id' => 'txtMemberSpouseFirstName'])->label(false) ?>
                                                    </div>
                                            </div>
                                            <!--/.rows -->
                                            <!-- rows -->
                                            <div class="inlinerow Mtop10">
                                                <div class="col-md-4 col-sm-5 L32">Middle Name</div>
                                                <div class="col-md-8 col-sm-7">
                                                <?= $form->field($model, 'spouse_middleName')->textInput(['maxlength' => true,'id'=>"txtMemberSpouseMiddleName"])->label(false) ?>
                                                </div>
                                            </div>
                                            <!--/.rows -->
                                            <!-- rows -->
                                            <div class="inlinerow Mtop10">
                                                <div class="col-md-4 col-sm-5 L32">Last Name <span id="spouse-lastname-required" style="color: red; display: none;">*</span></div>
                                                <div class="col-md-8 col-sm-7">
                                                 <?= $form->field($model, 'spouse_lastName')->textInput(['maxlength' => true,'id'=>'txtMemberSpouseLastName'])->label(false) ?>
                                                </div>
                                            </div>
                                            <!--/.rows -->
                                            <!-- rows -->
                                            <div class="inlinerow Mtop10">
                                                <div class="col-md-4 col-sm-5 L32">Nick/AKA</div>
                                                <div class="col-md-8 col-sm-7">
                                                    <?= $form->field($model, 'spousenickname')->textInput(['maxlength' => true,'id'=>"txtSpouseNickName"])->label(false) ?>
                                                </div>
                                            </div>
                                            <!--/.rows -->
                                            <!-- rows -->
                                            <div class="inlinerow Mtop10">
                                                <div class="col-md-4 col-sm-5 L32">Email</div>
                                                <div class="col-md-8 col-sm-7">
                                                     <?= $form->field($model, 'spouse_email')->textInput(['maxlength' => true ,'id' =>'txtMemberspouseemail'])->label(false) ?>
                                                </div>
                                            </div>
                                            <!--/.rows -->
                                            <!-- rows -->
                                          <?php if ( $type !="Staff"){?>
                                            <div class="inlinerow Mtop10">
                                                <div class="col-md-4 col-sm-5 L32">Keep number private</div>

                                                <div class="col-md-8 col-sm-7">
                                                    
                                                     <?= $form->field($settingsModel, 'spousemobilePrivacyEnabled')->checkbox(array('label'=>''))->label(false); ?> 
                                                </div>
                                            </div>
                                           <?php }?> 

                                           <div class="inlinerow Mtop10">
                                                <div class="col-md-4 col-sm-5 L32">Mobile<span id="spouse-mobile-countrycode-required" style="color: red; display: none;"> *</span></div>
                                                    <div class="col-md-8 col-sm-12 phone-div">
                                                <?= $form->field($model, 'spouse_mobile1_countrycode')->textInput(['maxlength' => '4' ,'class' => 'form-control citycodes' ,'id'=>'txtspousemobile1_countrycode' ])->label(false) ?>
                                                <?= $form->field($model, 'spouse_mobile1')->textInput(['maxlength' => '10', 'class' =>'form-control fullnumber-affiliated phone-margin', 'id'=> 'txtspousemobile1' ])->label(false) ?>
                                                </div>
                                            </div>
                                            <!--/.rows -->
                                            <!-- rows -->
                                            <div class="inlinerow Mtop10">
                                                <div class="col-md-4 col-sm-5 L32">DOB</div>
                                                <div class="col-md-8 col-sm-7">
                                                 <?= DatePicker::widget([
				                                    'name' => 'spouse_dob',
                                     	            'id' => 'txtSpouseDOB',
                                                    'value' => $model->spouse_dob?date(yii::$app->params['dateFormat']['viewDateFormat'], strtotimeNew($model->spouse_dob)):'',
            				                        'options' => [
            				                           // 'placeholder' => 'Select End Date',
            				                        ],
                				                  	  'pluginOptions' => [
                				                            'autoclose'=> true,
                				                            'format' => 'dd MM yyyy',
                				                            'endDate' => '0d'
                				                    	]
                				                    ]); ?>
                                                    </div>
                                            </div>

                                            <div class="inlinerow Mtop10">
                                                <div class="col-md-4 col-sm-5 L32">Occupation</div>
                                                <div class="col-md-8 col-sm-7">
                                                <?= $form->field($model, 'spouseoccupation')->textInput(['maxlength' => true,'id'=>"txtSpouseOccupation"])->label(false) ?>
                                                    </div>
                                            </div>
                                            
                                            <?php if ($type !="Staff"){?>
                                              <?php 
                                              // Set active_spouse and confirmed_spouse to 0 if spouse details are not provided
                                              if (empty($model->spousetitle) || empty($model->spouse_firstName)) {
                                                  $model->active_spouse = 0;
                                                  $model->confirmed_spouse = 0;
                                              }
                                              ?>
                                              <div class="inlinerow Mtop10">
                                                <div class="col-md-4 col-sm-5 L32">Active Spouse</div>
                                                <div class="col-md-8 col-sm-7">
                                                    <?= $form->field($model, 'active_spouse')->checkbox(['id'=>'chkSpouseActive', 'label' => ''])->label(false) ?>
                                                </div>
                                            </div>
                                            
                                              <div class="inlinerow Mtop10">
                                                <div class="col-md-4 col-sm-5 L32">Confirmed Spouse</div>
                                                <div class="col-md-8 col-sm-7">
                                                    <?= $form->field($model, 'confirmed_spouse')->checkbox(['id'=>'chkSpouseConfirmed', 'label' => ''])->label(false) ?>
                                                </div>
                                            </div>
                                            
                                            <div class="inlinerow Mtop10">
                                                <div class="col-md-4 col-sm-5 L32">Head of Family</div>
                                                <div class="col-md-8 col-sm-7">
                                                    <?php 
                                                    // Set default to 'Member' if not set
                                                    if (empty($model->head_of_family)) {
                                                        $model->head_of_family = 'm';
                                                    }
                                                    ?>
                                                    <?= $form->field($model, 'head_of_family')->radioList(
                                                        ['m' => 'Member', 's' => 'Spouse'],
                                                        ['inline' => true, 'id' => 'headOfFamily']
                                                    )->label(false) ?>
                                                </div>
                                            </div>
                                            <?php }?>
                                            
                                            <?php if ($type !="Staff"){?>
                                                 <div class="inlinerow Mtop10">
                                                <div class="col-md-4 col-sm-5 L32">Blood Group </div>
                                                
                                                <div class="col-md-8 col-sm-7">
                                                   
 											<?= $form->field($model, 'spousebloodgroup')->dropDownList(
                                            $bloodGroup,
                                            [
                                            'prompt'=>'Please Select',
                                            'id' => 'txtSpouseBloodgroup',		
                                            ]
                                            )
                                        ->label(false);?>

                                                </div>
                                            </div>
										<?php }?>
                                            <!--/.rows -->

                                            <div class="inlinerow Mtop10" id="date-of-wedding" style="display: none;">
                                            <div class="col-md-4 col-sm-5 L32">Wedding Anniversary</div>
                                            <div class="col-md-8 col-sm-7">
                                            
                                            <?= DatePicker::widget([
        				                        'name' => 'dom',
                                             	'id' => 'dom',
                                                'value' => ($model->dom ) ? date(yii::$app->params['dateFormat']['viewDateFormat'], strtotimeNew($model->dom)):'',
        				                        'options' => [
        				                           // 'placeholder' => 'Select End Date',
        				                        ],
            				                    'pluginOptions' => [
            				                            'autoclose'=>true,
            				                            'format' => 'dd MM yyyy',
            				                            'endDate' =>'0d'
            				                    	]
            				                    ]);?>
				                    
                                                </div>
                                        </div>
                                        </fieldset>
                                    </div>
                                    <!-- /.Section 2 -->

                                    <!-- Separator -->
                                    <div class="segment">&nbsp;</div>
                                    <div class="col-md-6 col-sm-6 location-details" style="display: none;">

                                        <fieldset>
                                            <legend>Location Details</legend>

                                            <div class="inlinerow Mtop10">
                                                <div class="col-md-4 col-sm-5 L32">Latitude</div>
                                                   <div class="col-md-8 col-sm-7">
                                                    <?= $form->field($model, 'latitude')->textInput(
                                                    [
                                                        'minlength' => 1, 
                                                        'min' => -90, 
                                                        'max' => 90,
                                                        'data-role' => 'latitude',
                                                        'id' => 'txtlatitude',
                                                        'placeholder'=> 'Enter Latitude',
                                                        'value' => $model->latitude
                                                    ])->label(false);?>           
                                                </div>
                                            </div>      
                                            <div class="inlinerow Mtop10">
                                                <div class="col-md-4 col-sm-5 L32">Longitude</div>
                                                   <div class="col-md-8 col-sm-7">
                                                    <?= $form->field($model, 'longitude')->textInput(
                                                    [
                                                        'minlength' => 1, 
                                                        'min' => -180, 
                                                        'max' => 180,
                                                        'data-role' => 'longitude',
                                                        'id' => 'txtlongitude',
                                                        'placeholder'=> 'Enter Longitude',
                                                        'value' => $model->longitude
                                                    ])->label(false);?>           
                                                </div>
                                            </div>                                                  

                                        </fieldset>
                                        </div>
                                        <!--/.rows -->
                                        <!-- rows -->
                                    <!-- /. separate section -->
                                    <!-- Separator -->
                                    <div class="segment">&nbsp;</div>

                                    <div class="col-md-6 col-sm-6">

                                        <fieldset>
                                            <legend>Business Details</legend>
                                            <!--/.rows -->
                                            <!-- rows -->
                                            <div class="inlinerow Mtop10">
                                                <div class="col-md-4 col-sm-5 L32">Address Line 1</div>
                                                <div class="col-md-8 col-sm-7">
                                                <?= $form->field($model, 'business_address1')->textarea(['maxlength' => true])->label(false) ?>
                                                
                                                  </div>
                                            </div>
                                            <!--/.rows -->
                                            <!-- rows -->
                                            <div class="inlinerow Mtop10">
                                                <div class="col-md-4 col-sm-5 L32">Address Line 2</div>
                                                <div class="col-md-8 col-sm-7">
                                                <?= $form->field($model, 'business_address2')->textarea(['maxlength' => true])->label(false) ?>
                                                </div>
                                            </div>

                                            <!--/.rows -->
                                            <!-- rows -->
                                            <div class="inlinerow Mtop10">
                                                <div class="col-md-4 col-sm-5 L32">Postal Code</div>
                                                <div class="col-md-8 col-sm-7">
                                                  <?= $form->field($model, 'business_pincode')->textInput(['maxlength' => true])->label(false) ?>
                                                   </div>
                                            </div>
                                            <!--/.rows -->
                                            <!-- rows -->
                                            <div class="inlinerow Mtop10">
                                                <div class="col-md-4 col-sm-5 L32">District</div>
                                                <div class="col-md-8 col-sm-7">
                                                <?= $form->field($model, 'business_district')->textInput(['maxlength' => true])->label(false) ?>
                                                   </div>
                                            </div>
                                            <!--/.rows -->
                                            <!-- rows -->
                                            <div class="inlinerow Mtop10">
                                                <div class="col-md-4 col-sm-5 L32">State</div>
                                                <div class="col-md-8 col-sm-7">
                                                 <?= $form->field($model, 'business_state')->textInput(['maxlength' => true])->label(false) ?>
                                                </div>
                                            </div>

                                            <!--/.rows -->
                                            <!-- rows -->
                                            <div class="inlinerow Mtop10">
                                                <div class="col-md-4 col-sm-5 L32">Office Phone 1</div>
                                                <div class="col-md-8 col-sm-12 phone-div">
                                                	<?= $form->field($model, 'member_business_phone1_countrycode')->textInput(['maxlength' => '4' ,'class' => 'form-control citycodes' ,'id'=>'txtMemberBusinessPhone1_countrycode' ])->label(false) ?>
                                                	<?= $form->field($model, 'member_business_phone1_areacode')->textInput(['maxlength' => '5' ,'class' => 'form-control citycodes' ,'id' => 'txtMemberBusinessPhone1_areacode'])->label(false) ?>
													<?= $form->field($model, 'member_musiness_Phone1')->textInput(['maxlength' => '10' ,'class' => 'form-control fullnumber' ,'id' => 'txtMemberBusinessPhone1'])->label(false) ?>
													
                                                </div>
                                                
                                            </div>
                                            <!--/.rows -->
                                            <!-- rows -->
                                              <div class="inlinerow Mtop10">
                                                <div class="col-md-4 col-sm-5 L32 ">Office Phone 2</div>
                                                <div class="col-md-8 col-sm-12 phone-div">
                                                	<?= $form->field($model, 'member_business_phone3_countrycode')->textInput(['maxlength' => '4' ,'class' => 'form-control citycodes' ,'id'=>'txtMemberBusinessPhone3_countrycode' ])->label(false) ?>
                                                	<?= $form->field($model, 'member_business_phone3_areacode')->textInput(['maxlength' => '5' ,'class' => 'form-control citycodes' ,'id'=>'txtMemberBusinessPhone3_areacode' ])->label(false) ?>
													<?= $form->field($model, 'member_business_Phone3')->textInput(['maxlength' => '10' ,'class' => 'form-control fullnumber','id'=>'txtMemberBusinessPhone3' ])->label(false) ?>
													
                                                </div>
                                                
                                            </div>
                                            <div class="inlinerow Mtop10">
                                                <div class="col-md-4 col-sm-5 L32">Email</div>
                                                <div class="col-md-8 col-sm-7">
                                                <?= $form->field($model, 'businessemail')->textInput(['maxlength' => true,'id'=>'txtbusinessEmail'])->label(false) ?>
                                                   </div>
                                            </div>



                                            <!--/.rows -->
                                        </fieldset>
                                    </div>
                                    <!-- /.Section 1 -->
                                    <!-- Section 2 -->
                                    <div class="col-md-6 col-sm-6">

                                        <fieldset>
                                            <legend>Residence Details</legend>
                                            <!--/.rows -->
                                            <!-- rows -->
                                            <div class="inlinerow Mtop10">
                                                <div class="col-md-4 col-sm-5 L32">Address Line 1</div>
                                                <div class="col-md-8 col-sm-7">
                                                  <?= $form->field($model, 'residence_address1')->textarea(['maxlength' => true])->label(false) ?>
                                                   </div>
                                            </div>
                                            <!--/.rows -->
                                            <!-- rows -->
                                            <div class="inlinerow Mtop10">
                                                <div class="col-md-4 col-sm-5 L32">Address Line 2</div>
                                                <div class="col-md-8 col-sm-7">
                                                  <?= $form->field($model, 'residence_address2')->textarea(['maxlength' => true])->label(false) ?>
                                                  </div>
                                            </div>

                                            <!--/.rows -->
                                            <!-- rows -->
                                            <div class="inlinerow Mtop10">
                                                <div class="col-md-4 col-sm-5 L32">Postal Code</div>
                                                <div class="col-md-8 col-sm-7">
                                                    <?= $form->field($model, 'residence_pincode')->textInput(['maxlength' => true])->label(false) ?>
                                                    </div>
                                            </div>

                                            <!--/.rows -->
                                            <!-- rows -->
                                            <div class="inlinerow Mtop10">
                                                <div class="col-md-4 col-sm-5 L32">District</div>
                                                <div class="col-md-8 col-sm-7">
                                                <?= $form->field($model, 'residence_district')->textInput(['maxlength' => true])->label(false) ?>
                                                    </div>
                                            </div>
                                            <!--/.rows -->
                                            <!-- rows -->
                                            <div class="inlinerow Mtop10">
                                                <div class="col-md-4 col-sm-5 L32">State</div>
                                                <div class="col-md-8 col-sm-7">
                                                    <?= $form->field($model, 'residence_state')->textInput(['maxlength' => true])->label(false) ?>
                                                    
                                                    </div>
                                            </div>
											<div class="inlinerow Mtop10">
                                                <div class="col-md-4 col-sm-5 L32">Land Line Number</div>
                                                <div class="col-md-8 col-sm-12 phone-div">
                                                	<?= $form->field($model, 'member_residence_Phone1_countrycode')->textInput(['maxlength' => '4' ,'class' => 'form-control citycodes' ,'id' =>'txtMemberResidencePhone1_countrycode' ])->label(false) ?>
                                                	<?= $form->field($model, 'member_residence_phone1_areacode')->textInput(['maxlength' => '5' ,'class' => 'form-control citycodes','id'=>'txtMemberResidencePhone1_areacode' ])->label(false) ?>
													<?= $form->field($model, 'member_residence_Phone1')->textInput(['maxlength' => '10' ,'class' => 'form-control fullnumber'  ,'id' =>'txtMemberResidencePhone1'])->label(false) ?>
													
                                                </div>
                                                
                                            </div>
                                            <!--/.rows -->
                                            <!-- rows -->
                                            <div style="display: none;" class="inlinerow Mtop10">
                                                <div class="col-md-4 col-sm-5 L32">TelePhone 1</div>
                                                <div class="col-md-8 col-sm-7">
                                                    <input type="text" value="" name="MemberResidencePhone1" maxlength="13" id="txtMemberResidencePhone1" class="form-control" autocomplete="off">
                                                </div>
                                            </div>
                                            <!--/.rows -->
                                            <!-- rows -->
                                            <div style="display: none;" class="inlinerow Mtop10">
                                                <div class="col-md-4 col-sm-5 L32">TelePhone 2</div>
                                                <div class="col-md-8 col-sm-7">
                                                    <input type="text" value="" name="MemberResidencePhone2" maxlength="13" id="txtMemberResidencePhone2" class="form-control" autocomplete="off">
                                                </div>
                                            </div>
                                            <div class="inlinerow Mtop10">
                                                <div class="col-md-4 col-sm-5 L32"></div>
                                                <div class="col-md-8 col-sm-7">
                                                </div>
                                            </div>
                                            <div class="inlinerow Mtop10">
                                                <div class="col-md-4 col-sm-5 L32"></div>
                                                <div class="col-md-8 col-sm-7">
                                                </div>
                                            </div>
                                            <div class="inlinerow Mtop10">
                                                <div class="col-md-4 col-sm-5 L32"></div>
                                                <div class="col-md-8 col-sm-7">
                                                </div>
                                            </div>
                                            <div class="inlinerow Mtop10">
                                                <div class="col-md-4 col-sm-5 L32"></div>
                                                <div class="col-md-8 col-sm-7">
                                                    <input type="button"  title ="Next" id="Next<?= $type=='Staff'?'2':1 ?>" value="Next" class="btn btn-success btn-lg">
                                                </div>
                                            </div>

                                        </fieldset>
                                        <!--/.rows -->
                                    </div>
                                    <!-- /.Section 2 -->


                                </div>
                                <!-- /.Member Tab -->
                                <!-- Dependants Tab -->
                                <div id="dependants" class="tab-pane fade" role="tabpanel">
                                    <div class="inlinerow Mtop20">
                                       <fieldset>
                                      <?php if ($type != 'Staff'){?>    
         				                         <div id="divdependants">           
												                <?= $dependantDetails ?>
											          </div>
											<?php } ?>
											<?php if ($formType != 'editmember') { ?>
                                            <div class="inlinerow Mtop10">

                                                <div class="col-md-3">
                                                </div>
                                                <div class="col-md-2"></div>
                                                <div class="col-md-3">
                                                    <input type="button" title ="Next" id="Next2" value="Next" class="btn btn-success btn-lg">

                                                </div>
                                            </div>
                                            
                                            <?php }else{ ?>
                                            
                                    		<div class="inlinerow Mtop20">

                                        <div class="col-md-5 col-sm-5"></div>
                                        <div class="col-md-4 col-sm-4">
										 <div class="form-group">
										        <?= Html::button('Save', ['class' => 'btn btn-success btn-lg valid','title' => "Save"]) ?>
										    </div>   
                            </div>
                                  </div>   
                            <?php } ?>
                          </fieldset>
                                    </div>
                                </div>
                                <!-- /.Dependants Tab -->

                                <!-- Settings Tab -->
                                <div id="settings" class="tab-pane fade" role="tabpanel">
                                    <div class="inlinerow Mtop20">
                                        <div class="col-md-4 col-sm-4">Communication Address</div>
                                        <div class="col-md-4 col-sm-4">
                                            
                                           <?= $form->field($settingsModel, 'addresstypeid')->dropDownList(
                                           						 $addressTypes,
                                            							[
                                           						 'prompt'=>'Please Select',
                                            						'value' => ($settingsModel->isNewRecord || empty($settingsModel->addresstypeid))? 1 : $settingsModel->addresstypeid
                                            					]
                                            					)
                                        					->label(false);?>
                                        </div>
                                    </div>
                                    <div class="inlinerow Mtop20">
                                        <table cellspacing="0" cellpadding="0" class="table">
                                            <tbody><tr>
                                                <th></th>
                                                <th>Member</th>
                                                <th>Spouse</th>
                                            </tr>
                                            <tr>
                                                <td>Institution  Notification</td>
                                                <td>
                                                <?= $form->field($settingsModel, 'membernotification')->checkbox(array('label'=>''))->label(false); ?></td>
                                                <td>
                                                <?= $form->field($settingsModel, 'spousenotification')->checkbox(array('label'=>''))->label(false); ?></td>
                                            </tr>
                                            <tr>
                                                <td>Birthday Notification</td>
                                                <td>
                                                    
                                                    <?= $form->field($settingsModel, 'birthday')->checkbox(array('label'=>''))->label(false); ?></td>
                                                <td>
                                                    
                                                    <?= $form->field($settingsModel, 'spousebirthday')->checkbox(array('label'=>''))->label(false); ?></td>

                                            </tr>
                                            <tr>
                                                <td>Anniversary Notification</td>
                                                <td>
                                                    
                                                    <?= $form->field($settingsModel, 'anniversary')->checkbox(array('label'=>''))->label(false); ?></td>
                                                <td>
                                                    
                                                   <?= $form->field($settingsModel, 'spouseanniversary')->checkbox(array('label'=>''))->label(false); ?></td>

                                            </tr>
                                            
                                            <tr>
                                                <td>Email Notification</td>
                                                <td>
                                                    
                                                    <?= $form->field($settingsModel, 'memberemail')->checkbox(array('label'=>''))->label(false); ?>
                                                    </td>
                                                <td>
                                                    
                                                    <?= $form->field($settingsModel, 'spouseemail')->checkbox(array('label'=>''))->label(false); ?>
                                                    </td>

                                            </tr>
                                        </tbody></table>
                                    </div>
                                       <div class="inlinerow Mtop20">
<table class="table Mtop20" cellspacing="0" cellpadding="0">
   <tbody>
      <tr>
         <td colspan="3"><strong>Member Role</strong></td>
      </tr>
      <tr>
         <td width="50%">Role Category <span style="color: red;">*</span></td>
         <td width="25%">
            <?= $form->field($settingsModel, 'role_category') ->dropDownList(
                      $roleCategories,
                      [
                          'id' => 'role-category',
                          'prompt' => 'Please Select',
                          'class' => 'form-control',
                          'options' => [
                              isset($selectedMemberCat) ? $selectedMemberCat : "" => array('selected' =>true) 
                          ]
                      ]
              )->label(false);
            ?>
         </td>

         <td width="25%">
            <?= $form->field($settingsModel, 'spouse_role_category') ->dropDownList(
                    $roleCategories,
                      [
                          'id'=>'spouse-role-category',
                          'prompt'=>'Please Select',
                          'class' => 'form-control spouse-role-category',
                          'disabled' => true,
                          'options' => [
                              isset($selectedSpouseCat) ? $selectedSpouseCat : "" => array('selected' =>true) 
                          ]
                      ]
              )->label(false);
            ?>
         </td>
      </tr>
      <tr>
         <td>Role <span style="color: red;">*</span></td>
         <td>
          <?= $form->field($settingsModel, 'member_role') ->dropDownList(
                      [],
                      [
                          'id'=>'member-role',
                          'prompt'=>'Please Select',
                          'class' => 'form-control'
                      ]
                )->label(false);
            ?>
         </td>
         <td>
            <?= $form->field($settingsModel, 'spouse_role') ->dropDownList(
                      [],
                      [
                          'id'=>'spouse-role',
                          'prompt'=>'Please Select',
                          'class' => 'form-control spouse-role',
                          'disabled' => true
                      ]
                )->label(false);
            ?>
         </td>
      </tr>
   </tbody>
</table>
</div>
                                    <?php ActiveForm::end(); ?>
                                    <div class="inlinerow Mtop20">

                                        <div class="col-md-5 col-sm-5"></div>
                                        <div class="col-md-4 col-sm-4">
										 <div class="form-group">
										        <?= Html::button('Save', ['class' => 'btn btn-success btn-lg valid submit','title'=> "Save"]) ?>
										    </div>
                                        
                                           
                                        </div>
                                    </div>
                                    
                                </div>
                                <!-- /.Settings Tab -->
                                <?php if ($formType =='update' && $type !="Staff" && false){ ?>
                                   <div class="col-md-12 col-sm-12 Mtop20">
                                    <fieldset>
                                        <legend>Generate Link</legend>
                                        <div class="row">
                                            <div class="col-md-6 col-sm-6">
                                                <input autocomplete="off" class="form-control" id="txtgeneratedurl" maxlength="45" name="memberediturl" type="text" value="" />
                                                
                                            </div>
                                            <div class="col-md-6 col-sm-6">
                                                <input type="button" title = "Generate" value="Generate" class="btn btn-primary" id="btngenerateurl" />
                                                &nbsp;
                            					<input type="button" title = "Send" value="Send via email" class="btn btn-success" id="btnsentmail" style="display: none;"/>
                                            </div>
                                        </div>
                                    </fieldset>

                                </div>
                                <?php } ?>
                                
                            </div>
                                
                            </div>
                         <!-- /.Tabs -->
                         
                         <!-- Save -->
                
                    </div><!-- /. section closed -->
               </div> 
          </div>
</div>

<!-- Modal -->
<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Member Profile</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">

        <div class="container-fluid">
            <div class="row">
                <div class="col-md-4">            
                        <img class="fix-size" width="184" height="184" id="memberProfileImage" src="">
                </div>
                <div class="col-md-8">
                    <legend id="profileMemberName">Member Name</legend> 
                    <p><?= $model->residence_address1?></p>
                    <p><?= $model->residence_address2?></p>
                    <p><?= $model->residence_pincode?></p>
                    <p><?= $model->residence_district?></p>
                    <p><?= $model->residence_state?></p>                             
                </div>
            
            </div> 
            <div class="row">
                <div class="segment">&nbsp;</div> 
                <input type="hidden" id="profileMemberUrlCopied" value="">
                URL: <input type="text" id="profileMemberUrl" value="No Url found" style="width: 90%;">
            </div>
        </div>
       

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
