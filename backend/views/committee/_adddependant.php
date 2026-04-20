<?php
   use backend\assets\AppAsset;
   use yii\helpers\Html;
   use yii\widgets\ActiveForm;
   ?>
<?php if($dependantDetails) { ?>
<div class="dependantprofile">
   <div class="text-right" style="margin-bottom: 10px;">
      <button type="button" class="btn btn-default btn-sm" id="clearDependantSearch">
         <i class="glyphicon glyphicon-arrow-left"></i> Search Again
      </button>
   </div>
   <script>
   $(document).ready(function() {
       $('#clearDependantSearch').click(function() {
           $('#CommitteeDependantDetailsDiv').html('');
           $('#dependant-search-name').val('');
           $('#dependant-search-name').focus();
       });
   });
   </script>
   <div class="alert alert-success" style="background-color: #d4edda; border-color: #c3e6cb; margin-bottom: 20px;">
      <h3 style="margin: 10px 0; color: #155724;">
         <i class="glyphicon glyphicon-user"></i> <strong>SELECTED DEPENDANT TO ADD</strong>
      </h3>
      <p style="font-size: 16px; margin: 5px 0; color: #155724;">
         <strong><?= Html::encode($dependantDetails[0]['title'])?> <?= Html::encode($dependantDetails[0]['dependantname'])?></strong>
         <span class="label label-info" style="font-size: 13px; margin-left: 10px;"><?= Html::encode($dependantDetails[0]['relation'])?></span>
      </p>
      <p style="margin: 5px 0; color: #155724;">
         Parent: <strong><?= Html::encode($dependantDetails[0]['parent_title'])?> <?= Html::encode($dependantDetails[0]['parent_firstname'])?> <?= Html::encode($dependantDetails[0]['parent_lastname'])?></strong>
         (Member #<?= Html::encode($dependantDetails[0]['parent_memberno'])?>)
      </p>
   </div>
   <div class="row">
      <div class="col-md-12 col-sm-8">
            <table class="table defaulttab table-bordered" cellspacing="0" cellpadding="0">
               <tbody>
                  <tr>
                     <th colspan="2" style="background-color: #5cb85c; color: white; font-size: 16px; padding: 12px;" class="text-center">
                        <i class="glyphicon glyphicon-user"></i> Dependant Information
                     </th>
                  </tr>
                  <tr>
                    <td colspan="2" align="center">
                      <?php if(!empty($dependantDetails[0]['memberimage'])){ ?>
                        <img class="memberimg"  style="width: 160px; border-radius: 25px; height: 150px;" src="<?php echo Yii::$app->params['imagePath'].$dependantDetails[0]['memberimage']?>">
                      <?php } else { ?>
                      <img class="memberimg"  style="width: 160px; border-radius: 25px; height: 150px;" src="/img/default-user.png">
                    <?php } ?> 
                  </td>
                  </tr>
                  <tr>
                     <td width="180"><strong>Dependant Name :</strong></td>
                     <td class="word-wrap">
                        <?= Html::encode($dependantDetails[0]['title'])?> 
                        <?= Html::encode($dependantDetails[0]['dependantname'])?>
                     </td>
                  </tr>
                  <tr>
                     <td><strong>Relation :</strong></td>
                     <td><?= Html::encode($dependantDetails[0]['relation'])?></td>
                  </tr>
                  <tr>
                     <td><strong>Parent Member :</strong></td>
                     <td class="word-wrap">
                        <?= Html::encode($dependantDetails[0]['parent_title'])?> 
                        <?= Html::encode($dependantDetails[0]['parent_firstname'])?> 
                        <?= Html::encode($dependantDetails[0]['parent_middlename'])?> 
                        <?= Html::encode($dependantDetails[0]['parent_lastname'])?>
                     </td>
                  </tr>
                  <tr>
                     <td><strong>Member Number :</strong></td>
                     <td><?= Html::encode($dependantDetails[0]['parent_memberno'])?></td>
                  </tr>
                  <?php if(!empty($dependantDetails[0]['dob'])) { ?>
                  <tr>
                     <td><strong>Date of Birth :</strong></td>
                     <td>
                        <?php echo date("d-m-Y", strtotimeNew($dependantDetails[0]['dob'])); ?>
                     </td>
                  </tr>
                  <?php } ?>
                  <?php if(!empty($dependantDetails[0]['dependantmobile'])) { ?>
                  <tr>
                     <td><strong>Mobile phone :</strong></td>
                     <td>
                        <label><?= Html::encode($dependantDetails[0]['dependantmobilecountrycode'])?> <?= Html::encode($dependantDetails[0]['dependantmobile'])?></label>
                     </td>
                  </tr>
                  <?php } ?>
                  <?php if(!empty($dependantDetails[0]['parent_email'])) { ?>
                  <tr>
                     <td><strong>Parent Email :</strong></td>
                     <td class="word-wrap">
                        <label><?= Html::encode($dependantDetails[0]['parent_email'])?></label>
                     </td>
                  </tr>
                  <?php } ?>
                  <?php if(!empty($dependantDetails[0]['parent_mobile'])) { ?>
                  <tr>
                     <td><strong>Parent Mobile :</strong></td>
                     <td>
                        <label><?= Html::encode($dependantDetails[0]['parent_mobile'])?></label>
                     </td>
                  </tr>
                  <?php } ?>
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
      <table class="table defaulttab table-bordered" cellspacing="0" cellpadding="0" style="border: 3px solid #f0ad4e;">
            <tbody>
               <tr>
                  <th colspan="2" style="background-color: #f0ad4e; color: white; font-size: 16px; padding: 12px;" class="text-center">
                     <i class="glyphicon glyphicon-plus-sign"></i> <strong>ASSIGN TO COMMITTEE</strong>
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
                             'id' => 'committeeTypeIdDep',
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
                             'id' => 'designationTypeDep',
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
                             'id' => 'periodTypeDep',
                             'prompt' => 'Please Select'
                           ]
                        )->label(false) ?>                                             
                  </td>
               </tr>
               <tr>
                <td colspan="2" class="text-center" style="padding: 15px;">
                   <?= Html::button('<i class="glyphicon glyphicon-ok"></i> Add Dependant to Committee', 
                      ['class' => 'btn btn-success btn-lg saveCommitteeDependant', 
                       'title' => Yii::t('yii', 'save'),
                       'style' => 'font-size: 16px; padding: 12px 30px;',
                       'data-institutionid' => $dependantDetails[0]['institutionid'], 
                       'data-memberid'=>$dependantDetails[0]['parent_memberid'], 
                       'data-userid'=>$dependantDetails[0]['userid'],
                       'data-dependantid'=>$dependantDetails[0]['dependantid'],
                       'data-isspouse'=>'d']) ?>
                 </td>
               </tr>
            </tbody>
         </table>
         <?php ActiveForm::end() ?>
         <div class="inlinerow Mtop10 text-center" id="errorDivDep" style="display: none">
            <div class="alert alert-danger" role="alert" id="errorMessageDivDep"><strong>Error!! Please fill in the fields above</strong></div>
         </div>
         <div class="inlinerow Mtop10 text-center" style="display: none" id="SuccessMessageDivDep">
            <div class="alert alert-success message" role="alert"></div>
         </div>
      </div>
   </div>
</div>
<?php } else { ?>
<div class="nocommitteemember">
   No Dependant Found  
</div>
<?php } ?>
