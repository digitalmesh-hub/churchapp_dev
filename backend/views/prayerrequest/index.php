<?php
   use yii\helpers\Html;
   use kartik\date\DatePicker;
   use yii\widgets\ActiveForm;
   use backend\assets\AppAsset;
   use yii\widgets\ListView;
   use yii\widgets\Pjax;
   
   
   $assetName = AppAsset::register($this);
   $this->registerJsFile(
       $assetName->baseUrl . '/theme/js/Remember.Prayerrequest.ui.js',
       [
           'depends' => [
                   AppAsset::className()
           ]
       ]
   );

   $this->title = 'Prayer Requests';
   ?>
<div class="extended-prayerrequest-index">
   <div class="col-md-12 col-sm-12 Mtop15">
      <div class="col-md-12 col-sm-12 pageheader Mtop15">Prayer Requests</div>
      <div class="col-md-12 col-sm-12 contentbg">
         <div class="col-md-12 col-sm-12 Mtopbot20">
            <div class="blockrow">
               <ul class="nav nav-tabs" role="tablist">
                  <li role="presentation" class="active" id="prayerrequestli"><a href="#prayers" aria-controls="prayers" reload="prayerrequest/index/" role="tab" data-toggle="tab">Prayer Requests</a></li>
                  <li role="presentation" id="prayerrequestsettingsli"><a href="#prayersettings" aria-controls="prayersettings" role="tab" data-toggle="tab">Prayer Request Settings</a></li>
               </ul>
               <div class="tab-content tabcontentborder">
                  <div role="tabpanel" id="PrayerSearchListDiv">
                     <div class="tab-pane fade active in" id="prayers" role="tabpanel">
                        <div class="col-md-12 col-sm-12 contentbg">
                           <div class="inlinerow">
                              <?php $form = ActiveForm::begin(['action' => ['index'],'method' => 'get']); ?>             
                              <div class="col-md-3 col-sm-3">
                                 <div class="labelbox"><strong>Start Date</strong></div>
                                 <div class="inlinerow Mtop10">
                                    <?= $form->field($searchModel, 'created_time_start')->widget(DatePicker::classname(), [
                                       'options' => [
                                           'placeholder' => 'Enter start date',
                                           'id' =>'start',
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
                                    <?= $form->field($searchModel, 'created_time_end')->widget(DatePicker::classname(), [
                                       'options' => [
                                           'placeholder' => 'Enter end date',
                                           'id' =>'end',
                                       ],
                                       'pluginOptions' => [
                                           'autoclose'=>true,
                                           'format' => 'dd MM yyyy',   
                                       ]
                                       ])->label(false); ?> 
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
                        </div>
                     </div>
                  </div>
                  <div class="tab-pane fade" id="prayersettings" role="tabpanel">
                     <div class="inlinerow Mtop20">
                        <div class="col-md-12 col--12"><strong>Please enter email addresses</strong></div>
                        <div class="inlinerow Mtop10">
                           <div class="col-md-4 col-sm-4">
                              <?php  ?>                          
                              <input class="form-control" id="txtprayeremail" maxlength="500" name="PrayerRequestEmailId" value="<?php echo $getmail['prayeremail'] ?>" type="text">
                           </div>
                           <div class="col-md-2 col-sm-2">
                              <button id="btnSavePrayerEmail" class="btn btn-primary" url="prayerrequest/save-prayer-request-email/"><span class="glyphicon glyphicon-plus-sign"></span>&nbsp;Add</button>
                           </div>
                           <!-- Validation errors -->      
                           <!-- /.Validation errors -->
                        </div>
                     </div>
                  </div>
                  <div class="inlinerow Mtop20" id="PrayerRequestListDiv">
                     <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                        <?php if(!empty( $dataProvider)) {?>
                        <?php Pjax::begin(); ?>
                        <?= ListView::widget(
                           [
                           'dataProvider' => $dataProvider,
                           'itemView' => '_view',
                           'layout' => '{items} </tbody></table><div class="blockrow Mtop25 text-right">{pager}</div>',
                           ]
                           ); ?>
                        <?php Pjax::end(); ?>
                        <?php }else { ?>
                        <div>
                           <label style="vertical-align: middle; padding-left: 40%;">No Record found</label>
                        </div>
                        <?php } ?>
                     </div>
                  </div>
               </div>
            </div>
            <div role="tabpanel" id="PrayerRquestSettingsDiv"></div>
         </div>
      </div>
   </div>
</div>
</div>