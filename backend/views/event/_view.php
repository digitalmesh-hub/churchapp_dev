<?php
   use yii\helpers\Html;
   use yii\helpers\Url;
   use common\models\extendedmodels\ExtendedRsvpdetails;
   use backend\assets\AppAsset;
   
   $assetName = AppAsset::register($this);
   
   $this->registerJsFile(
      $assetName->baseUrl . '/theme/js/Remember.Event.ui.js',
      [
          'depends' => [
                  AppAsset::className()
          ]
      ]
   );
   echo Html::hiddenInput(
          'event-delete-url',
          \Yii::$app->params['ajaxUrl']['event-delete-url'],
          [
                  'id'=>'event-delete-url'
          ]
   );
   ?>
<tr>
   <td><?= Html::encode(($model->notehead)? $model->notehead : '--')?></td>
   <td><?= ($model->venue) ? Html::encode($model->venue) : '--'?> </td>
   <td><?= Html::encode(date("d-M-Y H:i",strtotimeNew($model->activitydate)))?> </td>
   <td><?= Html::encode(date("d-M-Y",strtotimeNew($model->activatedon)))?> </td>
   <td><?= Html::encode( ($model->expirydate) ? date("d-M-Y",strtotimeNew($model->expirydate)) : '--')?> </td>
   <td><?= Html::encode(date("d-M-Y H:i",strtotimeNew($model->createddate)))?> </td>
   <td><?= Html::encode(($model->iseventpublishable && $model->publishedon ) ? date("d-M-Y H:i",strtotimeNew($model->publishedon))  : "--")?> </td>
   <td class="text-center">
      <a href ='update/<?php echo $model->id?>'  class="btn btn-success btn-sm actionbtns manage" title="Manage" event-id= '$model->id'>Manage</a>
      <?php if (!$model->iseventpublishable) { ?>
       <a id='btn-publish' class="btn btn-primary btn-sm publish"   url="event/publish"  event-id=<?php echo $model->id?> publish=<?php echo $model->iseventpublishable?>  class="btn btn-primary btn-sm publish" title= "Publish" event-title='<?php echo $model->notehead?>' activity-date='<?php echo $model->activitydate?>' activity-on='<?php echo $model->activatedon?>'  familyUnitId='<?php echo $model->familyunitid?>' venue='<?php echo $model->venue?>'
         >Publish</a>
     <?php } else { ?>
         <?= Html::tag('p', "Publish", ['class' => 'btn btn-primary btn-sm publish isDisabled']) ?>
     <?php } ?>          
      <?= Html::button('Delete' ,[ 'class' => 'btn btn-danger btn-sm actionbtns delete', 'id' => 'btn-event-delete', 'title' => Yii::t('yii', 'Delete'),'event-id' => $model->id
         ]
         );?>
    <?php if ($model->rsvpavailable) { ?>
       <a href ='rsvp-listing/<?php echo $model->id?>' rsvpavailable =<?php echo $model->rsvpavailable ?>  id='rsvplist' class='rsvplisting btn btn-success btn-sm' title="RSVP Listing">RSVP Listing</a>
    <?php } else { ?>
        <?= Html::tag('p', "RSVP Listing", ['class' => 'rsvplisting btn btn-success btn-sm isDisabled']) ?>
   <?php } ?>
   </td>
</tr>
<?php  $eventModel = new ExtendedRsvpdetails();
   $eventdetails = $eventModel->getRsvpCountDetails($model->id); 
   $sum = $eventdetails[0]['membercount'] + $eventdetails[0]['childrencount'] + $eventdetails[0]['guestcount'] ?>
<tr>
   <td colspan="10" class="rsvpbkgrnd"><strong>No. of confirmed attendees:</strong>  <strong>Members – <?php echo ($eventdetails[0]['membercount']) ? $eventdetails[0]['membercount'] : 0; ?></strong>,  <strong>Children – <?php echo ($eventdetails[0]['childrencount']) ? $eventdetails[0]['childrencount'] : 0; ?></strong>,  <strong>Guest –<?php echo ($eventdetails[0]['guestcount']) ? $eventdetails[0]['guestcount'] : 0; ?></strong>.  <strong>Total : <?php echo ($sum) ? $sum : 0; ?></strong></td>
</tr>