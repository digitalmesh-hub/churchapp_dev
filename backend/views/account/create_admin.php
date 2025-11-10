<?php 

use yii\helpers\Html;
use backend\assets\AppAsset;
use yii\bootstrap\ActiveForm;
use backend\components\widgets\FlashResult;

$assetName = AppAsset::register($this);

$this->title = 'Manage Admin';

$this->registerJsFile(
    $assetName->baseUrl . '/theme/js/Remember.account.ui.js',
    [
        'depends' => [
                AppAsset::className()
        ]
    ]
);

echo Html::hiddenInput(
    'getAccountDepDropUrl',
    \Yii::$app->params['ajaxUrl']['getAccountDepDropUrl'],
    [
        'id'=>'account-dep-drop-Url'
    ]
);

?>
<!-- Header -->
<div class="col-md-12 col-sm-12 col-xs-12 pageheader Mtop15">
    <?= $this->title ?>
</div>
<div class="extended-userprofile-index">
            <!-- Dashboard -->
                <div class="col-md-12 col-sm-12 contentbg">
                <div class="col-md-12 col-sm-12 Mtopbot20">
                 <div class="Mtop10">
                  <?= FlashResult::widget(); ?>
                  </div>
                    <fieldset>
                        <legend style="font-size: 20px;">Define Admin</legend>
                        <!-- Section head -->
                        <div class="blockrow Mtop50">
                        <?php $form = ActiveForm::begin([ 'options' => ['id' => 'form']]); ?>
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
                                    <div class="col-md-5 col-sm-5">Last Name <span style="color: red;">*</span></div>
                                    <div class="col-md-7 col-sm-7">
                                        <?= $form->field($userProfileModel, 'lastname')->textInput(['maxlength' => true])->label(false)?>
                                    </div>
                                </div>
                                <div class="inner-rows">
                                    <div class="col-md-5 col-sm-5">Email Id <span style="color: red;">*</span></div>
                                    <div class="col-md-7 col-sm-7">
                                        <?= $form->field($userCredentialModel, 'emailid')->textInput(['maxlength' => true])->label(false)?>
                                    </div>
                                </div>
                                 <div class="inner-rows">
                                    <div class="col-md-5 col-sm-5">Password 
                                    <?php if ($userCredentialModel->isNewRecord): ?>
                                         <span style="color: red;">*</span>   
                                    <?php endif ?>
                                   
                                       </div>
                                    <div class="col-md-7 col-sm-7">
                                        <?= $form->field($userCredentialModel, 'password')->passwordInput(['maxlength' => true, 'placeholder' => '**********'])->label(false)?>
                                    </div>
                                </div>
                                 <div class="inner-rows">
                                    <div class="col-md-5 col-sm-5">Confirm Password 
                                    <?php if ($userCredentialModel->isNewRecord): ?>
                                         <span style="color: red;">*</span>   
                                    <?php endif ?>
                                      </div>
                                    <div class="col-md-7 col-sm-7">
                                       <?= $form->field($userCredentialModel, 'confirm_password')->passwordInput(['maxlength' => true,'placeholder' => '**********'])->label(false)?>
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
                                    <div class="col-md-5 col-sm-5">Role Category <span style="color: red;">*</span></div>
                                    <div class="col-md-7 col-sm-7">
                                      <?= $form->field($userCredentialModel, 'role_category') ->dropDownList(
                                            [],
                                            [
                                            'id'=>'role-category',
                                            'prompt'=>'Please Select',
                                            'class' => 'form-control'
                                            ]
                                            )
                                        ->label(false);
                                        ?>
                                    </div>
                                </div>
                                <div class="inner-rows">
                                    <div class="col-md-5 col-sm-5">Role <span style="color: red;">*</span></div>
                                    <div class="col-md-7 col-sm-7">
                                      <?= $form->field($userCredentialModel, 'role') ->dropDownList(
                                            [],
                                            [
                                            'id'=>'roles',
                                            'prompt'=>'Please Select',
                                            'class' => 'form-control'
                                            ]
                                            )
                                        ->label(false);
                                        ?>
                                    </div>
                                </div>
                                <div class="inner-rows">
                                    <div class="col-md-5 col-sm-5">&nbsp;</div>
                                    <div class="col-md-7 col-sm-7">
                                        <?= Html::activeHiddenInput($userCredentialModel, 'id', []) ?>
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


