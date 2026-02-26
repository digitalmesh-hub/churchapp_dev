<?php
   use yii\helpers\Html;
   use yii\helpers\Url;
   use yii\grid\GridView;
   use yii\widgets\Pjax;
   use backend\assets\AppAsset;
   use yii\widgets\ActiveForm;
   use backend\components\widgets\FlashResult;
   
   $assetName = AppAsset::register($this);
   $this->title = $typeList;
   
   $this->registerJsFile(
        $assetName->baseUrl . '/theme/js/Remember.memberList.ui.js',
        [
                'depends' => [
                        AppAsset::className()
                ]
        ]
   );
   
   echo Html::hiddenInput(
        'delete-member',
        \Yii::$app->params['ajaxUrl']['delete-member'],
        [
                'id'=>'delete-member'
        ]
   );
   
   ?>
<div class="col-md-12 col-sm-12 pageheader Mtop15">
   <?= $typeList ?>
</div>
<div class="extended-member-index">
   <!-- Content -->
   <div class="col-md-12 col-sm-12 contentbg">
      <div class="Mtop10">
         <?= FlashResult::widget(); ?>
      </div>
      <div class="col-md-12 col-sm-12 Mtopbot20">
         <!-- Tab Panels -->
         <div class="blockrow">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs" role="tablist">
               <li role="presentation" class="active" id="memberlistli"><a href="#memberlist" aria-controls="home" role="tab" data-toggle="tab"><?= $typeList ?></a></li>
               <?php if($typeList != "Staff List") {?>
               <li role="presentation" id="approvalli"><a href="#approval" aria-controls="profile" role="tab" data-toggle="tab" id="tempmember">Pending Approval <span id="pendingcount">(<?= $pendingRequest ?>)</span></a></li>
               <?php }?>
            </ul>
            <!-- Member list -->
            <div role="tabpanel" class="tab-pane fade in active" id="memberlist">
               <!-- content -->
               <div class="blockrow Mtop25">
                  <?php $form = ActiveForm::begin(
                     [
                     'method' => 'get',
                     ]
                     ); ?>                    
                  <div class="col-md-12">
                     <div class="col-md-4 col-md-offset-1 col-sm-4 col-sm-offset-1">
                        <?= $form->field($searchModel, 'searchParam')->textInput(['placeholder' => 'Search by name/nick name/Mobile Number/batch'])->label(false); ?>
                     </div>
                     <div class="col-md-3 col-sm-4">
                        <?= $form->field($searchModel, 'memberno')->textInput(['placeholder' => 'Search by Membership Number'])->label(false); ?>
                     </div>
                     <div class="col-md-1 col-sm-1">
                        <?= Html::submitButton('Search', ['class' => 'btn btn-primary','title' => Yii::t('yii', 'Search')]) ?>
                     </div>

                  <?php
                  $urlPath = parse_url(Yii::$app->request->url, PHP_URL_PATH);
                  if ($urlPath == '/member/index') { ?>
                     <?php if(Yii::$app->user->can('fe083df2-ec49-11e6-b48e-000c2990e707')): ?>
                        <div class="col-md-2 col-sm-2">
                           <?= Html::a('Export', '#', ['class' => 'btn btn-success','title' => 'Export with Advanced Filters', 'data-toggle' => 'modal', 'data-target' => '#exportFilterModal']) ?>
                        </div>
                     <?php endif; 
                     }
                  ?>
                     
                  </div>
                  <?php ActiveForm::end(); ?>                               
               </div>
               <div class="blockrow Mtop20" id="memberlistdiv">
                  <?= $memberList ?>
               </div>
            </div>
            <div role="tabpanel" class="tab-pane fade" id="pendinglist">
               <?= $pendingMemberList ?>
            </div>
         </div>
      </div>
   </div>
</div>

<!-- Export Filter Modal -->
<div class="modal fade" id="exportFilterModal" tabindex="-1" role="dialog" aria-labelledby="exportFilterModalLabel">
   <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="exportFilterModalLabel">Export Members</h4>
         </div>
         <div class="modal-body">
            <!-- Export Type Tabs -->
            <ul class="nav nav-tabs" role="tablist">
               <li role="presentation" class="active">
                  <a href="#general-export-tab" aria-controls="general-export" role="tab" data-toggle="tab">General</a>
               </li>
               <li role="presentation">
                  <a href="#birthday-export-tab" aria-controls="birthday-export" role="tab" data-toggle="tab">Birthday</a>
               </li>
               <li role="presentation">
                  <a href="#anniversary-export-tab" aria-controls="anniversary-export" role="tab" data-toggle="tab">Anniversary</a>
               </li>
               <li role="presentation">
                  <a href="#age-range-export-tab" aria-controls="age-range-export" role="tab" data-toggle="tab">Age Range</a>
               </li>
               <li role="presentation">
                  <a href="#occupation-export-tab" aria-controls="occupation-export" role="tab" data-toggle="tab">Occupation</a>
               </li>
               <li role="presentation">
                  <a href="#hof-export-tab" aria-controls="hof-export" role="tab" data-toggle="tab">Head of Family</a>
               </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content" style="padding-top: 20px;">
               
               <!-- General Export Tab -->
               <div role="tabpanel" class="tab-pane fade in active" id="general-export-tab">
                  <?php $form = ActiveForm::begin(['action' => ['/member/export-members-filtered'], 'method' => 'get', 'id' => 'general-export-form']); ?>
                  
                  <div class="row">
                     <div class="col-md-12">
                        <h5><strong>Membership Type</strong></h5>
                        <div class="col-md-12">
                           <div class="form-group">
                              <label>Membership Type</label>
                              <?= Html::dropDownList('membership_type', '', [
                                 'Primary' => 'Primary',
                                 'Fellowship' => 'Fellowship'
                              ], [
                                 'class' => 'form-control',
                                 'prompt' => '-- All Membership Types --'
                              ]) ?>
                              <p class="help-block">Select a membership type to filter or leave as "All" to include all types</p>
                           </div>
                        </div>
                     </div>
                  </div>
                  
                  <hr>
                  
                  <div class="row">
                     <div class="col-md-12">
                        <h5><strong>Member Since Date Range</strong></h5>
                        <div class="col-md-6">
                           <div class="form-group">
                              <label>From Date</label>
                              <?= Html::input('date', 'member_since_from', '', ['class' => 'form-control']) ?>
                           </div>
                        </div>
                        <div class="col-md-6">
                           <div class="form-group">
                              <label>To Date</label>
                              <?= Html::input('date', 'member_since_to', '', ['class' => 'form-control']) ?>
                           </div>
                        </div>
                     </div>
                  </div>
                  
                  <hr>
                  
                  <div class="row">
                     <div class="col-md-12">
                        <h5><strong>Additional Options</strong></h5>
                        <div class="col-md-6">
                           <div class="form-group">
                              <div class="checkbox">
                                 <label>
                                    <?= Html::checkbox('include_dependants', true, ['class' => 'include-dependants-checkbox', 'value' => 1]) ?>
                                    <strong>Include Dependant Details</strong>
                                 </label>
                              </div>
                              <small class="text-muted">Check this to include dependant information in the export</small>
                           </div>
                        </div>
                     </div>
                  </div>
                  
                  <div class="row" style="margin-top: 20px;">
                     <div class="col-md-12 text-right">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-success export-btn" data-form="general-export-form">Export</button>
                     </div>
                  </div>
                  
                  <?php ActiveForm::end(); ?>
               </div>

               <!-- Birthday Export Tab -->
               <div role="tabpanel" class="tab-pane fade" id="birthday-export-tab">
                  <?php $form = ActiveForm::begin(['action' => ['/member/export-members-filtered'], 'method' => 'get', 'id' => 'birthday-export-form']); ?>
                  
                  <!-- <div class="row">
                     <div class="col-md-12">
                        <div class="alert alert-info">
                           <i class="fa fa-info-circle"></i> <strong>Birthday Export Format:</strong> Each individual with a matching birthday will appear in a separate row with Name, Membership Number, Phone, Birthdate, Age, and Zone.
                        </div>
                     </div>
                  </div> -->
                  
                  <div class="row">
                     <div class="col-md-12">
                        <h5><strong>Birthday Filter</strong></h5>
                        <div class="col-md-4">
                           <div class="form-group">
                              <label>Birthday Month</label>
                              <?= Html::dropDownList('birthday_month', '', [
                                 '' => 'Select Month',
                                 '1' => 'January',
                                 '2' => 'February',
                                 '3' => 'March',
                                 '4' => 'April',
                                 '5' => 'May',
                                 '6' => 'June',
                                 '7' => 'July',
                                 '8' => 'August',
                                 '9' => 'September',
                                 '10' => 'October',
                                 '11' => 'November',
                                 '12' => 'December'
                              ], ['class' => 'form-control birthday-month']) ?>
                              <small class="text-muted">Select a month to filter birthdays</small>
                           </div>
                        </div>
                        <div class="col-md-4">
                           <div class="form-group">
                              <label>Birthday From (Day-Month)</label>
                              <?= Html::input('text', 'birthday_date_from', '', [
                                 'class' => 'form-control birthday-date-from day-month-input',
                                 'placeholder' => 'DD-MM (e.g., 15-01)',
                                 'maxlength' => '5',
                                 'title' => 'Enter day-month in DD-MM format (e.g., 15-01 for January 15)'
                              ]) ?>
                              <small class="text-muted">Or specify day-month range (DD-MM)</small>
                           </div>
                        </div>
                        <div class="col-md-4">
                           <div class="form-group">
                              <label>Birthday To (Day-Month)</label>
                              <?= Html::input('text', 'birthday_date_to', '', [
                                 'class' => 'form-control birthday-date-to day-month-input',
                                 'placeholder' => 'DD-MM (e.g., 31-01)',
                                 'maxlength' => '5',
                                 'title' => 'Enter day-month in DD-MM format (e.g., 31-01 for January 31)'
                              ]) ?>
                           </div>
                        </div>
                     </div>
                  </div>
                  
                  <hr>
                  
                  <div class="row">
                     <div class="col-md-12">
                        <h5><strong>Additional Options</strong></h5>
                        <div class="col-md-6">
                           <div class="form-group">
                              <div class="checkbox">
                                 <label>
                                    <?= Html::checkbox('include_dependants', true, ['class' => 'include-dependants-checkbox', 'value' => 1]) ?>
                                    <strong>Include Dependants</strong>
                                 </label>
                              </div>
                              <small class="text-muted">Check this to include dependant birthdays in the export</small>
                           </div>
                        </div>
                     </div>
                  </div>
                  
                  <div class="row" style="margin-top: 20px;">
                     <div class="col-md-12 text-right">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-success export-btn" data-form="birthday-export-form">Export</button>
                     </div>
                  </div>
                  
                  <?php ActiveForm::end(); ?>
               </div>

               <!-- Anniversary Export Tab -->
               <div role="tabpanel" class="tab-pane fade" id="anniversary-export-tab">
                  <?php $form = ActiveForm::begin(['action' => ['/member/export-members-filtered'], 'method' => 'get', 'id' => 'anniversary-export-form']); ?>
                  
                  <!-- <div class="row">
                     <div class="col-md-12">
                        <div class="alert alert-info">
                           <i class="fa fa-info-circle"></i> <strong>Anniversary Export Format:</strong> Each couple with a matching anniversary will appear in a separate row with Name, Spouse Name, Membership Number, Phone, Anniversary Date, and Zone.
                        </div>
                     </div>
                  </div> -->
                  
                  <div class="row">
                     <div class="col-md-12">
                        <h5><strong>Anniversary Filter</strong></h5>
                        <div class="col-md-4">
                           <div class="form-group">
                              <label>Anniversary Month</label>
                              <?= Html::dropDownList('marriage_month', '', [
                                 '' => 'Select Month',
                                 '1' => 'January',
                                 '2' => 'February',
                                 '3' => 'March',
                                 '4' => 'April',
                                 '5' => 'May',
                                 '6' => 'June',
                                 '7' => 'July',
                                 '8' => 'August',
                                 '9' => 'September',
                                 '10' => 'October',
                                 '11' => 'November',
                                 '12' => 'December'
                              ], ['class' => 'form-control marriage-month']) ?>
                              <small class="text-muted">Select a month to filter anniversaries</small>
                           </div>
                        </div>
                        <div class="col-md-4">
                           <div class="form-group">
                              <label>Anniversary From (Day-Month)</label>
                              <?= Html::input('text', 'marriage_date_from', '', [
                                 'class' => 'form-control marriage-date-from day-month-input',
                                 'placeholder' => 'DD-MM (e.g., 15-01)',
                                 'maxlength' => '5',
                                 'title' => 'Enter day-month in DD-MM format (e.g., 15-01 for January 15)'
                              ]) ?>
                              <small class="text-muted">Or specify day-month range (DD-MM)</small>
                           </div>
                        </div>
                        <div class="col-md-4">
                           <div class="form-group">
                              <label>Anniversary To (Day-Month)</label>
                              <?= Html::input('text', 'marriage_date_to', '', [
                                 'class' => 'form-control marriage-date-to day-month-input',
                                 'placeholder' => 'DD-MM (e.g., 31-01)',
                                 'maxlength' => '5',
                                 'title' => 'Enter day-month in DD-MM format (e.g., 31-01 for January 31)'
                              ]) ?>
                           </div>
                        </div>
                     </div>
                  </div>
                  
                  <hr>
                  
                  <div class="row">
                     <div class="col-md-12">
                        <h5><strong>Additional Options</strong></h5>
                        <div class="col-md-6">
                           <div class="form-group">
                              <div class="checkbox">
                                 <label>
                                    <?= Html::checkbox('include_dependants', true, ['class' => 'include-dependants-checkbox', 'value' => 1]) ?>
                                    <strong>Include Dependants</strong>
                                 </label>
                              </div>
                              <small class="text-muted">Check this to include dependant anniversaries in the export</small>
                           </div>
                        </div>
                     </div>
                  </div>
                  
                  <div class="row" style="margin-top: 20px;">
                     <div class="col-md-12 text-right">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-success export-btn" data-form="anniversary-export-form">Export</button>
                     </div>
                  </div>
                  
                  <?php ActiveForm::end(); ?>
               </div>

               <!-- Age Range Export Tab -->
               <div role="tabpanel" class="tab-pane fade" id="age-range-export-tab">
                  <?php $form = ActiveForm::begin(['action' => ['/member/export-members-filtered'], 'method' => 'get', 'id' => 'age-range-export-form']); ?>
                  
                  <div class="row">
                     <div class="col-md-12">
                        <div class="alert alert-info">
                           <i class="fa fa-info-circle"></i> <strong>Age Range Export:</strong> Export members and their dependants filtered by age range.
                        </div>
                     </div>
                  </div>
                  
                  <div class="row">
                     <div class="col-md-12">
                        <h5><strong>Age Range Filter</strong></h5>
                        <div class="col-md-6">
                           <div class="form-group">
                              <label>Minimum Age</label>
                              <?= Html::input('number', 'age_from', '', ['class' => 'form-control age-from', 'placeholder' => 'e.g., 18', 'min' => 0]) ?>
                              <small class="text-muted">Minimum age (leave empty for no minimum)</small>
                           </div>
                        </div>
                        <div class="col-md-6">
                           <div class="form-group">
                              <label>Maximum Age</label>
                              <?= Html::input('number', 'age_to', '', ['class' => 'form-control age-to', 'placeholder' => 'e.g., 65', 'min' => 0]) ?>
                              <small class="text-muted">Maximum age (leave empty for no maximum)</small>
                           </div>
                        </div>
                     </div>
                  </div>
                  
                  <hr>
                  
                  <div class="row">
                     <div class="col-md-12">
                        <h5><strong>Additional Options</strong></h5>
                        <div class="col-md-6">
                           <div class="form-group">
                              <div class="checkbox">
                                 <label>
                                    <?= Html::checkbox('include_dependants', true, ['class' => 'include-dependants-checkbox', 'value' => 1]) ?>
                                    <strong>Include Dependants</strong>
                                 </label>
                              </div>
                              <small class="text-muted">Check this to include dependants in the age range filter</small>
                           </div>
                        </div>
                     </div>
                  </div>
                  
                  <div class="row" style="margin-top: 20px;">
                     <div class="col-md-12 text-right">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-success export-btn" data-form="age-range-export-form">Export</button>
                     </div>
                  </div>
                  
                  <?php ActiveForm::end(); ?>
               </div>

               <!-- Occupation Export Tab -->
               <div role="tabpanel" class="tab-pane fade" id="occupation-export-tab">
                  <?php $form = ActiveForm::begin(['action' => ['/member/export-members-filtered'], 'method' => 'get', 'id' => 'occupation-export-form']); ?>
                  
                  <div class="row">
                     <div class="col-md-12">
                        <div class="alert alert-info">
                           <i class="fa fa-info-circle"></i> <strong>Occupation Export:</strong> Export members and their dependants filtered by one or more occupations. Select multiple occupations to include all members matching any of the selected occupations.
                        </div>
                     </div>
                  </div>
                  
                  <div class="row">
                     <div class="col-md-12">
                        <h5><strong>Occupation Filter</strong></h5>
                        <div class="col-md-12">
                           <div class="form-group">
                              <label>Occupation</label>
                              <?= Html::dropDownList('occupation[]', '', $occupations, [
                                 'class' => 'form-control occupation-field',
                                 'id' => 'occupation-select',
                                 'multiple' => true,
                              ]) ?>
                              <small class="text-muted">Select one or more occupations to filter members and dependants (type to search)</small>
                           </div>
                        </div>
                     </div>
                  </div>
                  
                  <hr>
                  
                  <div class="row">
                     <div class="col-md-12">
                        <h5><strong>Additional Options</strong></h5>
                        <div class="col-md-6">
                           <div class="form-group">
                              <div class="checkbox">
                                 <label>
                                    <?= Html::checkbox('include_dependants', true, ['class' => 'include-dependants-checkbox', 'value' => 1]) ?>
                                    <strong>Include Dependants</strong>
                                 </label>
                              </div>
                              <small class="text-muted">Check this to include dependants in the occupation filter</small>
                           </div>
                        </div>
                     </div>
                  </div>
                  
                  <div class="row" style="margin-top: 20px;">
                     <div class="col-md-12 text-right">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-success export-btn" data-form="occupation-export-form">Export</button>
                     </div>
                  </div>
                  
                  <?php ActiveForm::end(); ?>
               </div>

               <!-- Head of Family Export Tab -->
               <div role="tabpanel" class="tab-pane fade" id="hof-export-tab">
                  <div class="row">
                     <div class="col-md-12">
                        <div class="alert alert-info">
                           <i class="fa fa-info-circle"></i> <strong>Head of Family Export:</strong> Export a list of all families with their designated Head of Family.
                        </div>
                     </div>
                  </div>
                  
                  <div class="row" style="margin-top: 20px;">
                     <div class="col-md-12 text-right">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-success" id="export-hof-btn">
                           <i class="fa fa-download"></i> Export
                        </button>
                     </div>
                  </div>
               </div>

            </div>
         </div>
      </div>
   </div>
</div>

<!-- Export Loading Overlay -->
<div id="export-loading-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); z-index: 10000;">
   <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 40px 60px; border-radius: 8px; text-align: center; box-shadow: 0 4px 20px rgba(0,0,0,0.3);">
      <i class="fa fa-spinner fa-spin" style="font-size: 64px; color: #5cb85c; margin-bottom: 20px;"></i>
      <h3 style="margin: 0 0 10px 0; color: #333; font-weight: 600;">Generating Export File</h3>
      <p style="margin: 0; color: #666; font-size: 14px;">Please wait while we prepare your data...</p>
   </div>
</div>

<?php
$this->registerJs("
   // Helper function to validate DD-MM format
   function isValidDayMonth(dateStr) {
      if (!dateStr) return true; // Empty is valid (optional field)
      
      // Check basic format DD-MM
      if (!/^[0-9]{1,2}-[0-9]{1,2}$/.test(dateStr)) {
         return false;
      }
      
      var parts = dateStr.split('-');
      var day = parseInt(parts[0], 10);
      var month = parseInt(parts[1], 10);
      
      // Check month is valid (1-12)
      if (month < 1 || month > 12) {
         return false;
      }
      
      // Check day is valid (1-31, depending on month)
      if (day < 1 || day > 31) {
         return false;
      }
      
      // Days in each month (using non-leap year for Feb since we ignore year)
      var daysInMonth = [31, 29, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
      
      if (day > daysInMonth[month - 1]) {
         return false;
      }
      
      return true;
   }
   
   // Helper function to convert DD-MM to MMDD format for comparison
   function dayMonthToMMDD(dateStr) {
      if (!dateStr) return '';
      var parts = dateStr.split('-');
      var day = parseInt(parts[0], 10);
      var month = parseInt(parts[1], 10);
      return month.toString().padStart(2, '0') + day.toString().padStart(2, '0');
   }
   
   // Helper function to validate date range (from must not be after to)
   function isValidDateRange(fromDate, toDate) {
      if (!fromDate || !toDate) return true; // Empty is valid
      
      var fromMMDD = dayMonthToMMDD(fromDate);
      var toMMDD = dayMonthToMMDD(toDate);
      
      // From must not be later than To (same dates are allowed)
      if (fromMMDD > toMMDD) {
         return {
            valid: false,
            message: 'From date cannot be later than To date. Please ensure the date range is within the same year (e.g., 01-01 to 31-12).'
         };
      }
      
      return { valid: true };
   }
   
   // Helper function to format DD-MM date for display
   function formatDayMonth(dateStr) {
      if (!dateStr) return '';
      var parts = dateStr.split('-');
      return parts[0].padStart(2, '0') + '-' + parts[1].padStart(2, '0');
   }
   
   // Handle export form submission for all tabs
   $('.export-btn').on('click', function(e) {
      e.preventDefault();
      e.stopPropagation();
      
      var \$btn = $(this);
      var formId = \$btn.data('form');
      var form = $('#' + formId);
      
      // Validate based on form type
      if (formId === 'birthday-export-form') {
         var birthdayMonth = form.find('.birthday-month').val();
         var birthdayDateFrom = form.find('.birthday-date-from').val().trim();
         var birthdayDateTo = form.find('.birthday-date-to').val().trim();
         
         if (!birthdayMonth && !birthdayDateFrom && !birthdayDateTo) {
            alert('Please select a birthday month or date range to export.');
            return false;
         }
         
         if ((birthdayDateFrom && !birthdayDateTo) || (!birthdayDateFrom && birthdayDateTo)) {
            alert('Please specify both From and To day-month for birthday range.');
            return false;
         }
         
         // Validate DD-MM format and date validity
         if (birthdayDateFrom && !isValidDayMonth(birthdayDateFrom)) {
            alert('Birthday From date is invalid. Please enter a valid DD-MM format (e.g., 15-01 for January 15th).\\nDay must be 01-31 and Month must be 01-12.');
            return false;
         }
         if (birthdayDateTo && !isValidDayMonth(birthdayDateTo)) {
            alert('Birthday To date is invalid. Please enter a valid DD-MM format (e.g., 31-01 for January 31st).\\nDay must be 01-31 and Month must be 01-12.');
            return false;
         }
         
         // Validate date range
         if (birthdayDateFrom && birthdayDateTo) {
            var rangeCheck = isValidDateRange(birthdayDateFrom, birthdayDateTo);
            if (!rangeCheck.valid) {
               alert(rangeCheck.message);
               return false;
            }
         }
      } else if (formId === 'anniversary-export-form') {
         var marriageMonth = form.find('.marriage-month').val();
         var marriageDateFrom = form.find('.marriage-date-from').val().trim();
         var marriageDateTo = form.find('.marriage-date-to').val().trim();
         
         if (!marriageMonth && !marriageDateFrom && !marriageDateTo) {
            alert('Please select an anniversary month or date range to export.');
            return false;
         }
         
         if ((marriageDateFrom && !marriageDateTo) || (!marriageDateFrom && marriageDateTo)) {
            alert('Please specify both From and To day-month for anniversary range.');
            return false;
         }
         
         // Validate DD-MM format and date validity
         if (marriageDateFrom && !isValidDayMonth(marriageDateFrom)) {
            alert('Anniversary From date is invalid. Please enter a valid DD-MM format (e.g., 15-01 for January 15th).\\nDay must be 01-31 and Month must be 01-12.');
            return false;
         }
         if (marriageDateTo && !isValidDayMonth(marriageDateTo)) {
            alert('Anniversary To date is invalid. Please enter a valid DD-MM format (e.g., 31-01 for January 31st).\\nDay must be 01-31 and Month must be 01-12.');
            return false;
         }
         
         // Validate date range
         if (marriageDateFrom && marriageDateTo) {
            var rangeCheck = isValidDateRange(marriageDateFrom, marriageDateTo);
            if (!rangeCheck.valid) {
               alert(rangeCheck.message);
               return false;
            }
         }
      } else if (formId === 'age-range-export-form') {
         var ageFrom = form.find('.age-from').val();
         var ageTo = form.find('.age-to').val();
         
         if (!ageFrom && !ageTo) {
            alert('Please specify at least a minimum or maximum age to export.');
            return false;
         }
         
         if (ageFrom && ageTo && parseInt(ageFrom) > parseInt(ageTo)) {
            alert('Minimum age cannot be greater than maximum age.');
            return false;
         }
      } else if (formId === 'occupation-export-form') {
         var occupation = form.find('.occupation-field').val();
         
         if (!occupation || occupation.length === 0) {
            alert('Please select at least one occupation to filter by.');
            return false;
         }
      }
      // Note: hof-export-form doesn't require validation as it has a default 'All' option
      
      // Disable button and change text
      var originalText = \$btn.html();
      \$btn.prop('disabled', true).html('<i class=\"fa fa-spinner fa-spin\"></i> Exporting...');
      
      // Show loading overlay
      $('#export-loading-overlay').fadeIn(200);
      
      // Build URL with form data
      var formData = form.serializeArray();
      var queryString = $.param(formData);
      var exportUrl = form.attr('action') + '?' + queryString;
      
      // Use fetch API for clean binary file handling
      fetch(exportUrl)
         .then(function(response) {
            if (!response.ok) {
               throw new Error('Export failed');
            }
            
            // Get filename from Content-Disposition header or use default
            var filename = 'members_export.xlsx';
            var disposition = response.headers.get('Content-Disposition');
            if (disposition && disposition.indexOf('filename=') !== -1) {
               var filenameStart = disposition.indexOf('filename=') + 9;
               var filenameEnd = disposition.indexOf(';', filenameStart);
               if (filenameEnd === -1) filenameEnd = disposition.length;
               filename = disposition.substring(filenameStart, filenameEnd).replace(/[\"\\']/g, '');
            }
            
            return response.blob().then(function(blob) {
               return { blob: blob, filename: filename };
            });
         })
         .then(function(result) {
            // Create blob link to download
            var link = document.createElement('a');
            link.href = window.URL.createObjectURL(result.blob);
            link.download = result.filename;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            window.URL.revokeObjectURL(link.href);
            
            // Update overlay with success message
            $('#export-loading-overlay').find('div').html(
               '<i class=\"fa fa-check-circle\" style=\"font-size: 64px; color: #5cb85c; margin-bottom: 20px;\"></i>' +
               '<h3 style=\"margin: 0 0 10px 0; color: #333; font-weight: 600;\">Export Completed!</h3>' +
               '<p style=\"margin: 0; color: #666; font-size: 14px;\">Your file has been downloaded successfully.</p>'
            );
            
            // Close modal and hide overlay after showing success message
            setTimeout(function() {
               $('#exportFilterModal').modal('hide');
               $('#export-loading-overlay').fadeOut(300);
               \$btn.prop('disabled', false).html(originalText);
               
               // Reset overlay content for next use
               setTimeout(function() {
                  $('#export-loading-overlay').find('div').html(
                     '<i class=\"fa fa-spinner fa-spin\" style=\"font-size: 64px; color: #5cb85c; margin-bottom: 20px;\"></i>' +
                     '<h3 style=\"margin: 0 0 10px 0; color: #333; font-weight: 600;\">Generating Export File</h3>' +
                     '<p style=\"margin: 0; color: #666; font-size: 14px;\">Please wait while we prepare your data...</p>'
                  );
               }, 500);
            }, 2000);
         })
         .catch(function(error) {
            // Update overlay with error message
            $('#export-loading-overlay').find('div').html(
               '<i class=\"fa fa-exclamation-circle\" style=\"font-size: 64px; color: #d9534f; margin-bottom: 20px;\"></i>' +
               '<h3 style=\"margin: 0 0 10px 0; color: #333; font-weight: 600;\">Export Failed</h3>' +
               '<p style=\"margin: 0; color: #666; font-size: 14px;\">There was an error generating the export file. Please try again.</p>'
            );
            
            // Hide overlay and reset after showing error
            setTimeout(function() {
               $('#export-loading-overlay').fadeOut(300);
               \$btn.prop('disabled', false).html(originalText);
               
               // Reset overlay content
               setTimeout(function() {
                  $('#export-loading-overlay').find('div').html(
                     '<i class=\"fa fa-spinner fa-spin\" style=\"font-size: 64px; color: #5cb85c; margin-bottom: 20px;\"></i>' +
                     '<h3 style=\"margin: 0 0 10px 0; color: #333; font-weight: 600;\">Generating Export File</h3>' +
                     '<p style=\"margin: 0; color: #666; font-size: 14px;\">Please wait while we prepare your data...</p>'
                  );
               }, 500);
            }, 3000);
         });
      
      return false;
   });
   
   // Reset overlay when modal is closed manually
   $('#exportFilterModal').on('hidden.bs.modal', function() {
      $('#export-loading-overlay').fadeOut(300);
      $('.export-btn').prop('disabled', false);
      $('#export-hof-btn').prop('disabled', false);
   });
   
   // HOF Button Handler
   $('body').on('click', '#export-hof-btn', function(e) {
      e.preventDefault();
      e.stopPropagation();
      
      var \$btn = $(this);
      var originalText = \$btn.html();
      
      // Show loading overlay
      $('#export-loading-overlay').fadeIn(300);
      
      // Disable button and change text
      \$btn.prop('disabled', true).html('<i class=\"fa fa-spinner fa-spin\"></i> Exporting...');
      
      var fullUrl = '/member/export-members-filtered?export_hof=1';
      
      fetch(fullUrl)
      .then(function(response) {
         if (!response.ok) {
            throw new Error('Failed to generate file');
         }
         
         var filename = 'head_of_families_' + new Date().toISOString().split('T')[0] + '.xlsx';
         var disposition = response.headers.get('Content-Disposition');
         if (disposition && disposition.indexOf('filename=') !== -1) {
            var filenameStart = disposition.indexOf('filename=') + 9;
            var filenameEnd = disposition.indexOf(';', filenameStart);
            if (filenameEnd === -1) filenameEnd = disposition.length;
            filename = disposition.substring(filenameStart, filenameEnd).replace(/[\"\\']/g, '');
         }
         
         return response.blob().then(function(blob) {
            return { blob: blob, filename: filename };
         });
      })
      .then(function(result) {
         // Create blob link to download
         var link = document.createElement('a');
         link.href = window.URL.createObjectURL(result.blob);
         link.download = result.filename;
         document.body.appendChild(link);
         link.click();
         document.body.removeChild(link);
         window.URL.revokeObjectURL(link.href);
         
         // Update overlay with success message
         $('#export-loading-overlay').find('div').html(
            '<i class=\"fa fa-check-circle\" style=\"font-size: 64px; color: #5cb85c; margin-bottom: 20px;\"></i>' +
            '<h3 style=\"margin: 0 0 10px 0; color: #333; font-weight: 600;\">Export Completed!</h3>' +
            '<p style=\"margin: 0; color: #666; font-size: 14px;\">Your file has been downloaded successfully.</p>'
         );
         
         // Close modal and hide overlay after showing success message
         setTimeout(function() {
            $('#exportFilterModal').modal('hide');
            $('#export-loading-overlay').fadeOut(300);
            \$btn.prop('disabled', false).html(originalText);
            
            // Reset overlay content
            setTimeout(function() {
               $('#export-loading-overlay').find('div').html(
                  '<i class=\"fa fa-spinner fa-spin\" style=\"font-size: 64px; color: #5cb85c; margin-bottom: 20px;\"></i>' +
                  '<h3 style=\"margin: 0 0 10px 0; color: #333; font-weight: 600;\">Generating Export File</h3>' +
                  '<p style=\"margin: 0; color: #666; font-size: 14px;\">Please wait while we prepare your data...</p>'
               );
            }, 500);
         }, 2000);
      })
      .catch(function(error) {
         console.error('Export error:', error);
         
         // Update overlay with error message
         $('#export-loading-overlay').find('div').html(
            '<i class=\"fa fa-times-circle\" style=\"font-size: 64px; color: #d9534f; margin-bottom: 20px;\"></i>' +
            '<h3 style=\"margin: 0 0 10px 0; color: #333; font-weight: 600;\">Export Failed</h3>' +
            '<p style=\"margin: 0; color: #666; font-size: 14px;\">There was an error generating the export file. Please try again.</p>'
         );
         
         // Hide overlay and reset after showing error
         setTimeout(function() {
            $('#export-loading-overlay').fadeOut(300);
            \$btn.prop('disabled', false).html(originalText);
            
            // Reset overlay content
            setTimeout(function() {
               $('#export-loading-overlay').find('div').html(
                  '<i class=\"fa fa-spinner fa-spin\" style=\"font-size: 64px; color: #5cb85c; margin-bottom: 20px;\"></i>' +
                  '<h3 style=\"margin: 0 0 10px 0; color: #333; font-weight: 600;\">Generating Export File</h3>' +
                  '<p style=\"margin: 0; color: #666; font-size: 14px;\">Please wait while we prepare your data...</p>'
               );
            }, 500);
         }, 3000);
      });
   });
   
   // Birthday tab: Disable date range when month is selected
   $('#birthday-export-form .birthday-month').on('change', function() {
      var form = $('#birthday-export-form');
      if ($(this).val()) {
         form.find('.birthday-date-from, .birthday-date-to').val('').prop('disabled', true);
      } else {
         form.find('.birthday-date-from, .birthday-date-to').prop('disabled', false);
      }
   });
   
   // Birthday tab: Disable month when date range is selected
   $('#birthday-export-form .birthday-date-from, #birthday-export-form .birthday-date-to').on('change', function() {
      var form = $('#birthday-export-form');
      if (form.find('.birthday-date-from').val() || form.find('.birthday-date-to').val()) {
         form.find('.birthday-month').val('').prop('disabled', true);
      } else {
         form.find('.birthday-month').prop('disabled', false);
      }
   });
   
   // Anniversary tab: Disable date range when month is selected
   $('#anniversary-export-form .marriage-month').on('change', function() {
      var form = $('#anniversary-export-form');
      if ($(this).val()) {
         form.find('.marriage-date-from, .marriage-date-to').val('').prop('disabled', true);
      } else {
         form.find('.marriage-date-from, .marriage-date-to').prop('disabled', false);
      }
   });
   
   // Anniversary tab: Disable month when date range is selected
   $('#anniversary-export-form .marriage-date-from, #anniversary-export-form .marriage-date-to').on('change', function() {
      var form = $('#anniversary-export-form');
      if (form.find('.marriage-date-from').val() || form.find('.marriage-date-to').val()) {
         form.find('.marriage-month').val('').prop('disabled', true);
      } else {
         form.find('.marriage-month').prop('disabled', false);
      }
   });
   
   // Initialize Select2 on occupation dropdown when the tab is shown
   \$('a[href=\"#occupation-export-tab\"]').on('shown.bs.tab', function (e) {
      if (!\$('#occupation-select').hasClass('select2-hidden-accessible')) {
         \$('#occupation-select').select2({
            placeholder: '-- Select Occupations --',
            allowClear: true,
            width: '100%',
            dropdownParent: \$('#occupation-export-tab'),
            closeOnSelect: false
         });
      }
   });
   
   // Real-time validation for day-month inputs
   \$('.day-month-input').on('blur', function() {
      var input = \$(this);
      var value = input.val().trim();
      
      if (value && !isValidDayMonth(value)) {
         input.addClass('is-invalid');
         input.css('border-color', '#dc3545');
         
         // Show tooltip or alert
         var fieldName = input.closest('.form-group').find('label').text();
         input.attr('title', 'Invalid date: ' + fieldName + ' must be in DD-MM format with valid day (01-31) and month (01-12)');
      } else {
         input.removeClass('is-invalid');
         input.css('border-color', '');
         input.attr('title', 'Enter day-month in DD-MM format (e.g., 15-01 for January 15)');
      }
   });
   
   // Track previous value to detect deletion (works on all devices including mobile)
   var previousValues = {};
   
   \$('.day-month-input').on('focus', function() {
      var input = \$(this);
      previousValues[input.attr('name')] = input.val();
   });
   
   // Auto-format day-month inputs as user types
   \$('.day-month-input').on('input', function() {
      var input = \$(this);
      var value = input.val();
      var fieldName = input.attr('name');
      var previousValue = previousValues[fieldName] || '';
      
      // Detect if user is deleting by comparing lengths
      var isDeleting = value.length < previousValue.length;
      
      // Remove any non-digit or dash characters
      value = value.replace(/[^0-9-]/g, '');
      
      // Auto-add dash after 2 digits if not already there (but not when deleting)
      if (!isDeleting && value.length === 2 && value.indexOf('-') === -1) {
         value = value + '-';
      }
      
      // Limit to DD-MM format (max 5 chars)
      if (value.length > 5) {
         value = value.substring(0, 5);
      }
      
      input.val(value);
      
      // Update previous value for next input event
      previousValues[fieldName] = value;
   });
", \yii\web\View::POS_READY);
?>

