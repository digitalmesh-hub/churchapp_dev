<?php 
   use yii\helpers\Html;
   if($committeeMemberDetails){ 
      $typeId = array();
      foreach($committeeGroupList as $group) {
         $designationId = 0; 
         foreach($committeeMemberDetails as $model) { 
            if ($group['committeegroupid'] == $model['committeegroupid']) {    
           if (!in_array($model['committeegroupid'], $typeId)) { ?>
      <div class="col-md-6 col-sm-6 Mtop20">
         <div class="commtypebox">
      <?php if($group['committeegroupid'] == $model['committeegroupid'] && $typeId != $model['committeegroupid']) { ?>
      <div class="committeehead">
         <?= Html::encode($model['committeegroupdescription']) ?>
      </div>
      <?php } ?>
      <?php
         $typeId[] = $model['committeegroupid'];
         }?>
      <table cellpadding="0" cellspacing="0" class="table defaulttab">
         <tbody>
            <?php if($designationId != $model['designationid']) { ?>
            <tr>
               <td colspan="4" class="commlist-head"><strong><?= Html::encode($model['description']) ?></strong></td>
            </tr>
            <?php } ?>
            <tr>
               <td width="15%">
                  <?php if ($model['memberimage'] != '') { ?>
                  <img class="img-rounded committeeimg" src="<?php echo yii::$app->params['imagePath'].$model['memberimage'] ?>">
                  <?php } else { ?>
                  <img class="img-rounded committeeimg" src="/img/default-user.png">
                  <?php } ?>
               </td>
               <td width="15%"><strong>Name:</strong></td>
               <td width="60%"><?= Html::encode($model['title']) ?><span class="capitalize">
                  <?= Html::encode($model['membername']) ?></span>
               </td>
               <td width="10%">
                  <?php if($model['active']) { ?>
                  <?= Html::button('Delete', ['class' => 'btn btn-danger btn-sm delete-committee-member', 'title' => Yii::t('yii', 'Delete'), 'data-committeeid' => $model['committeeid']]) ?>
               </td>
               <?php } else { ?>
               <?= Html::button('Delete', ['class' => 'btn btn-primary btn-sm delete-committee-member', 'title' => Yii::t('yii', 'Delete'), 'data-committeeid' => $model['committeeid']]) ?>
               </td>
               <?php } ?>
            </tr>
            <?php 
               $designationId = $model['designationid'];
               $typeId[] = (int)$model['committeegroupid'];
               ?>
         </tbody>
      </table>
      <?php } }?>
   </div>
</div>
<?php } } else { ?>
<div>
   <label style="vertical-align: middle; padding-left: 40%; margin-top: 20px">No Record found</label>
</div>
<?php } ?>