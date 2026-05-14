<?php
   use backend\assets\AppAsset;
   use yii\helpers\Html;
   use yii\widgets\ActiveForm;
   ?>
<style>
.person-card {
    border: 2px solid #ddd;
    margin: 15px 0;
    padding: 20px;
    border-radius: 8px;
    background: #fff;
    transition: all 0.3s;
}
.person-card:hover {
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
.person-card.selected-person {
    border: 3px solid #5cb85c !important;
    background: #f0fff0 !important;
    box-shadow: 0 0 15px rgba(92, 184, 92, 0.5);
}
.person-card .card-type-badge {
    display: inline-block;
    padding: 5px 15px;
    border-radius: 3px;
    font-size: 12px;
    font-weight: bold;
    margin-bottom: 10px;
}
.person-card .card-type-member {
    background: #337ab7;
    color: white;
}
.person-card .card-type-spouse {
    background: #d9534f;
    color: white;
}
.person-card .card-type-dependant {
    background: #f0ad4e;
    color: white;
}
</style>
<?php if($memberDetails || $spouseDetails || $dependants) { ?>
<div class="committee-add-container">
   
   <!-- Search Result Cards -->
   <div class="row">
      <div class="col-md-12">
         <h4 style="margin: 20px 0 10px 0; color: #333;">
            <i class="glyphicon glyphicon-search"></i> 
            <strong>Search Results - Select Person to Add to Committee</strong>
         </h4>
         <div class="alert alert-info" style="margin-bottom: 20px;">
            <i class="glyphicon glyphicon-info-sign"></i>
            Click the "Add to Committee" button on any person below to select them for committee assignment.
         </div>
      </div>
   </div>

   <div class="row">
      <div class="col-md-12">
         <!-- Member Card -->
         <?php if ($memberDetails && !empty($memberDetails[0])) { 
            $member = $memberDetails[0]; ?>
         <div class="person-card" data-person-type="member" data-member-id="<?= Html::encode($member['memberid']) ?>">
            <span class="card-type-badge card-type-member">MEMBER</span>
            <div class="row">
               <div class="col-md-2 text-center">
                  <?php if($member['memberimage'] && $member['memberimage'] != null){ ?>
                     <img class="img-rounded" style="width: 120px; height: 120px; object-fit: cover;" 
                          src="<?php echo Yii::$app->params['imagePath'].$member['memberimage']?>">
                  <?php } else { ?>
                     <img class="img-rounded" style="width: 120px; height: 120px; object-fit: cover;" 
                          src="/img/default-user.png">
                  <?php } ?>
               </div>
               <div class="col-md-7">
                  <h3 style="margin-top: 0; color: #337ab7;">
                     <strong><?= Html::encode($member['membername'])?></strong>
                  </h3>
                  <p style="margin: 5px 0;"><strong>Member #:</strong> <?= Html::encode($member['memberno'])?></p>
                  <p style="margin: 5px 0;"><strong>Mobile:</strong> <?= Html::encode($member['mobilephone'])?></p>
                  <p style="margin: 5px 0;"><strong>Email:</strong> <?= Html::encode($member['emailaddress'])?></p>
                  <?php if (!empty($member['membersince'])) { ?>
                  <p style="margin: 5px 0;"><strong>Member Since:</strong> <?= date("Y", strtotimeNew($member['membersince'])) ?></p>
                  <?php } ?>
               </div>
               <div class="col-md-3 text-right">
                  <button class="btn btn-success btn-lg select-person-for-committee" 
                          data-person-type="member"
                          data-member-id="<?= Html::encode($member['memberid']) ?>"
                          data-user-id="<?= Html::encode(!empty($member['userid']) ? $member['userid'] : '0') ?>"
                          data-institution-id="<?= Html::encode($member['institutionid']) ?>"
                          data-is-spouse="0"
                          data-dependant-id=""
                          data-person-name="<?= Html::encode($member['membername']) ?>"
                          style="margin-top: 35px;">
                     <i class="glyphicon glyphicon-plus-sign"></i> Add to Committee
                  </button>
               </div>
            </div>
         </div>
         <?php } ?>

         <!-- Spouse Card -->
         <?php if ($spouseDetails && !empty($spouseDetails[0])) { 
            $spouse = $spouseDetails[0]; ?>
         <div class="person-card" data-person-type="spouse" data-member-id="<?= Html::encode($spouse['memberid']) ?>">
            <span class="card-type-badge card-type-spouse">SPOUSE</span>
            <div class="row">
               <div class="col-md-2 text-center">
                  <?php if($spouse['memberimage'] && $spouse['memberimage'] != null){ ?>
                     <img class="img-rounded" style="width: 120px; height: 120px; object-fit: cover;" 
                          src="<?php echo Yii::$app->params['imagePath'].$spouse['memberimage']?>">
                  <?php } else { ?>
                     <img class="img-rounded" style="width: 120px; height: 120px; object-fit: cover;" 
                          src="/img/default-user.png">
                  <?php } ?>
               </div>
               <div class="col-md-7">
                  <h3 style="margin-top: 0; color: #d9534f;">
                     <strong><?= Html::encode($spouse['membername'])?></strong>
                  </h3>
                  <p style="margin: 5px 0;"><strong>Relation:</strong> Spouse of <?= Html::encode($memberDetails[0]['membername']) ?></p>
                  <p style="margin: 5px 0;"><strong>Mobile:</strong> <?= Html::encode($spouse['mobilephone'])?></p>
                  <p style="margin: 5px 0;"><strong>Email:</strong> <?= Html::encode($spouse['emailaddress'])?></p>
               </div>
               <div class="col-md-3 text-right">
                  <button class="btn btn-success btn-lg select-person-for-committee" 
                          data-person-type="spouse"
                          data-member-id="<?= Html::encode($spouse['memberid']) ?>"
                          data-user-id="<?= Html::encode(!empty($memberDetails[0]['userid']) ? $memberDetails[0]['userid'] : '0') ?>"
                          data-institution-id="<?= Html::encode($memberDetails[0]['institutionid']) ?>"
                          data-is-spouse="1"
                          data-dependant-id=""
                          data-person-name="<?= Html::encode($spouse['membername']) ?>"
                          style="margin-top: 35px;">
                     <i class="glyphicon glyphicon-plus-sign"></i> Add to Committee
                  </button>
               </div>
            </div>
         </div>
         <?php } ?>

         <!-- Dependant Cards -->
         <?php if ($dependants && count($dependants) > 0) { 
            foreach ($dependants as $dependant) { ?>
         <div class="person-card" data-person-type="dependant" data-dependant-id="<?= Html::encode($dependant['id']) ?>">
            <span class="card-type-badge card-type-dependant">DEPENDANT</span>
            <div class="row">
               <div class="col-md-2 text-center">
                  <?php if(!empty($dependant['image'])){ ?>
                     <img class="img-rounded" style="width: 120px; height: 120px; object-fit: cover;" 
                          src="<?php echo Yii::$app->params['imagePath'].$dependant['image']?>">
                  <?php } else { ?>
                     <img class="img-rounded" style="width: 120px; height: 120px; object-fit: cover;" 
                          src="/img/default-user.png">
                  <?php } ?>
               </div>
               <div class="col-md-7">
                  <h3 style="margin-top: 0; color: #f0ad4e;">
                     <?php if (!empty($dependant['title'])) { ?>
                        <?= Html::encode($dependant['title']) ?> 
                     <?php } ?>
                     <strong><?= Html::encode($dependant['dependantname'])?></strong>
                  </h3>
                  <p style="margin: 5px 0;"><strong>Relation:</strong> <?= Html::encode($dependant['relation'] ?: 'Not specified') ?></p>
                  <p style="margin: 5px 0; display:none;"><strong>Parent Member:</strong> <?= Html::encode($memberDetails[0]['membername']) ?></p>
                  <?php if (!empty($dependant['dependantmobile'])) { ?>
                  <p style="margin: 5px 0;"><strong>Mobile:</strong> <?= Html::encode($dependant['dependantmobile'])?></p>
                  <?php } ?>
                  <?php if (!empty($dependant['dob'])) { ?>
                  <p style="margin: 5px 0; display:none;"><strong>DOB:</strong> <?= Html::encode($dependant['dob'])?></p>
                  <?php } ?>
               </div>
               <div class="col-md-3 text-right">
                  <button class="btn btn-success btn-lg select-person-for-committee" 
                          data-person-type="dependant"
                          data-member-id="<?= Html::encode($memberDetails[0]['memberid']) ?>"
                          data-user-id="<?= Html::encode(!empty($memberDetails[0]['userid']) ? $memberDetails[0]['userid'] : '0') ?>"
                          data-institution-id="<?= Html::encode($memberDetails[0]['institutionid']) ?>"
                          data-is-spouse="0"
                          data-dependant-id="<?= Html::encode($dependant['id']) ?>"
                          data-person-name="<?= Html::encode($dependant['dependantname']) ?>"
                          style="margin-top: 35px;">
                     <i class="glyphicon glyphicon-plus-sign"></i> Add to Committee
                  </button>
               </div>
            </div>
         </div>
         <?php } 
         } ?>
      </div>
   </div>

   <!-- Committee Assignment Section -->
   <div class="row" id="CommitteeAssignmentSection" style="display:none;">
      <div class="col-md-12">
         <?php $form = ActiveForm::begin() ?>
         <?php echo Html::hiddenInput(
            'admin-save-committee-member-Url',
            \Yii::$app->params['ajaxUrl']['admin-save-committee-member-Url'],
            [
                'id'=>'admin-save-committee-member-Url'
            ]
            ); ?>
         <?php echo Html::hiddenInput('selected-person-type', '',
            [
                'id'=>'selected-person-type'
            ]
         ); ?>
         <?php echo Html::hiddenInput('selected-member-id', '',
            [
                'id'=>'selected-member-id'
            ]
         ); ?>
         <?php echo Html::hiddenInput('selected-user-id', '',
            [
                'id'=>'selected-user-id'
            ]
         ); ?>
         <?php echo Html::hiddenInput('selected-institution-id', '',
            [
                'id'=>'selected-institution-id'
            ]
         ); ?>
         <?php echo Html::hiddenInput('selected-is-spouse', '',
            [
                'id'=>'selected-is-spouse'
            ]
         ); ?>
         <?php echo Html::hiddenInput('selected-dependant-id', '',
            [
                'id'=>'selected-dependant-id'
            ]
         ); ?>
         <?php echo Html::hiddenInput('selected-person-name', '',
            [
                'id'=>'selected-person-name'
            ]
         ); ?>
         
         <table class="table defaulttab" cellspacing="0" cellpadding="0">
            <tbody>
               <tr>
                  <th colspan="2" bgcolor="#5cb85c" class="text-center" style="color: white; font-size: 16px;">
                     <span id="AssignmentTypeLabel">
                        <i class="glyphicon glyphicon-user"></i> Assign to Committee
                     </span>
                  </th>
               </tr>
               <tr>
                  <td colspan="2">
                     <div class="alert alert-success" id="SelectedPersonInfo" style="margin: 10px 0;">
                        <!-- Will be populated by JavaScript -->
                     </div>
                  </td>
               </tr>
               <tr>
                  <td width="30%"><strong>Committee:</strong>&nbsp; <span style="color: red;">*</span></td>
                  <td>
                     <?= $form->field($committeeModel, 'committeeType'
                        )
                        ->dropDownList(
                            $committeTypeList,
                            [
                             'id' => 'committeeTypeId',
                             'prompt' => 'Please Select'
                           ]
                        )->label(false) ?>
                  </td>
               </tr>
               <tr>
                  <td><strong>Designation:</strong>&nbsp; <span style="color: red;">*</span></td>
                  <td>
                     <?= $form->field($committeeModel, 'designationType'
                        )
                        ->dropDownList(
                            $committeDesignationList,
                            [
                             'id' => 'designationType',
                             'prompt' => 'Please Select'
                           ]
                        )->label(false) ?>
                  </td>
               </tr>
               <tr>
                  <td><strong>Period:</strong>&nbsp; <span style="color: red;">*</span></td>
                  <td>
                     <?= $form->field($committeeModel, 'periodType'
                        )
                        ->dropDownList(
                            [],
                            [
                             'id' => 'periodType',
                             'prompt' => 'Please Select'
                           ]
                        )->label(false) ?>                                             
                  </td>
               </tr>
               <tr>
                <td><span></span></td>
                 <td>
                   <?= Html::button('Save', ['class' => 'btn btn-primary btn-lg saveCommitteeMember', 'title' => Yii::t('yii', 'save')]) ?>
                   <button type="button" class="btn btn-default btn-lg cancel-selection" style="margin-left: 10px;">
                      Cancel Selection
                   </button>
                 </td>
               </tr>
            </tbody>
         </table>
         <?php ActiveForm::end() ?>
         <div class="inlinerow Mtop10 text-center" id="errorDiv" style="display: none">
            <div class="alert alert-danger" role="alert" id="errorMessageDiv"><strong>Error!! Please fill in the fields above</strong></div>
         </div>
         <div class="inlinerow Mtop10 text-center" style="display: none" id="SuccessMessageDiv">
            <div class="alert alert-success message" role="alert"></div>
         </div>
      </div>
   </div>
</div>
<?php 
   } 
   else
     { ?>
<div class="nocommitteemember">
   No Records Found  
</div>
<?php } ?>