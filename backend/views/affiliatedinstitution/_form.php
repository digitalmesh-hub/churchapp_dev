<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\assets\AppAsset;
use kartik\time\TimePicker;
use backend\components\widgets\FlashResult;

/* @var $this yii\web\View */
/* @var $model common\models\extendedmodels\ExtendedAffiliatedinstitution */
/* @var $form yii\widgets\ActiveForm */

$assetName = AppAsset::register ($this );
$this->registerJsFile ( $assetName->baseUrl . '/theme/js/Remember.affiliatedInstitution.ui.js',[
        'depends' => [
            AppAsset::className ()
    ]
] );
?>
<?= FlashResult::widget(); ?>
<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>
    <?php echo @Html::hiddenInput('homeUrl', Url::home(), array('id'=>'homeUrl'));?> 
    <?php echo Html::hiddenInput(
        'admin-delete-affiliated-institution-Url',
        \Yii::$app->params['ajaxUrl']['admin-delete-affiliated-institution-Url'],
        [
            'id'=>'admin-delete-affiliated-institution-Url'
        ]
        ); ?>
    <?php echo Html::hiddenInput(
        'admin-get-countryCode-Url',
        \Yii::$app->params['ajaxUrl']['admin-get-countryCode-Url'],
        [
            'id'=>'admin-get-countryCode-Url'
        ]
        ); ?>

    <div class="inlinerow">
        <div class="col-md-2 col-sm-2 col-md-offset-3 col-sm-offset-3">
            <?php if($model->institutionlogo){ ?>
                <img style="width: 160px; border-radius: 25px; height: 150px;" id="AffliatedInstitutionImage" class="form-control" 
                src="<?php echo Yii::$app->params['imagePath'].'/institutionlogo/'. $model->institutionid.'/' .$model->institutionlogo ?>">

            <?php } else { ?>
                <img style="width: 160px; border-radius: 25px; height: 150px;" id="AffliatedInstitutionImage" class="form-control" src="<?php echo $assetName->baseUrl .'/theme/images/institution-icon-grey.png' ?>">
            <?php } ?>
        </div>
        <div class="col-md-4 col-sm-4 Mtop50">
            <div class="inlinerow">Please upload an image
            </div>
            <div class="inlinerow Mtop15">
                <?= $form->field($model, 'institutionlogo')->fileInput(['class'=>'thumbnailimage form-control'])->label(false) ?>
            </div>
        </div>
    </div>
    <!-- Details -->
    <div class="inlinerow Mtop50">

        <!-- Section 1 -->
        <?php if($isRotary)  { ?>
            <div class="col-md-6 col-sm-6">
                <div class="inner-rows">
                    <div class="col-md-5 col-sm-5">Institution Name <span style="color: red;">*</span></div>
                    <div class="col-md-7 col-sm-7">
                        <?= $form->field($model, 'name')->textInput()->label(false) ?>
                    </div>
                </div>
                <div class="inner-rows">
                    <div class="col-md-5 col-sm-5">Address Line1 </div>
                    <div class="col-md-7 col-sm-7">
                        <?= $form->field($model, 'address1')->textarea(['rows' => '2', 'cols' => '20'])->label(false) ?>
                    </div>
                </div>
                <div class="inner-rows">
                    <div class="col-md-5 col-sm-5">Address Line2 </div>
                    <div class="col-md-7 col-sm-7">
                        <?= $form->field($model, 'address2')->textarea(['rows' => '2', 'cols' => '20'])->label(false) ?>
                    </div>
                </div>
                <div class="inner-rows">
                    <div class="col-md-5 col-sm-5">Email</div>
                    <div class="col-md-7 col-sm-7">
                        <?= $form->field($model, 'email')->textInput(['autocomplete' => 'off'])->label(false) ?>
                    </div>
                </div> 
                <div class="inner-rows">
                    <div class="col-md-5 col-sm-5">Website URL </div>
                    <div class="col-md-7 col-sm-7">
                        <?= $form->field($model, 'url')->textInput(['autocomplete' => 'off'])->label(false) ?>
                    </div>
                </div>
                <div class="inner-rows">
                    <div class="col-md-5 col-sm-5">Meeting Venue </div>
                    <div class="col-md-7 col-sm-7">
                        <?= $form->field($model, 'meetingvenue')->textInput(['autocomplete' => 'off'])->label(false) ?>
                    </div>
                </div> 
                                
            </div>
            <!-- /. Section 1 -->
            <!-- Section 2 -->
            <div class="col-md-6 col-sm-6">
                <div class="inner-rows">
                    <div class="col-md-5 col-sm-5">Meeting Day </div>
                    <div class="col-md-7 col-sm-7">
                       <?= $form->field($model, 'meetingday'
                        )
                        ->dropDownList(
                            $meetingDays,
                            [
                             'id' => 'meetingDayId',
                             'prompt' => 'Please Select'
                           ]
                      )->label(false) ?>
                    </div>
                </div>
                <div class="inner-rows">
                    <div class="col-md-5 col-sm-5">Meeting Time </div>
                    <div class="col-md-7 col-sm-7">
                        <?= $form->field($model, 'meetingtime')->widget(TimePicker::classname(), [])->label(false); ?>
                    </div>
                </div>
                <div class="inner-rows">
                    <div class="col-md-5 col-sm-5">President Name</div>
                    <div class="col-md-7 col-sm-7">
                        <?= $form->field($model, 'presidentname')->textInput(['autocomplete' => 'off'])->label(false) ?>
                    </div>
                </div> 
                <div class="inner-rows">
                    <div class="col-md-5 col-sm-5">President Mobile</div>
                    <div class="col-md-7 col-sm-7 phone-div">
                        <?= $form->field($model, 'presidentmobile_countrycode')->textInput(
                            ['maxlength' => '4','autocomplete' => 'off', 'class' => 'form-control citycodes country-code'])->label(false) ?>
                        <?= $form->field($model, 'presidentmobile')->textInput(
                                ['maxlength' => '10','class' => 'form-control fullnumber-affiliated','autocomplete' => 'off'])->label(false) ?>
                         <div class="error-president-mobile msg-div error-div" style="display:none">Invalid Mobile Number</div>
                        
                    </div>
                </div>
                <div class="inner-rows">
                    <div class="col-md-5 col-sm-5">Secretary Name</div>
                    <div class="col-md-7 col-sm-7">
                        <?= $form->field($model, 'secretaryname')->textInput(['autocomplete' => 'off'])->label(false) ?>
                    </div>
                </div> 
                <div class="inner-rows">
                    <div class="col-md-5 col-sm-5">Secretary Mobile </div>
                    <div class="col-md-7 col-sm-7 phone-div">
                        <?= $form->field($model, 'secretarymobile_countrycode')->textInput(
                            ['maxlength' => '4', 'autocomplete' => 'off', 'class' => 'form-control citycodes country-code'])->label(false) ?>
                       <?= $form->field($model, 'secretarymobile')->textInput(
                                ['maxlength' => '10','class' => 'form-control fullnumber-affiliated','autocomplete' => 'off'])->label(false) ?>
                         <div class="error-secretary-mobile msg-div error-div" style="display:none">Invalid Mobile Number</div>
                        
                    </div>
                </div>
                <div class="inner-rows">
                    <div class="col-md-5 col-sm-5">Remarks </div>
                    <div class="col-md-7 col-sm-7">
                        <?= $form->field($model, 'remarks')->textInput(['autocomplete' => 'off'])->label(false) ?>
                    </div>
                </div>                                
            </div>
            <!-- /. Section 2 -->
        <?php } else { ?>
           <div class="col-md-6 col-sm-6">
                <div class="inner-rows">
                    <div class="col-md-5 col-sm-5">Institution Name <span style="color: red;">*</span></div>
                    <div class="col-md-7 col-sm-7">
                        <?= $form->field($model, 'name')->textInput()->label(false) ?>
                    </div>
                </div>
                <div class="inner-rows">
                    <div class="col-md-5 col-sm-5">Address Line1 </div>
                    <div class="col-md-7 col-sm-7">
                        <?= $form->field($model, 'address1')->textarea(['rows' => '2', 'cols' => '20'])->label(false) ?>
                    </div>
                </div>
                <div class="inner-rows">
                    <div class="col-md-5 col-sm-5">Address Line2 </div>
                    <div class="col-md-7 col-sm-7">
                        <?= $form->field($model, 'address2')->textarea(['rows' => '2', 'cols' => '20'])->label(false) ?>
                    </div>
                </div>
                <div class="inner-rows">
                    <div class="col-md-5 col-sm-5">Country<span style="color: red;"> *</span> </div>
                    <div class="col-md-7 col-sm-7">
                        <?= $form->field($model, 'CountryID')
                            ->dropDownList(
                            $countryList,
                            [
                                'id' => 'country_id',
                                'prompt' => 'Please Select',
                                'options' => [
                                    94 => array('selected' =>true) //set defualt country to india.
                                ]
                           ]
                        )->label(false) ?>
                    </div>
                </div>
                <div class="inner-rows">
                    <div class="col-md-5 col-sm-5">State </div>
                    <div class="col-md-7 col-sm-7">
                        <?= $form->field($model, 'state')->textInput()->label(false) ?>
                    </div>
                </div>
                                
            </div>
            <!-- /. Section 1 -->
            <!-- Section 2 -->
            <div class="col-md-6 col-sm-6">
                <div class="inner-rows">
                    <div class="col-md-5 col-sm-5">District </div>
                    <div class="col-md-7 col-sm-7">
                        <?= $form->field($model, 'district')->textInput()->label(false) ?>
                    </div>
                </div>
                <div class="inner-rows">
                    <div class="col-md-5 col-sm-5">Pin </div>
                    <div class="col-md-7 col-sm-7">
                        <?= $form->field($model, 'pin')->textInput(['autocomplete' => 'off'])->label(false) ?>
                    </div>
                </div>
                <div class="inner-rows">
                    <div class="col-md-5 col-sm-5">Email</div>
                    <div class="col-md-7 col-sm-7">
                        <?= $form->field($model, 'email')->textInput(['autocomplete' => 'off'])->label(false) ?>
                    </div>
                </div> 
                <div class="inner-rows">
                    <div class="col-md-5 col-sm-5">Mobile Number</div>
                    <div class="col-md-7 col-sm-7 phone-div">
                        <?= $form->field($model, 'mobilenocountrycode')->textInput(
                            ['maxlength' => '4','autocomplete' => 'off', 'class' => 'form-control citycodes country-code number-check'])->label(false) ?>
                        <?= $form->field($model, 'phone2')->textInput(
                                ['maxlength' => '10','class' => 'form-control fullnumber-affiliated','autocomplete' => 'off'])->label(false) ?>
                         <div class="error-phone1 msg-div error-div" style="display:none">Invalid Mobile Number</div>
                        
                    </div>
                </div>
                <div class="inner-rows">
                    <div class="col-md-5 col-sm-5">Telephone Number 1 </div>
                    <div class="col-md-7 col-sm-7 phone-div">
                        <?= $form->field($model, 'phone1_countrycode')->textInput(
                            ['maxlength' => '4', 'autocomplete' => 'off', 'class' => 'form-control citycodes country-code number-check'])->label(false) ?>
                        <?= $form->field($model, 'phone1_areacode')->textInput(
                            ['maxlength' => '4','autocomplete' => 'off', 'class' => 'form-control citycodes number-check'])->label(false) ?>   
                        <?= $form->field($model, 'phone1')->textInput(
                                ['maxlength' => '7','class' => 'form-control fullnumber number-check',
                                'autocomplete' => 'off'])->label(false) ?>
                         <div class="error-phone2 msg-div error-div" style="display:none">Invalid Telephone Number 1</div>
                        
                    </div>
                </div>

                <div class="inner-rows">
                    <div class="col-md-5 col-sm-5">Telephone Number 2</div>
                    <div class="col-md-7 col-sm-7 phone-div">
                        <?= $form->field($model, 'phone3_countrycode')->textInput(
                            ['maxlength' => '4', 'autocomplete' => 'off', 'class' => 'form-control citycodes country-code number-check'])->label(false) ?>
                        <?= $form->field($model, 'phone3_areacode')->textInput(
                            ['maxlength' => '4', 'autocomplete' => 'off', 'class' => 'form-control citycodes number-check'])->label(false) ?>   
                        <?= $form->field($model, 'phone3')->textInput(
                                ['maxlength' => '7','class' => 'form-control fullnumber number-check','autocomplete' => 'off'])->label(false) ?>
                        <div class="error-phone3 msg-div" style="display:none">Invalid Telephone Number 2</div>
                        
                    </div>
                </div>
                <div class="inner-rows">
                    <div class="col-md-5 col-sm-5">Website URL </div>
                    <div class="col-md-7 col-sm-7">
                        <?= $form->field($model, 'url')->textInput(['autocomplete' => 'off'])->label(false) ?>
                    </div>
                </div>                                
            </div>
            <!-- /. Section 2 -->
        <?php } ?> 
    </div>
    <div class="inlinerow Mtop30 text-center">
        <?= Html::submitButton('Save',['class' => 'btn btn btn-success btn-extra save-institution','title' => Yii::t('yii', 'Save')]) ?>

        <?= Html::a('Cancel', ['index'] ,['class' => 'btn btn-danger btn-extra', 'title' => Yii::t('yii', 'Cancel')]) ?>

        <input type="hidden" id="hdnInstitutionId" value="0">
        <input type="hidden" id="hdnaffliatedInstitutionId" value="0">

    </div>
<?php ActiveForm::end(); ?>