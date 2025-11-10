<?php 

use yii\helpers\Html;
use backend\assets\AppAsset;
use yii\bootstrap\ActiveForm;
use backend\components\widgets\FlashResult;

$assetName = AppAsset::register($this);

$this->title = 'Admin Profile';

$this->registerJsFile(
    $assetName->baseUrl . '/theme/js/Remember.account.ui.js',
    [
        'depends' => [
                AppAsset::className()
        ]
    ]
);

?>
<!-- Header -->
<div class="col-md-12 col-sm-12 col-xs-12 pageheader Mtop15">
    <?= $this->title ?>
</div>
<div class="extended-userprofile-index">
            <!-- Dashboard -->
            <div class="inlinerow Mtop50">
                 <div class="col-md-12 col-sm-12 contentbg">
                <div class="col-md-12 col-sm-12 Mtopbot20">
                 <div class="Mtop1o">
                  <?= FlashResult::widget(); ?>
                  </div>
                    <fieldset>
                        <legend style="font-size: 20px;">Admin Profile</legend>
                        <!-- Section head -->
                        <div class="blockrow Mtop50">
                        <?php $form = ActiveForm::begin(['options' => ['id' => 'form']]); ?>
                            <div class="col-md-6 col-md-offset-2 col-sm-7">
                                <div class="inner-rows">
                                    <div class="col-md-5 col-sm-5">First Name <span style="color: red;">*</span></div>
                                    <div class="col-md-7 col-sm-7">
                                        <?= $form->field($userProfileModel, 'firstname')->textInput(['maxlength' => true])->label(false)?>
                                    </div>
                                </div>
                                <div class="inner-rows">
                                    <div class="col-md-5 col-sm-5">Middle Name </div>
                                    <div class="col-md-7 col-sm-7">
                                        <?= $form->field($userProfileModel, 'middlename')->textInput(['maxlength' => true])->label(false)?>
                                    </div>
                                </div>
                                <div class="inner-rows">
                                    <div class="col-md-5 col-sm-5">Last Name </div>
                                    <div class="col-md-7 col-sm-7">
                                        <?= $form->field($userProfileModel, 'lastname')->textInput(['maxlength' => true])->label(false)?>
                                    </div>
                                </div>
                                <div class="inner-rows">
                                    <div class="col-md-5 col-sm-5">Email Id <span style="color: red;">*</span></div>
                                    <div class="col-md-7 col-sm-7">
                                        <?= $form->field($userCredentialModel, 'emailid')->textInput(['maxlength' => true, 'readonly' =>true])->label(false)?>
                                    </div>
                                </div>
                                <div class="inner-rows">
                                    <div class="col-md-5 col-sm-5">Phone Number </div>
                                    <div class="col-md-7 col-sm-7">
                                        <?= $form->field($userCredentialModel, 'mobileno')->textInput(['maxlength' => true])->label(false)?>
                                    </div>
                                </div>
                                 <div class="inner-rows">
                                    <div class="col-md-5 col-sm-5">Institution Name <span style="color: red;">*</span></div>
                                    <div class="col-md-7 col-sm-7">
                                       <?= $form->field($userCredentialModel, 'institutionid') ->dropDownList(
                                            $institutions,
                                            [
                                            'id'=>'institution_name',
                                            'prompt'=>'Please Select',
                                            'class' => 'form-control',
                                            'disabled' => ($userCredentialModel->institutionid) ? true :false
                                            ]
                                            )
                                        ->label(false);
                                        ?>
                                    </div>
                                </div>
                        <div class="inner-rows">
                            <div class="col-md-5 col-sm-5"></div>
                                <div class="col-md-4 col-sm-4 change-password-checkbox">
                                     <?= $form->field($userCredentialModel, 'editpassword')->checkbox([],true)->label('Change password') ?>
                                </div>
                            <div class="col-md-1 col-sm-1">&nbsp;</div>
                        </div>
                        <div class="inner-rows form-control" style="display: none;" id="divChangepassword">
                               <div class="col-md-12 col-sm-12">
                                  <div>
                                     <div class="blockrow Mtop50">
                                        <div class="inner-rows">
                                           <div class="col-md-5 col-sm-5">New Password <span style="color: red;">*</span></div>
                                           <div class="col-md-7 col-sm-7">
                                              <?= $form->field($userCredentialModel, 'password')->passwordInput(['maxlength' => true, 'placeholder' => '**********'])->label(false)?>
                                           </div>
                                        </div>
                                        <div class="inner-rows">
                                           <div class="col-md-5 col-sm-5">Confirm Password <span style="color: red;">*</span></div>
                                           <div class="col-md-7 col-sm-7">
                                               <?= $form->field($userCredentialModel, 'confirm_password')->passwordInput(['maxlength' => true,'placeholder' => '**********'])->label(false)?>
                                           </div>
                                        </div>
                                        <div class="inner-rows">
                                           <div class="col-md-5 col-sm-5">Old Password <span style="color: red;">*</span></div>
                                           <div class="col-md-7 col-sm-7">
                                              <?= $form->field($userCredentialModel, 'old_password')->passwordInput(['maxlength' => true,'placeholder' => '**********'])->label(false)?>
                                           </div>
                                        </div>
                                  </div>
                                  </div>
                               </div>
                            </div>
                            <div class="inner-rows">
                                <div class="col-md-5 col-sm-5">&nbsp;</div>
                                <div class="col-md-7 col-sm-7">
                                         <?= Html::submitButton('Save', ['class' =>  'btn btn-success submit' , 'title' => 'Save']) ?>
                                </div>
                                </div>
                            </div>
                            <?php ActiveForm::end(); ?>
                        </div>
                        <!-- /. Blockrow closed -->
                    </fieldset>
                </div>
            </div>
            <!-- /. Blockrow closed -->  
     </div>             
</div>



