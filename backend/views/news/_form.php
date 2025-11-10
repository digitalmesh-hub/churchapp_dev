<?php
   use yii\helpers\Html;
   use yii\widgets\ActiveForm;
   use kartik\date\DatePicker;
   use backend\assets\AppAsset;
   use common\models\extendedmodels\ExtendedInstitution;
   use yii\jui\AutoComplete;
   use yii\web\JsExpression;
   
   $assetName = AppAsset::register ( $this );
   $this->registerJsFile ( $assetName->baseUrl . '/theme/js/Remember.Event.ui.js', [ 
   		'depends' => [ 
   				AppAsset::className () 
   		] 
   ] );
   
   $this->title = 'Register News';
   echo Html::hiddenInput('image-upload-url','news/upload',['id'=>'imageUpload']);
   ?>
<div class="extended-event-form">
   <div class="col-md-12 col-sm-12 pageheader Mtop15"><?= $this->title ?></div>
   <div class="col-md-12 col-sm-12 contentbg">
      <div class="col-md-12 col-sm-12 Mtopbot20">
         <div class="blockrow Mtop50">
            <legend style="font-size: 20px;">Notice Board </legend>
            <div class="blockrow Mtop20">
               <div class="inlinerow Mtop10">
                  <?php  $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
                  <div class="col-md-2 col-sm-2 L32">
                     Notice Board Title <span style="color: red;">*</span>
                  </div>
                  <div class="col-md-8 col-sm-8">
                     <?= $form->field($model, 'notehead')->textInput(['maxlength' => true])->label(false) ?>
                  </div>
               </div>
               <div class="inlinerow Mtop10">
                  <div class="col-md-2 col-sm-2 L32">
                     Notice Board Body <span style="color: red;">*</span>
                  </div>
                  <div class="col-md-8 col-sm-8 z-index-11">
                     <?= $form->field($model, 'notebody')->textarea(['rows' => '10','class' =>'input-description','id' => 'txtnotebody' ])->label(false) ?>
                     <!--   <textarea autocomplete="off" class="form-control input-description" cols="20" id="txtnotebody" maxlength="8000" name="NoteBody" rows="8" ></textarea> -->
                  </div>
               </div>
               <div class="inlinerow Mtop30">
                  <div class="col-md-2 col-sm-2 L32">Upload files &nbsp;</div>
                  <div class="col-md-6 col-sm-6">
                     <input type="file" name="exlfile" id="exlfile"
                        class="form-control">
                  </div>
                  <div class="col-md-3 col-sm-3">
                     <input type="button" class="btn btn-primary" value="Upload"
                        id="btnUpload" url='news/upload' title='upload file'>
                  </div>
               </div>
               <div class="col-md-2 col-sm-2 L32"></div>
               <div class="col-md-6 col-sm-6">
                  <div class="alert alert-danger text-center" hidden="hidden"
                     id="uploadErrorMessage" role="alert">Please select a valid file
                     (.jpeg,.jpg,.png,.pdf,.doc,docx,docs)
                  </div>
                  <div class="alert alert-danger text-center" hidden="hidden"
                     id="uploadErrorMessageEmptyFeild" role="alert">File
                     Cannot be blank
                  </div>
               </div>
               <div class="col-md-6 col-sm-6 Mtop20">
                  <!-- rows -->
                  <div class="inlinerow Mtop10">
                     <div class="col-md-4 col-sm-5 padleft0 L32">
                        Notice Board Date <span style="color: red;">*</span>
                     </div>
                     <div class="col-md-7 col-sm-7">
                        <?= $form->field($model, 'activitydate')->widget(DatePicker::classname(), [
                           'options' => [
                               'placeholder' => 'Choose date',
                               'id' =>'notice-board-activity-date',
                           ],
                           'pluginOptions' => [
                               'autoclose'=>true,
                               'format' => 'dd MM yyyy',
                               'startDate' =>'0d',
                           ]
                           ])->label(false); ?>  
                     </div>
                  </div>
                  <?php if(yii::$app->user->identity->institution->institutiontype == ExtendedInstitution::INSTITUTION_TYPE_CHURCH) { ?>
                     <div class="inlinerow Mtop10">
                        <div class="col-md-4 col-sm-5 padleft0 L32">Family Unit</div>
                        <div class="col-md-7 col-sm-7">
                           <?=$form->field ( $model, 'familyunitid' )->label ( false )->dropDownList ( $familyUnits, [ 'prompt' => 'Please select' ] )?>
                        </div>
                     </div>
                  <?php } ?>
                  <?php
                     $model->batch = ($model->batch==null) ? ['All'] :  explode(',',$model->batch);
                  ?>
                  <?php if(yii::$app->user->identity->institution->institutiontype == ExtendedInstitution::INSTITUTION_TYPE_EDUCATION) { ?>
                     <div class="inlinerow Mtop10">
                        <div class="col-md-4 col-sm-5 padleft0 L32">Batch</div>
                        <div class="col-md-7 col-sm-7">
                        <?= $form->field($model, 'batch') ->dropDownList(
                           $batches,
                           [
                              'class' => 'form-control drop-select',
                              'multiple'=> true,
                           ]
                           )
                        ->label(false);
                        ?>
                        </div>
                     </div>
                  <?php } ?>

               </div>

              
               <div class="col-md-6 col-sm-6 Mtop20">
                  <!-- rows -->
                  <div class="inlinerow Mtop10">
                     <div class="col-md-3 col-sm-4 L32">Expiry Date</div>
                     <div class="col-md-7 col-sm-7">
                        <?= $form->field($model, 'expirydate')->widget(DatePicker::classname(), [
                           'options' => [
                               'placeholder' => 'Choose date',
                               'id' =>'news-expriy-date',
                           ],
                           'pluginOptions' => [
                               'autoclose' => true,
                               'format' => 'dd MM yyyy',
                               'startDate' =>'0d',
                           ]
                           ])->label(false); ?>          
                     </div>
                  </div>
                  <!-- rows -->
                  <div class="inlinerow Mtop10">
                     <div class="col-md-3 col-sm-4 L32">Select Member</div>
                     <div class="col-md-7 col-sm-7">
                        <?= $form->field($model, 'members')->widget(\yii\jui\AutoComplete::classname(),[
                              'options' => [
                                 'class' => 'form-control member-append'
                              ],
                                 'clientOptions' => [
                                 'source' => $members,
                                 'autoFill' => true,
                                 'minLength'=> 2, 
                                 'select' => new JsExpression("function( event, ui ) {
                                      type = (ui.item.usertype == 'M') ? 'member' :'spouse'; 
                                      _url = 're-member://'+type+'/'+ ui.item.id;
                                      title =  ui.item.name;
                                       $('.input-description').summernote('createLink', {
                                                      text: title,
                                                      url: _url,
                                                      isNewWindow: true
                                       });
                                 }")
                              ],
                              ])->label(false);?>
                              <!-- $('.note-editable').append('&nbsp&nbsp<a href=' + _url + ' target=_blank >' + title + '</a>'); -->
                     </div>
                  </div>
               </div>
               <div class="inlinerow Mtop20 text-center">
                  <?= Html::submitButton('Save', ['class' => 'btn btn-success btn-extra','title' => 'Save']) ?>&nbsp;&nbsp;
                  <a href="/news/index" class="btn btn-danger btn-extra"
                     title='Cancel' id="btnCancel">&nbsp;Cancel</a>
               </div>
               <?php ActiveForm::end(); ?>
            </div>
         </div>
      </div>
   </div>
</div>
