<?php 
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\assets\AppAsset;
use kartik\date\DatePicker;
use common\models\extendedmodels\ExtendedInstitution;
use backend\components\widgets\FlashResult;
$assetName = AppAsset::register($this);

$this->title = 'Member-Details';

$this->registerJsFile(
    $assetName->baseUrl . '/theme/js/Remember.memberEdit.ui.js',
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
        'id'=>'base-url'
    ]
);

?>
    <div class="col-md-12 col-sm-12 col-xs-12 contentbg Mtop15">
        <div class="row Mtopbot20">

            <div class="Mtop10">
                <?= FlashResult::widget(); ?>
            </div>
            <!-- Member Tab -->

            <!-- Section 1 -->
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

                <?= $form->field($model, 'memberid')->hiddenInput()->label(false) ?>
                    <?= $form->field($model, 'institutionid')->hiddenInput()->label(false) ?>

                        <div class="col-md-6 col-sm-6 col-xs-12 Mtop20">
                            <fieldset>
                                <legend>Member Details</legend>
                                <div class="blockrow">
                                    <div class="col-md-5 col-sm-5 col-xs-12 imagesize"> <img class="fix-size" width="184" height="184" id='memberimage' src="<?php 
              $image  = $model->member_pic?$model->member_pic: "/Member/default-user.png ";
                  echo Yii::$app->params['imagePath'].$image; 
              ?>">
                                    </div>
                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                        <div class="inlinerow">Upload Member Photo</div>
                                        <div class="inlinerow Mtop10">
                                            <?= $form->field($model, 'memberImageThumbnail')->fileInput(['class' => 'form-control','id' => 'memberfile'])->label(false); ?>
                                                <?= $form->field($dynamicImageManageModel, 'memberpic')->hiddenInput(['id' =>"memberImageChecker", "value"=>"notremoved"])->label(false) ?>
                                                    <input type="button" id="btnremovemember" value="Remove">
                                        </div>
                                        <div class="inlinerow Mtop10">
                                            <br>
                                        </div>
                                    </div>
                                </div>
                                <!-- rows -->
                                <div class="inlinerow Mtop10">
                                    <div class="col-md-4 col-sm-5 col-xs-12 L32">Title<span style="color: red;">*</span></div>
                                    <div class="col-md-8 col-sm-7 col-xs-12">
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
                                    <div class="col-md-4 col-sm-5 col-xs-12 L32">First Name <span style="color: red;">*</span></div>
                                    <div class="col-md-8 col-sm-7 col-xs-12">
                                        <?= $form->field($model, 'firstName')->textInput(['maxlength' => true , 'id' => 'txtFirstName', ])->label(false) ?>
                                    </div>
                                </div>
                                <!--/.rows -->
                                <!-- rows -->
                                <div class="inlinerow Mtop10">
                                    <div class="col-md-4 col-sm-5 col-xs-12 L32">Middle Name</div>
                                    <div class="col-md-8 col-sm-7 col-xs-12 col-xs-12">
                                        <?= $form->field($model, 'middleName')->textInput(['maxlength' => true])->label(false) ?>
                                    </div>
                                </div>
                                <!--/.rows -->
                                <!-- rows -->
                                <div class="inlinerow Mtop10">
                                    <div class="col-md-4 col-sm-5 col-xs-12 L32">Last Name <span style="color: red;">*</span></div>
                                    <div class="col-md-8 col-sm-7 col-xs-12">
                                        <?= $form->field($model, 'lastName')->textInput(['maxlength' => true , 'id' => 'txtLastName' ])->label(false) ?>
                                    </div>
                                </div>
                                <!--/.rows -->
                                <!-- rows -->
                                <div class="inlinerow Mtop10">
                                    <div class="col-md-4 col-sm-5 col-xs-12 L32">Nick/AKA</div>
                                    <div class="col-md-8 col-sm-7 col-xs-12">
                                        <?= $form->field($model, 'membernickname')->textInput(['maxlength' => true])->label(false) ?>
                                    </div>
                                </div>
                                <!--/.rows -->
                                <!-- rows -->
                                <div class="inlinerow Mtop10">
                                    <div class="col-md-4 col-sm-5 col-xs-12 L32">Email </div>
                                    <div class="col-md-8 col-sm-7 col-xs-12">
                                        <?= $form->field($model, 'member_email')->textInput(['maxlength' => true ,'id' => 'txtMemberEmail'])->label(false) ?>
                                    </div>
                                </div>

                                <div class="inlinerow Mtop10">
                                    <div class="col-md-4 col-sm-5 col-xs-12 L32">DOB</div>
                                    <div class="col-md-8 col-sm-7 col-xs-12">
                                        <?= DatePicker::widget([
                    'name' => 'member_dob',
                    'id' => 'member_dob',
                    'value' => $model->member_dob ? date(yii::$app->params['dateFormat']['viewDateFormat'], strtotimeNew($model->member_dob)):'',
                    'options' => [
                      // 'placeholder' => 'Select End Date',
                    ],
                    'pluginOptions' => [
                        'autoclose'=>true,
                        'format' => 'dd MM yyyy',
                        'endDate' =>'0d'
                    ]]); ?>
                                    </div>
                                </div>

                                <!--/.rows -->
                                <?php if (strtolower(ExtendedInstitution::getInstitutionTypeNameFromCode($model->institution->institutiontype)) == "church" ) {?>
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
                                                <?= $form->field($model, 'occupation')->textInput(['maxlength' => true])->label(false) ?>
                                            </div>
                                        </div>

                                        <div class="inlinerow Mtop10">
                                            <div class="col-md-4 col-sm-5 L32">Blood Group </div>

                                            <div class="col-md-8 col-sm-7">

                                                <?= $form->field($model, 'memberbloodgroup')->dropDownList(
                                            $bloodGroup,
                                            [
                                            'prompt'=>'Please Select'
                                            ]
                                            )
                                        ->label(false);?>

                                            </div>
                                        </div>

                                        <?php if ($model->institution->tagcloud){ ?>
                                            <div class="inlinerow Mtop10">
                                                <div class="col-md-4 col-sm-5 L32">TagCloud</div>
                                                <div class="col-md-8 col-sm-7">
                                                    <?= $form->field($memberadditionalModal, 'tagcloud')->textInput(['maxlength' => true,'data-role' => 'tagsinput','id' => 'test'])->label(false);?>
                                                </div>
                                            </div>
                                            <?php }?>

                            </fieldset>
                        </div>
                        <!-- /.Section 1 -->

                        <!-- Section 2 -->
                        <div class="col-md-6 col-sm-6 col-xs-12 Mtop20">
                            <fieldset>
                                <legend>Spouse Details</legend>
                                <div class="blockrow">
                                    <div class="col-md-5 col-sm-5 col-xs-12 imagesize"> <img class="fix-size" width="184" height="184" id='spouseimage' src="<?php $image  = $model->spouse_pic?$model->spouse_pic: "/Member/default-user.png ";

                                                    echo Yii::$app->params['imagePath'].$image; ?>"> </div>
                                    <div class="col-md-7 col-sm-7 col-xs-12">
                                        <div class="inlinerow ">Upload Spouse Photo</div>
                                        <div class="inlinerow Mtop10">
                                            <?= $form->field($model, 'spouseImageThumbnail')->fileInput(['maxlength' => true , 'id' => 'spousefile' ,'class' => 'form-control'])->label(false) ?>
                                                <br>
                                                <?= $form->field($dynamicImageManageModel, 'spousepic')->hiddenInput(['id' =>"spouseImageChecker","value"=>"notremoved"])->label(false) ?>
                                                    <input type="button" id="btnspouseremove" value="Remove">
                                        </div>
                                        <div class="inlinerow Mtop10">
                                            <br>
                                        </div>
                                    </div>
                                </div>

                                <!-- rows -->
                                <div class="inlinerow Mtop10">
                                    <div class="col-md-4 col-sm-5 col-xs-12 L32">Title</div>
                                    <div class="col-md-8 col-sm-7 col-xs-12">
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
                                    <div class="col-md-4 col-sm-5 col-xs-12 L32">First Name</div>
                                    <div class="col-md-8 col-sm-7 col-xs-12">
                                        <?= $form->field($model, 'spouse_firstName')->textInput(['maxlength' => true, 'id' => 'txtMemberSpouseFirstName'])->label(false) ?>

                                    </div>
                                </div>
                                <!--/.rows -->
                                <!-- rows -->
                                <div class="inlinerow Mtop10">
                                    <div class="col-md-4 col-sm-5 col-xs-12 L32">Middle Name</div>
                                    <div class="col-md-8 col-sm-7 col-xs-12">
                                        <?= $form->field($model, 'spouse_middleName')->textInput(['maxlength' => true])->label(false) ?>
                                    </div>
                                </div>
                                <!--/.rows -->
                                <!-- rows -->
                                <div class="inlinerow Mtop10">
                                    <div class="col-md-4 col-sm-5 col-xs-12 L32">Last Name</div>
                                    <div class="col-md-8 col-sm-7 col-xs-12">
                                        <?= $form->field($model, 'spouse_lastName')->textInput(['maxlength' => true,'id'=>'txtMemberSpouseLastName'])->label(false) ?>
                                    </div>
                                </div>
                                <!--/.rows -->
                                <!-- rows -->
                                <div class="inlinerow Mtop10">
                                    <div class="col-md-4 col-sm-5 col-xs-12 L32">Nick/AKA</div>
                                    <div class="col-md-8 col-sm-7 col-xs-12">
                                        <?= $form->field($model, 'spousenickname')->textInput(['maxlength' => true])->label(false) ?>
                                    </div>
                                </div>
                                <!--/.rows -->
                                <!-- rows -->
                                <div class="inlinerow Mtop10">
                                    <div class="col-md-4 col-sm-5 col-xs-12 L32">Email</div>
                                    <div class="col-md-8 col-sm-7 col-xs-12">
                                        <?= $form->field($model, 'spouse_email')->textInput(['maxlength' => true ,'id' =>'txtMemberspouseemail'])->label(false) ?>
                                    </div>
                                </div>
                                <!--/.rows -->
                                <!-- rows -->
                                <div class="inlinerow Mtop10">
                                    <div class="col-md-4 col-sm-5 col-xs-12 L32">Mobile </div>
                                    <div class="col-md-8 col-sm-7 col-xs-12">
                                        <?= $form->field($model, 'spouse_mobile1_countrycode')->textInput(['maxlength' => '4' ,'class' => 'form-control citycodes' ,'id'=>'txtspousemobile1_countrycode' ])->label(false) ?>
                                            <?= $form->field($model, 'spouse_mobile1')->textInput(['maxlength' => '10', 'class' =>'form-control fullnumber', 'id'=> 'txtspousemobile1' ])->label(false) ?>

                                    </div>
                                </div>
                                <!--/.rows -->
                                <!-- rows -->

                                <!--/.rows -->
                                <!-- rows -->
                                <div class="inlinerow Mtop10">
                                    <div class="col-md-4 col-sm-5 col-xs-12 L32">DOB</div>
                                    <div class="col-md-8 col-sm-7 col-xs-12">

                                        <?= DatePicker::widget([
                                'name' => 'spouse_dob',
                                'id' => 'spouse_dob',
                                'value' => $model->spouse_dob?date(yii::$app->params['dateFormat']['viewDateFormat'], strtotimeNew($model->spouse_dob)):'',
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
                                <div class="inlinerow Mtop10">
                                    <div class="col-md-4 col-sm-5 L32">Occupation</div>
                                    <div class="col-md-8 col-sm-7">
                                        <?= $form->field($model, 'spouseoccupation')->textInput(['maxlength' => true])->label(false) ?>
                                    </div>
                                </div>

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
                                <!--/.rows -->
                            </fieldset>
                        </div>
                        <!-- /.Section 2 -->

                        <div id="date-of-wedding" style="display: none;">
                            <!-- Separator -->
                            <div class="segment">&nbsp;</div>
                            <!-- Section 1 -->
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <div class="inlinerow Mtop10">
                                    <div class="col-md-4 col-sm-5 col-xs-12 L32">Wedding Anniversary</div>
                                    <div class="col-md-8 col-sm-7 col-xs-12">
                                        <?= DatePicker::widget([
                      'name' => 'dom',
                      'id' => 'dom',
                      'value' => $model->dom?date(yii::$app->params['dateFormat']['viewDateFormat'], strtotimeNew($model->dom)):'',
                      'options' => [
                          // 'placeholder' => 'Select End Date',
                      ],
                      'pluginOptions' => [
                            'autoclose'=>true,
                            'format' => 'dd MM yyyy',
                            'endDate' =>'0d'
                      ]]);?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /. separate section -->
                        <!-- Separator -->
                        <div class="segment">&nbsp;</div>
                        <div class="segment">&nbsp;</div>
        <!-- /. location section -->
        <div class="col-md-6 col-sm-6">

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
                                'placeholder' => 'Enter Latitude',
                                'value' => $model->latitude
                            ]
                        )->label(false); ?>
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
                                'placeholder' => 'Enter Longitude',
                                'value' => $model->longitude
                            ]
                        )->label(false); ?>
                    </div>
                </div>

            </fieldset>
        </div>
        <div class="segment">&nbsp;</div>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <fieldset>
                                <legend>Business Details</legend>
                                <!--/.rows -->
                                <!-- rows -->
                                <div class="inlinerow Mtop10">
                                    <div class="col-md-4 col-sm-5 col-xs-12 L32">Address Line 1</div>
                                    <div class="col-md-8 col-sm-7 col-xs-12">
                                        <?= $form->field($model, 'business_address1')->textarea(['maxlength' => true])->label(false) ?>
                                    </div>
                                </div>
                                <!--/.rows -->
                                <!-- rows -->
                                <div class="inlinerow Mtop10">
                                    <div class="col-md-4 col-sm-5 col-xs-12 L32">Address Line 2</div>
                                    <div class="col-md-8 col-sm-7 col-xs-12">
                                        <?= $form->field($model, 'business_address2')->textarea(['maxlength' => true])->label(false) ?>
                                    </div>
                                </div>

                                <!--/.rows -->
                                <!-- rows -->
                                <div class="inlinerow Mtop10">
                                    <div class="col-md-4 col-sm-5 col-xs-12 L32">Postal Code</div>
                                    <div class="col-md-8 col-sm-7 col-xs-12">
                                        <?= $form->field($model, 'business_pincode')->textInput(['maxlength' => true])->label(false) ?>
                                    </div>
                                </div>
                                <!--/.rows -->
                                <!-- rows -->
                                <div class="inlinerow Mtop10">
                                    <div class="col-md-4 col-sm-5 col-xs-12 L32">District</div>
                                    <div class="col-md-8 col-sm-7 col-xs-12">
                                        <?= $form->field($model, 'business_district')->textInput(['maxlength' => true])->label(false) ?>
                                    </div>
                                </div>
                                <!--/.rows -->
                                <!-- rows -->
                                <div class="inlinerow Mtop10">
                                    <div class="col-md-4 col-sm-5 col-xs-12 L32">State</div>
                                    <div class="col-md-8 col-sm-7 col-xs-12">
                                        <?= $form->field($model, 'business_state')->textInput(['maxlength' => true])->label(false) ?>
                                    </div>
                                </div>

                                <!--/.rows -->
                                <!-- rows -->
                                <div class="inlinerow Mtop10">
                                    <div class="col-md-4 col-sm-5 col-xs-12 L32">Office Phone</div>
                                    <div class="col-md-8 col-sm-12 phone-div">
                                        <?= $form->field($model, 'member_business_phone1_countrycode')->textInput(['maxlength' => '4' ,'class' => 'form-control citycodes' ,'id'=>'txtMemberBusinessPhone1_countrycode' ])->label(false) ?>
                                            <?= $form->field($model, 'member_business_phone1_areacode')->textInput(['maxlength' => '5' ,'class' => 'form-control citycodes' ,'id' => 'txtMemberBusinessPhone1_areacode'])->label(false) ?>
                                                <?= $form->field($model, 'member_musiness_Phone1')->textInput(['maxlength' => '10' ,'class' => 'form-control fullnumber' ,'id' => 'txtMemberBusinessPhone1'])->label(false) ?>

                                    </div>
                                </div>
                                <div class="inlinerow Mtop10">
                                    <div class="col-md-4 col-sm-5 L32">Office Phone 2</div>
                                    <div class="col-md-8 col-sm-12 phone-div">
                                        <?= $form->field($model, 'member_business_phone3_countrycode')->textInput(['maxlength' => '4' ,'class' => 'form-control citycodes' ,'id'=>'txtMemberBusinessPhone3_countrycode' ])->label(false) ?>
                                            <?= $form->field($model, 'member_business_phone3_areacode')->textInput(['maxlength' => '5' ,'class' => 'form-control citycodes' ,'id'=>'txtMemberBusinessPhone3_areacode' ])->label(false) ?>
                                                <?= $form->field($model, 'member_business_Phone3')->textInput(['maxlength' => '10' ,'class' => 'form-control fullnumber','id'=>'txtMemberBusinessPhone3' ])->label(false) ?>

                                    </div>
                                </div>
                                <!--/.rows -->
                                <!-- rows -->

                                <div class="inlinerow Mtop10">
                                    <div class="col-md-4 col-sm-5 col-xs-12 L32">Email</div>
                                    <div class="col-md-8 col-sm-7 col-xs-12">
                                        <?= $form->field($model, 'businessemail')->textInput(['maxlength' => true,'id'=>'txtbusinessEmail'])->label(false) ?>
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
                                        <?= $form->field($model, 'residence_address1')->textarea(['maxlength' => true])->label(false) ?>
                                    </div>
                                </div>
                                <!--/.rows -->
                                <!-- rows -->
                                <div class="inlinerow Mtop10">
                                    <div class="col-md-4 col-sm-5 col-xs-12 L32">Address Line 2</div>
                                    <div class="col-md-8 col-sm-7 col-xs-12">
                                        <?= $form->field($model, 'residence_address2')->textarea(['maxlength' => true])->label(false) ?>
                                    </div>
                                </div>

                                <!--/.rows -->
                                <!-- rows -->
                                <div class="inlinerow Mtop10">
                                    <div class="col-md-4 col-sm-5 col-xs-12 L32">Postal Code</div>
                                    <div class="col-md-8 col-sm-7 col-xs-12">
                                        <?= $form->field($model, 'residence_pincode')->textInput(['maxlength' => true])->label(false) ?>
                                    </div>
                                </div>

                                <!--/.rows -->
                                <!-- rows -->
                                <div class="inlinerow Mtop10">
                                    <div class="col-md-4 col-sm-5 col-xs-12 L32">District</div>
                                    <div class="col-md-8 col-sm-7 col-xs-12">
                                        <?= $form->field($model, 'residence_district')->textInput(['maxlength' => true])->label(false) ?>
                                    </div>
                                </div>
                                <!--/.rows -->
                                <!-- rows -->
                                <div class="inlinerow Mtop10">
                                    <div class="col-md-4 col-sm-5 col-xs-12 L32">State</div>
                                    <div class="col-md-8 col-sm-7 col-xs-12">
                                        <?= $form->field($model, 'residence_state')->textInput(['maxlength' => true])->label(false) ?>
                                    </div>
                                </div>
                                <!--/.rows -->
                                <!-- rows -->
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
        if ($count>=1) {
        ?>
        
        <!-- Dependants -->
        <div class="col-md-12 col-sm-12 col-xs-12 Mtop30">
            <fieldset>
                <legend>Dependents</legend>
                <input type='hidden' value="<?=$dependantIds ?>" name='depentantIdslist'>
                <?php  
                  $i = 0;
                  foreach ($dependantDetails as $data) { 
                    $i++;
                    ?>
                                        <div class="<?= $i%2 ==1 ?" row ":'' ?>">

                                            <div class="col-md-6 col-sm-6 col-xs-12 <?= $i%2 ==1 ?" splitup ":'' ?>">

                                                <div class="inlinerow Mtop10">
                                                    <div class="col-md-4 col-sm-5 col-xs-12 L32">Dependent Photo</div>
                                                    <div class="col-md-8 col-sm-7 col-xs-12">
                                                        <div class="row">
                                                            <div class="col-md-5 col-sm-5 col-xs-12">
                                                                <img class="w100p " src="<?php
                                                       $image  = $data['dependantimage'] ? $data['dependantimage']: "/Member/default-user.png ";
                                                    echo Yii::$app->params['imagePath'].$image; ?>" id="dependantimage_<?= $data['id']?>">
                                                            </div>

                                                        </div>
                                                        <div class="inlinerow Mtop10">
                                                            <input type="file" id="dependantfile_<?= $data['id']?>" dependantImageid="<?= $data['id']?>" name="dependantfile_<?= $data['id']?>" class="form-control dependantfile depentendImage ">
                                                        </div>
                                                        <div class="inlinerow Mtop5">
                                                            <input type='hidden' name="dependantpic_<?= $data['id']?>" id="dependantpic_<?= $data['id']?>" value="notremoved">
                                                            <input type="button" value="Remove" class="btn btn-primary removedependantimage" id="removedependantimage" dependantid="<?= $data['id']?>">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="inlinerow Mtop10">
                                                    <div class="col-md-4 col-sm-5 col-xs-12 L32">Dependent Title</div>
                                                    <div class="col-md-8 col-sm-7 col-xs-12">
                                                        <select name="dependanttitle_<?= $data['id']?>" class="form-control">
                                                            <?php foreach ($titlesArray as $id => $name) {?>
                                                                <option value="<?= $id ?>" <?=$id==$data[ 'dependanttitleid']? "selected='selected'": ''?> >
                                                                    <?= $name ?>
                                                                </option>
                                                                <?php }?>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="inlinerow Mtop10">
                                                    <div class="col-md-4 col-sm-5 col-xs-12 L32">Dependent Name</div>
                                                    <div class="col-md-8 col-sm-7 col-xs-12">
                                                        <input autocomplete="off" class="form-control" type="text" value="<?= $data['dependantname']?>" name="dependantname_<?= $data['id']?>">
                                                    </div>
                                                </div>

                                                <div class="inlinerow Mtop10">
                                                    <div class="col-md-4 col-sm-5 col-xs-12 L32">Dependent Mobile</div>
                                                    <div class="col-md-8 col-sm-7 col-xs-12 phone-div">
                                                    <input autocomplete="off" class="form-control citycodes" type="text" maxlength="4" id="dependantmobilecountrycode"  value="<?= $data['dependantmobilecountrycode']?>" name="dependantmobilecountrycode_<?= $data['id']?>">
                                                        <input autocomplete="off" class="form-control fullnumber-affiliated" type="text" minlength="6" maxlength="13" id="dependantmobile"  value="<?= $data['dependantmobile']?>" name="dependantmobile_<?= $data['id']?>">
                                                    </div>
                                                </div>

                                                <div class="inlinerow Mtop10">
                                                    <div class="col-md-4 col-sm-5 col-xs-12 L32">DOB</div>
                                                    <div class="col-md-8 col-sm-7 col-xs-12">
                                                        <?= DatePicker::widget([
                                'name' => 'dependantdob_'.$data['id'],
                                      'id' => 'dependantdob_'.$data['id'],
                                        'value' => $data['dob']?date(yii::$app->params['dateFormat']['viewDateFormat'], strtotimeNew($data['dob'])):'',
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

                                                <div class="inlinerow Mtop10">
                                                    <div class="col-md-4 col-sm-5 col-xs-12 L32">Relation</div>
                                                    <div class="col-md-8 col-sm-7 col-xs-12">
                                                        <select name="dependantrelation_<?= $data['id']?>" class="form-control">
                                                            <?php foreach ($relations as $id => $name) {?>
                                                                <option value="<?= $id ?>" <?=$id==$data[ 'relation']? "selected='selected'": ''?>>
                                                                    <?= $name ?>
                                                                </option>
                                                                <?php }?>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="inlinerow Mtop10">
                                                    <div class="col-md-4 col-sm-5 col-xs-12 L32">Marital Status</div>
                                                    <div class="col-md-8 col-sm-7 col-xs-12">
                                                        <select name="dependantmartialstatus_<?= $data['id']?>" dependantid="<?= $data['id'] ?>" class="form-control tempdpmartialstatus ">
                                                            <?php foreach ($isMarried as $id => $name) {?>
                                                                <option value="<?= $id ?>" <?=$id==$data[ 'ismarried']? "selected='selected'": ''?> >
                                                                    <?= $name ?>
                                                                </option>
                                                                <?php }?>
                                                        </select>
                                                    </div>
                                                </div>

                                                <!-- Hidden spouse info -->
                                                <div id="dependentspousediv_<?=$data['id'] ?>" class="inlinerow maritalspouse Mtop20  <?= $data['ismarried']==2 ?'':'nodisplay' ?>  ">

                                                    <div class="inlinerow Mtop10">
                                                        <div class="col-md-4 col-sm-5 col-xs-12 L32">Spouse Photo</div>
                                                        <div class="col-md-8 col-sm-7 col-xs-12">
                                                            <div class="row">
                                                                <div class="col-md-5 col-sm-5 col-xs-12">
                                                                    <img class="w100p" src="<?php

                                                       $image  = $data['dependantspouseimage']?$data['dependantspouseimage']: "/Member/default-user.png ";

                                                    echo Yii::$app->params['imagePath'].$image; ?>" id="dependantspouseimage_<?= $data['id']?>">
                                                                </div>

                                                            </div>
                                                            <div class="inlinerow Mtop10">
                                                                <input type="file" id="dependantspousefile_<?= $data['id']?>" name="dependantspousefile_<?= $data['id']?>" dependantImageid="<?= $data['id']?>" class="form-control dependantfile dependantspouseimage">
                                                            </div>
                                                            <div class="inlinerow Mtop5">
                                                                <input type='hidden' name="dependantspousepic_<?= $data['id']?>" id="dependantspousepic_<?= $data['id']?>" value="notremoved">
                                                                <input type="button" value="Remove" class="btn btn-primary removedependantspouseimage" id="removedependantspouseimage" dependantid="<?= $data['id']?>">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <input type='hidden' name='spousedependantid_<?= $data['id']?>' value="<?= $data['dependantspouseid']?>">

                                                    <div class="inlinerow Mtop10">
                                                        <div class="col-md-4 col-sm-5 col-xs-12 L32">Spouse Title</div>
                                                        <div class="col-md-8 col-sm-7 col-xs-12">
                                                            <select name="tempdependantspousetitleid_<?= $data['id']?>" class="form-control">
                                                                <?php foreach ($titlesArray as $id => $name) {?>
                                                                    <option value="<?= $id ?>" <?=$id==$data[ 'spousetitleid']? "selected='selected'": ''?> >
                                                                        <?= $name ?>
                                                                    </option>
                                                                    <?php }?>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="inlinerow Mtop10">
                                                        <div class="col-md-4 col-sm-5 col-xs-12 L32">Spouse Name</div>
                                                        <div class="col-md-8 col-sm-7 col-xs-12">
                                                            <input autocomplete="off" class="form-control" type="text" value="<?= $data['spousename']?>" name="tempDependantSpousName_<?= $data['id']?>">
                                                        </div>
                                                    </div>

                                                    <div class="inlinerow Mtop10">
                                                        <div class="col-md-4 col-sm-5 col-xs-12 L32">Spouse Mobile</div>
                                                        <div class="col-md-8 col-sm-7 col-xs-12 phone-div">
                                                        <input autocomplete="off" class="form-control citycodes" id="dependantspousemobilecountrycode" maxlength="4" type="text" value="<?= $data['dependantmobilecountrycode']?>" name="tempDependantSpouseMobileCountryCode_<?= $data['id']?>">
                                                        <input autocomplete="off" class="form-control fullnumber-affiliated" id="dependantspousemobile" minlength="6" maxlength="13" type="text" value="<?= $data['dependantspousemobile']?>" name="tempDependantSpouseMobile_<?= $data['id']?>">
                                                        </div>
                                                    </div>

                                                    <div class="inlinerow Mtop10">
                                                        <div class="col-md-4 col-sm-5 col-xs-12 L32">Spouse DOB</div>
                                                        <div class="col-md-8 col-sm-7 col-xs-12">
                                                            <?= DatePicker::widget([
                                'name' => 'tempDependantSpouseDob_'.$data['id'],
                                      'id' => 'tempDependantSpouseDob_'.$data['id'],
                                        'value' => $data['dob']?date(yii::$app->params['dateFormat']['viewDateFormat'], strtotimeNew($data['spousedob'])):'',
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
                                                    <div class="inlinerow Mtop10">
                                                        <div class="col-md-4 col-sm-5 col-xs-12 L32">Wedding Anniversary</div>
                                                        <div class="col-md-8 col-sm-7 col-xs-12">
                                                            <?= DatePicker::widget([
                                'name' => 'tempDependantwdate_'.$data['id'],
                                      'id' => 'tempDependantwdate_'.$data['id'],
                                        'value' => $data['weddinganniversary']?date(yii::$app->params['dateFormat']['viewDateFormat'], strtotimeNew($data['weddinganniversary'])):'',
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
                                                </div>

                                            </div>
                                            <?php if ($i%2 == 2){ ?>
                                        </div>
                                        <?php } ?>
                                            <?php } ?>
                                </fieldset>
                            </div>
                            <?php } ?>
        </div>
        <div class="inlinerow text-center Mtop20">
            <?= Html::button('Save', ['class' => 'btn btn-success btn-lg valid','title'=>"Save"]) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
