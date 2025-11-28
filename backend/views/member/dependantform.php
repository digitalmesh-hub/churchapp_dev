<?php
   use yii\helpers\Html;
   use yii\widgets\ActiveForm;
 //  use backend\assets\AppAsset;
   use kartik\date\DatePicker;
    use backend\assets\MemberAsset;
   $assetName = MemberAsset::register($this);
   
  
    
   /* @var $this yii\web\View */
   /* @var $model common\models\extendedmodels\ExtendedDependant */
   /* @var $form yii\widgets\ActiveForm */
   ?>
<div class="extended-dependant-form">
   <input type ='hidden' name='memberId' value="<?= $memberId ?>" id = "memberId" class = 'memberId' "memberId" = "<?= $memberId ?>">
   <?php $form = ActiveForm::begin([
      'action' => '#',
      'options' => [
         'enctype' => 'multipart/form-data',
         'id' =>'form-dependant',
      ],
      ]); ?>
   <legend>Add Dependent</legend>
   <div class="row">
      <div class="col-md-12">
         <div class="inlinerow Mtop10">
            <div class="col-md-2 col-sm-2">Dependent Title <span style="color: red;">*</span></div>
            <div class="col-md-2 col-sm-3">
               <?= $form->field($dependantModel, 'titleid')->dropDownList(
                  $titlesArray,
                  [
                     'prompt'=>'Please Select',
                     'id' =>"dependantTitleId"     
                  ])->label(false);?>
            </div>
         </div>
      </div>
   </div>
   <div class="inlinerow Mtop20">
      <div class="col-md-2">Dependent Name <span style="color: red;">*</span></div>
      <div class="col-md-3">
         <?= $form->field($dependantModel, 'dependantname')->textInput(['maxlength' => true , 'id' => 'txtDependantName'])->label(false) ?>
      </div>
      <div class="col-md-2">DOB</div>
      <div class="col-md-3">
         <?= DatePicker::widget([
            'name' => 'dependantdob',
            'id' => 'dependantdob',                
            'value' => $dependantModel->dob?date(yii::$app->params['dateFormat']['viewDateFormat'], strtotimeNew($dependantModel->dob)):'',
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
      <div class="col-md-2">Relation</div>
      <div class="col-md-3">
         <?= $form->field($dependantModel, 'relation')->dropDownList(
            $relations,
            [
               'prompt'=>'Please Select',
               'id' => 'relation',
            ])->label(false);?>
      </div>
      <div class="col-md-2">Marital Status</div>
      <div class="col-md-3">
         <?= $form->field($dependantModel, 'ismarried')->dropDownList(
            $isMarried,
            [
               'prompt'=>'Please Select',
               'id' =>'DPMartialStatus',     
            ])->label(false);?>
      </div>
   </div>
   <div class="inlinerow Mtop10">
      <div class="col-md-2">Occupation</div>
      <div class="col-md-3">
         <?= $form->field($dependantModel, 'occupation')->textInput(['maxlength' => true, 'id' => 'txtDependantOccupation'])->label(false) ?>
      </div>
      <div class="col-md-2">Confirmed</div>
      <div class="col-md-3">
         <?= $form->field($dependantModel, 'confirmed')->checkbox(['id' => 'chkDependantConfirmed', 'label' => ''])->label(false) ?>
      </div>
   </div>
   <div class="inlinerow Mtop10">
      <div class="col-md-2">Photo</div>
      <div class="col-md-3">
         <?= $form->field($dependantModel, 'image')->fileInput(['class' => 'form-control'])->label(false); ?> 
      </div>
      <div class="col-md-2">Dependent Mobile</div>
      <div class="col-md-4 col-sm-2 phone-div">
         <?= $form->field($dependantModel, 'dependantmobilecountrycode')->textInput(['maxlength' => '4' ,'class' => 'form-control citycodes' , 'id' => 'dependantMobileCountryCode'])->label(false) ?>
         <?= $form->field($dependantModel, 'dependantmobile')->textInput(['minlength' => 6, 'maxlength' => 13 , 'id' => 'dependantMobile', 'class' => 'form-control fullnumber-affiliated'])->label(false) ?>
      </div>
   </div>
   <!-- Hidden spouse info -->
   <div  id="dependantspousediv" class="inlinerow maritalspouse Mtop20 <?= $dependantModel->ismarried==2 ?'':'nodisplay' ?>  ">
      <div class="col-md-12"><strong>Add Dependent spouse</strong></div>
      <div class="row">
         <div class="col-md-12 Mtop10">
            <div class="col-md-2 col-sm-2">Spouse Title </div>
            <div class="col-md-2 col-sm-3">
               <?= $form->field($spouseModel, 'spouse_title')->dropDownList(
                  $titlesArray,
                  [
                     'prompt'=>'Please Select',
                     'id' => 'dependantspousetitleid'    
                  ])->label(false);?>
            </div>
         </div>
         <div class="col-md-12 Mtop10">
            <div class="col-md-2 col-sm-2">Spouse Name</div>
            <div class="col-md-3 col-sm-3">
               <?= $form->field($spouseModel, 'spouse_name')->textInput(['maxlength' => true,'id' => 'txtdependantspousename'])->label(false) ?>
            </div>
            <div class="col-md-2 col-sm-2">DOB</div>
            <div class="col-md-3 col-sm-3">
               <?= DatePicker::widget([
                  'name' => 'dependantspousedob',
                  'id' => 'dependantspousedob',
                  'value' => $spouseModel->dob?date(yii::$app->params['dateFormat']['viewDateFormat'], strtotimeNew($spouseModel->dob)):'',
                  'options' => [
                  ],
                  'pluginOptions' => [
                      'autoclose'=>true,
                      'format' => 'dd MM yyyy',
                      'endDate' =>'0d'
                  ]
                  ]);?>
            </div>
         </div>
         <div class="col-md-12 Mtop10">
            <div class="col-md-2 col-sm-2">Wedding Anniversary</div>
            <div class="col-md-3 col-sm-3">
               <?= DatePicker::widget([
                  'name' => 'dependantweddingdate',
                  'id' => 'dependantweddingdate',
                  'value' => $spouseModel->wedding_anniversary?date(yii::$app->params['dateFormat']['viewDateFormat'], strtotimeNew($spouseModel->wedding_anniversary)):'', 
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

            <div class="col-md-2 col-sm-2">Spouse Mobile</div>
            <div class="col-md-4 col-sm-22 phone-div">
            <?= $form->field($spouseModel, 'spouse_mobile_country_code')->textInput(['maxlength' => '4' ,'class' => 'form-control citycodes' , 'id' => 'dependantSpouseMobileCountryCode'])->label(false) ?>
            <?= $form->field($spouseModel, 'spouse_mobile')->textInput(['minlength' => 6, 'maxlength' => 13,'id' => 'dependantSpouseMobile', 'class' => 'form-control fullnumber-affiliated'])->label(false) ?>
            </div>
         </div>
         <div class="col-md-12 Mtop10">
            <div class="col-md-2 col-sm-2">Occupation</div>
            <div class="col-md-3 col-sm-3">
               <?= $form->field($spouseModel, 'spouse_occupation')->textInput(['maxlength' => true,'id' => 'txtdependantspouseoccupation'])->label(false) ?>
            </div>
            <div class="col-md-2 col-sm-2">Confirmed</div>
            <div class="col-md-3 col-sm-3">
               <label>
                  <input type="checkbox" id="dependantspouseconfirmed" name="dependantspouseconfirmed" value="1" <?= (isset($spouseModel->confirmed) && $spouseModel->confirmed == 1) ? 'checked' : '' ?>>
               </label>
            </div>
         </div>
         <div class="col-md-12 Mtop20">
            <div class="col-md-2 col-sm-2">Photo</div>
            <div class="col-md-3 col-sm-3">
               <?= $form->field($spouseModel, 'spouseImage')->fileInput(['class' => 'form-control'])->label(false); ?> 
            </div>
    
      </div>
      </div>
   </div>
   <!-- /.Hidden spouse info -->
   <input type ="hidden" id ="spouseDependantId" value = "<?= $spouseModel->id ?>">
   <input type ="hidden" id ="dependantId" value = "<?= $dependantModel->id ?>">
   <!-- Add button -->
   <div class="inlinerow dpsection">
      <div class="col-md-12 col-sm-12 text-right">
         <input type="button" id="btnAddDepend" title = "Add" value="<?= $dependantModel->id?"Update":"Add"?>" class="btn btn-primary">
         &nbsp;&nbsp;
         <input type="button" id="btnCancelDepend" title = "Cancel" value="Cancel" class="btn btn-primary">                                                 
      </div>
   </div>
   <!-- /.Add button -->
   <div class="form-group">
   </div>
   <?php ActiveForm::end(); ?>
</div>