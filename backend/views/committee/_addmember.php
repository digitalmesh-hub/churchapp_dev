<?php
   use backend\assets\AppAsset;
   use yii\helpers\Html;
   use yii\widgets\ActiveForm;
   ?>
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
      <div class="col-md-12 col-sm-8">
         <?php $form = ActiveForm::begin() ?>
         <?php echo Html::hiddenInput(
            'admin-save-committee-member-Url',
            \Yii::$app->params['ajaxUrl']['admin-save-committee-member-Url'],
            [
                'id'=>'admin-save-committee-member-Url'
            ]
            ); ?>
      <table class="table defaulttab" cellspacing="0" cellpadding="0">
            <tbody>
               <tr>
                  <th colspan="2" bgcolor="#ADF7EE" class="text-center">Assign Member To Committe</th>
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