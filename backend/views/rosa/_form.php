<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\assets\AppAsset;
$assetName = AppAsset::register($this);

/* @var $this yii\web\View */
/* @var $model common\models\extendedmodels\ExtendedRosa */
/* @var $form yii\widgets\ActiveForm */
?>

  <div class="col-md-12 col-sm-12 Mtop15 text-center">
                <img src="https://admin.re-member.co.in/App_Themes/Default/images/Rosa-logo2.jpg">
            </div>

<div class="col-md-12 col-sm-12 col-xs-12 pageheader Mtop15">
    <?= 'Rajagiri Old Students Association' ?>
</div>
    
    


<div class="extended-rosa-form">
<div class="inlinerow Mtop50">
                 <div class="col-md-12 col-sm-12 contentbg">
                <div class="col-md-12 col-sm-12 Mtopbot20">
                    <fieldset>
                        <!-- Section head -->
                        <div class="blockrow Mtop50">
                        <?php $form = ActiveForm::begin([]); ?>
                            <div class="col-md-8 col-md-offset-2 col-sm-9">
                                <div class="inner-rows">
                                    <div class="col-md-5 col-sm-5">Year of Passing 10th Standard  </div>
                                    <div class="col-md-7 col-sm-7">
                                        <?= $form->field($model, 'year') ->dropDownList(
                        $years,
                        [
                        'id'=>'input-year',
                        'prompt'=>'Select year'
                        ]
                        )->label(false)?>
                                    </div>
                                </div>
                                <div class="inner-rows">
                                    <div class="col-md-5 col-sm-5">First Name <span style="color: red;">*</span></div>
                                    <div class="col-md-7 col-sm-7">
                                        <?= $form->field($model, 'name')->textInput(['maxlength' => true])->label(false)?>
                                    </div>
                                </div>
                                <div class="inner-rows">
                                    <div class="col-md-5 col-sm-5">Middle Name</div>
                                    <div class="col-md-7 col-sm-7">
                                       <?= $form->field($model, 'middlename')->textInput(['maxlength' => true])->label(false) ?>
                                    </div>
                                </div>
                                <div class="inner-rows">
                                    <div class="col-md-5 col-sm-5"> Last Name</div>
                                    <div class="col-md-7 col-sm-7">
                                  <?= $form->field($model, 'lastname')->textInput(['maxlength' => true])->label(false) ?>
                                    </div>
                                </div>
                                 <div class="inner-rows">
                                    <div class="col-md-5 col-sm-5">Mobile Number <span style="color: red;">*</span></div>
                                    <div class="col-md-7 col-sm-4">
                                    
                                    <?= $form->field($model, 'countrycode')->textInput(['maxlength' => '4' ,'class' => 'form-control citycodes' ])->label(false) ?>
                                    
                                    <?= $form->field($model, 'mobile')->textInput(['maxlength' => '100', 'class' =>'form-control ', "style" => "width: 293px;" ])->label(false) ?>
                                    </div>
                                </div>
                                 <div class="inner-rows">
                                    <div class="col-md-5 col-sm-5">Date of Birth <span style="color: red;">*</span></div>
                                    <div class="col-md-7 col-sm-7">
                                      <?= $form->field($model, 'dob')->textInput()->label(false) ?>
                                    </div>
                                </div>
                                <div class="inner-rows">
                                    <div class="col-md-5 col-sm-5">Email <span style="color: red;">*</span> </div>
                                    <div class="col-md-7 col-sm-7">
                                          <?= $form->field($model, 'email')->textInput(['maxlength' => true])->label(false) ?>	
                                    </div>
                                </div>
                                
                               
                
                                <div class="inner-rows">
                                    <div class="col-md-5 col-sm-5">&nbsp;</div>
                                    <div class="col-md-7 col-sm-7">
                                         <?= Html::submitButton('Save', ['class' =>  'btn btn-success' , 'title' => 'Save']) ?>
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
