<?php

   use yii\helpers\Html;
   use kartik\date\DatePicker;
   use yii\widgets\ActiveForm;
   use backend\assets\AppAsset;
   use yii\helpers\ArrayHelper;
   use yii\widgets\ListView;
   use yii\widgets\Pjax;
   
   $assetName = AppAsset::register($this);
   $this->registerJsFile(
       $assetName->baseUrl . '/theme/js/Remember.Feedback.ui.js',
       [
           'depends' => [
                   AppAsset::className()
           ]
       ]
   );
   /* @var $this yii\web\View */
   /* @var $searchModel common\models\searchmodels\ExtendedFeedbackSearch */
   /* @var $dataProvider yii\data\ActiveDataProvider */
   $this->title = 'Feedback';
   $this->params['breadcrumbs'][] = $this->title;
   ?>
<div class="col-md-12 col-sm-12 pageheader Mtop15">Feedbacks</div>
<div class="col-md-12 col-sm-12 contentbg">
   <div class="blockrow">
      <ul class="nav nav-tabs" role="tablist" id="myTab">
         <li role="presentation" class="active" >
            <a href="#feedbacks" aria-controls="Feedbacks" role="tab" data-toggle="tab">Feedbacks</a>
         </li>
         <li role="presentation">
            <a href="#feedbacksettings" aria-controls="FeedbackSettings" role="tab" data-toggle="tab">Feedback Settings</a>
         </li>
      </ul>
      <!-- Tab panes -->        
      <div class="tab-content tabcontentborder">
         <div role="tabpanel" class="tab-pane fade active in" id="feedbacks">
            <div class="inlinerow">
               <?php $form = ActiveForm::begin(['action' => ['index'],'method' => 'get']); ?>             
               <div class="col-md-3 col-sm-3">
                  <div class="labelbox"><strong>Feedback type</strong></div>
                  <div class="inlinerow Mtop10">
                         <?= $form->field($searchModel, 'feedbacktypeid') ->dropDownList(
                                 $feedbackarray,
                                 [
                                    'prompt' => 'All',
                                    'id' => 'rating-search'
                                 ])->label(false);?> 
                  </div>
               </div>
               <div class="col-md-3 col-sm-3">
                  <div class="labelbox"><strong>Start Date</strong></div>
                  <div class="inlinerow Mtop10">
                     <?= $form->field($searchModel, 'start_date')->widget(DatePicker::classname(), [
                                       'options' => [
                                           'placeholder' => 'Enter start date',
                                           'id' =>'start-date',
                                       ],
                                       'pluginOptions' => [
                                           'autoclose'=>true,
                                           'format' => 'dd MM yyyy',
                                           
                                       ]
                     ])->label(false); ?> 
                  </div>
               </div>
               <div class="col-md-3 col-sm-3">
                  <div class="labelbox"><strong>End Date</strong></div>
                  <div class="inlinerow Mtop10">
                     <?= $form->field($searchModel, 'end_date')->widget(DatePicker::classname(), [
                                       'options' => [
                                           'placeholder' => 'Enter end date',
                                           'id' =>'end-date',
                                       ],
                                       'pluginOptions' => [
                                           'autoclose'=>true,
                                           'format' => 'dd MM yyyy',
                                           
                                       ]
                     ])->label(false); ?> 
                  </div>
               </div>
               <div class="col-md-3 col-sm-3">
                  <div class="labelbox"><strong>Rating</strong></div>
                  <div class="inlinerow Mtop10">
                       <?= $form->field($searchModel, 'feedbackrating') ->dropDownList(
                                          [ 
                                    0 => "0",
                                    1 => "1",
                                    2 => "2",
                                    3 => "3",
                                    4 => "4",
                                    5 => "5"
                                 ],
                                 [
                                    'prompt' => 'All',
                                    'id' => 'rating-search'
                                 ])->label(false);?> 
                  </div>
               </div>
               <div class="col-md-3 col-sm-3">
                  <div class="labelbox">&nbsp;</div>
                  <div class="inlinerow Mtop10">
                     <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
                  </div>
               </div>
               <?php ActiveForm::end(); ?>
            </div>
            <div class="inlinerow Mtop20" id="FeedbackListDiv">
               <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                  <?php if(!empty( $dataProvider->getModels())) {?>
                  <?= ListView::widget(
                     [
                     'dataProvider' => $dataProvider,
                     'itemView' => '_view',
                     
                     'layout' => '{items} </tbody></table><div class="blockrow Mtop25 text-right">{pager}</div>',
                     ]
                     ); ?>
                  <?php }else { ?>
                  <div>
                     <label style="vertical-align: middle; padding-left: 40%;">No Record found</label>
                  </div>
                  <?php } ?>    
               </div>
            </div>
         </div>
         <!-- </div> -->
         <div role="tabpanel" class="tab-pane fade" id="feedbacksettings">
            <div class="inlinerow Mtop20">
               <div class="col-md-12 col-sm-12"><strong>Please enter Feedback types &nbsp;<span style="color: red;">*</span></strong></div>
               <div class="inlinerow Mtop10">
                  <div class="col-md-4 col-sm-4"><input class="form-control" id="FeedbackTypeTextbox" maxlength="75" name="FeedbackTypeTextbox" value="" type="text"></div>
                  <div class="col-md-2 col-sm-2">
                     <button class="btn btn-primary" id="Savefeedback"  url="feedback/add-feedback-type/" reload="feedback/index/" ><span class="glyphicon glyphicon-plus-sign"></span>&nbsp;Add</button>
                  </div>
                  <div class="col-md-6 col-sm-6" id="ErrorDiv" hidden="hidden">
                     <div class="alert alert-danger text-center" role="alert" id="ErrorMessageDiv">
                        <strong>
                        <label id="ErrorMessageLabel">Feedback type Cannot be blank</label></strong>
                     </div>
                  </div>
                  <div class="col-md-6 col-sm-6 nodisplay" id="ErrorDiv">
                     <div class="alert alert-danger text-center" role="alert" id="ErrorMessageDiv">
                        <strong>
                        <label id="ErrorMessageLabel">Error! Please check again</label></strong>
                     </div>
                  </div>
               </div>
            </div>
            <div class="col-md-12 col-sm-12 Mtop20">
               <table class="table table-bordered" id="feedbacktypelisting" cellspacing="0" cellpadding="0">
                  <thead>
                     <tr>
                        <th width="70%">Feedback Types</th>
                        <th class="text-center" width="15%">Sort</th>
                        <th class="text-center" width="15%">Actions</th>
                     </tr>
                  </thead>
                  <?php if(!empty( $institutionfeedback)) {?>
                  <?= ListView::widget(
                     [
                     'dataProvider' => $institutionfeedback,
                     'itemView' => '_feedbacktype',
                     'layout' => '{items} </tbody></table><div class="blockrow Mtop25 text-right">{pager}</div>',
                     ]
                     ); ?>
                  <?php } else { ?>
                  <div>
                     <label style="vertical-align: middle; padding-left: 40%;">No Record found</label>
                  </div>
                  <?php } ?>
               </table>
               <div class="col-md-12 col-sm-12 Mtop20">
                  <legend style="font-size: 20px;">Feedback Emails</legend>
                  <div class="inlinerow Mtop10">
                     <div class="col-md-4 col-sm-4"><input class="form-control" id="txtfeedbackemail" maxlength="500" name="institution.FeedbackEmail" value="<?php echo $getmail['feedbackemail'] ?>" type="text"></div>
                     <div class="col-md-2 col-sm-2">
                        <button class="btn btn-primary" id="Savefeedbackemail" url="feedback/save-feedback-email/"><span class="glyphicon glyphicon-plus-sign"></span>&nbsp;Add</button>
                     </div>
                     <div class="col-md-6 col-sm-6" id="ErroraddmailDiv" hidden="hidden">
                        <div class="alert alert-danger text-center" role="alert" id="ErrorMessageDiv" >
                           <strong>
                           <label id="ErrorMessageLabel">Feedback Email Cannot be blank</label></strong>
                        </div>
                     </div>
                     <div class="col-md-6 col-sm-6" id="ErrorvalidatemaleDiv" hidden="hidden">
                        <div class="alert alert-danger text-center" role="alert" id="ErrorMessageDiv" >
                           <strong>
                           <label id="ErrorMessageLabel">Feedback Email has been Invalid</label></strong>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>