<?php 
use yii\helpers\Html;
?>
<table class="table table-bordered" id="tablecommitteetype" cellspacing="0" cellpadding="0">
   <tbody id="committeetypebody">
      <tr>
         <th width="75%">Committee Period</th>
         <th class="text-center" width="25%">Actions</th>
      </tr>
      <?php 
         if($periodResult){
             $descriptionList = array();
             foreach($periodResult as $model){ 
                 $fromDate = date_format(date_create($model['period_from']),Yii::$app->params['dateFormat']['viewDateFormat']);
                 $toDate = date_format(date_create($model['period_to']),Yii::$app->params['dateFormat']['viewDateFormat']);
                 if(!in_array($model['description'], $descriptionList)) { ?>
      <tr>
         <td colspan="2" class="commperiod"><?= Html::encode($model['description'])?></td>
      </tr>
      <?php
         array_push($descriptionList, $model['description']);
         } ?>
      <tr>
         <td><?= Html::encode($fromDate)?> - <?= Html::encode($toDate)?></td>
         <td class="text-center">
            <?= Html::button('Edit',
               ['class' => 'btn btn-primary edit-period', 'data-startDate'=> $fromDate, 
               'data-endDate'=>$toDate,
               'data-committe-type'=> $model['committeegroupid'], 'data-period_id' =>$model['committee_period_id'],
               'title' => Yii::t('yii', 'Edit')
               ]
               ); ?>
            <?php 
               if($model['active'] == 1){ ?>
            <?= Html::button('Deactivate',['class' => 'btn btn-danger btn-active', 'title' => Yii::t('yii', 'Deactivate'),'data-committee_period_id' => 
               $model['committee_period_id'],
               'data-active' => $model['active'],
               'style' => 'width:50%']); ?>
            <?php }
               else{ ?>
            <?= Html::button('Activate',['class' => 'btn btn-success btn-active', 'title' => Yii::t('yii', 'Activate'),
               'data-committee_period_id' => 
               $model['committee_period_id'],
               'data-active' => $model['active'],
               'style' => 'width:50%;']
               );?>
            <?php } ?>      
         </td>
      </tr>
      <?php } 
         }
         else{?>
      <tr>
         <td colspan="2" class="text-center"><?= Html::encode('No Records')?></td>
      </tr>
      <?php } ?> 
   </tbody>
</table>