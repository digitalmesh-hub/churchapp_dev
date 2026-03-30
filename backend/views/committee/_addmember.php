<?php
   use backend\assets\AppAsset;
   use yii\helpers\Html;
   use yii\widgets\ActiveForm;
   ?>
<style>
.dependant-card.selected-dependant {
    border: 3px solid #5cb85c !important;
    background: #f0fff0 !important;
    box-shadow: 0 0 10px rgba(92, 184, 92, 0.5);
}
</style>
<?php if($committeMemberDetails) { ?>
<div class="memberprofile">
   <div class="row">
      <div class="col-md-12 col-sm-8">
            <table class="table defaulttab" cellspacing="0" cellpadding="0">
               <tbody>
                  <tr>
                     <th colspan="2" bgcolor="#ADF7EE" class="text-center">Personal Details</th>
                  </tr>
                  <tr>
                    <td colspan="2" align="center">
                      <?php if($committeMemberDetails[0]['memberimage'] && $committeMemberDetails[0]['memberimage'] != null){ ?>
                        <img class="memberimg"  style="width: 160px; border-radius: 25px; height: 150px;" src="<?php echo Yii::$app->params['imagePath'].$committeMemberDetails[0]['memberimage']?>">
                      <?php } else { ?>
                      <img class="memberimg"  style="width: 160px; border-radius: 25px; height: 150px;" src="/img/default-user.png">
                    <?php } ?> 
                  </td>
                  </tr>
                  <tr>
                     <td><strong>Member Name :</strong></td>
                     <td class="word-wrap">
                        <?= Html::encode($committeMemberDetails[0]['membername'])?>
                     </td>
                  </tr>
                  <tr>
                     <td><strong>Membership Number :</strong></td>
                     <td><?= Html::encode($committeMemberDetails[0]['memberno'])?></td>
                  </tr>
                  <tr>
                     <td><strong>Member Since :</strong></td>
                     <td>
                        <?php 
                          if(!empty($committeMemberDetails[0]['membersince'])) {
                            echo date("Y", strtotimeNew($committeMemberDetails[0]['membersince']));
                          } else {
                            echo "Not Available";
                          } 
      
                          ?>
                     </td>
                  </tr>
                  <tr>
                     <td><strong>Office phone :</strong></td>
                     <td>
                        <?= Html::encode($committeMemberDetails[0]['officephone'])?>
                     </td>
                  </tr>
                  <tr>
                     <td><strong>Mobile phone :</strong></td>
                     <td>
                        <label id="MemberMobileLabel"><?= Html::encode($committeMemberDetails[0]['mobilephone'])?></label>
                     </td>
                  </tr>
                  <tr>
                     <td><strong>Email Address :</strong></td>
                     <td class="word-wrap">
                        <label id="MemberEmailLabel"><?= Html::encode($committeMemberDetails[0]['emailaddress'])?></label>
                     </td>
                  </tr>
                  <tr>
                     <td><strong>Residential Address :</strong></td>
                     <td> <?= Html::encode($committeMemberDetails[0]['residentialaddress1'])?> <?= Html::encode($committeMemberDetails[0]['residentialaddress2'])?>
                        <?= Html::encode($committeMemberDetails[0]['residencedistrict'])?> <?= Html::encode($committeMemberDetails[0]['residencestate'])?>
                        <?= Html::encode($committeMemberDetails[0]['residencepincode'])?>
                     </td>
                  </tr>
               </tbody>
            </table>
      </div>
      
      <!-- Dependants Section -->
      <div class="col-md-12 col-sm-8" id="MemberDependantsSection" style="display:none;">
         <table class="table defaulttab" cellspacing="0" cellpadding="0">
            <tbody>
               <tr>
                  <th colspan="2" bgcolor="#FFF4E6" class="text-center" style="color: #ff9800; font-size: 16px;">
                     <i class="glyphicon glyphicon-list"></i> Member's Dependants
                  </th>
               </tr>
               <tr>
                  <td colspan="2">
                     <div class="alert alert-info" style="margin-bottom: 0;">
                        <i class="glyphicon glyphicon-info-sign"></i>
                        <strong>Add Dependant to Committee:</strong> Select a dependant below to add them instead of the member.
                     </div>
                  </td>
               </tr>
            </tbody>
         </table>
         <div id="DependantsListDiv"></div>
      </div>
      
      <div class="col-md-12 col-sm-8" id="CommitteeAssignmentSection">
         <?php $form = ActiveForm::begin() ?>
         <?php echo Html::hiddenInput(
            'admin-save-committee-member-Url',
            \Yii::$app->params['ajaxUrl']['admin-save-committee-member-Url'],
            [
                'id'=>'admin-save-committee-member-Url'
            ]
            ); ?>
         <?php echo Html::hiddenInput('selected-dependant-id', '',
            [
                'id'=>'selected-dependant-id'
            ]
         ); ?>
         <?php echo Html::hiddenInput('is-dependant-selected', '0',
            [
                'id'=>'is-dependant-selected'
            ]
         ); ?>
      <table class="table defaulttab" cellspacing="0" cellpadding="0">
            <tbody>
               <tr>
                  <th colspan="2" bgcolor="#ADF7EE" class="text-center">
                     <span id="AssignmentTypeLabel">Assign Member To Committe</span>
                  </th>
               </tr>
               <tr>
                  <td><strong>Committee:</strong>&nbsp; <span style="color: red;">*</span></td>
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
                   <?= Html::button('Save', ['class' => 'btn btn-primary saveCommitteeMember', 'title' => Yii::t('yii', 'save'),
                        'data-institutionid' => $committeMemberDetails[0]['institutionid'], 'data-memberid'=>$committeMemberDetails[0]['memberid'], 
                        'data-userid'=>$committeMemberDetails[0]['userid'],'data-isspouse'=>$committeMemberDetails[0]['isspouse']]) ?>
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