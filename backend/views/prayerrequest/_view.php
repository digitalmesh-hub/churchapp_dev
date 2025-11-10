<?php
   use yii\helpers\Html;
   use common\models\extendedmodels\ExtendedPrayerrequest;
   
   ?>
<?php 
   $memberData = ExtendedPrayerrequest::getRequestData($model->prayerrequestid);
   if(isset($memberData)) {
   ?>
<div class="panel panel-default">
   <div class="panel-heading" role="tab" id="headingOne">
      <h4 class="panel-title">
         <a role="button" data-toggle="collapse" data-parent="#accordion" href="#<?=$model->prayerrequestid; ?>" aria-expanded="false" aria-controls="3" class="">
            <div id="subject3" class=""><?=$model->subject; ?></div>
         </a>
         <div class="feedbackname"><?= $memberData['title'].' '.$memberData['username']; ?></div>
         <div class="feedback-contact"><strong>Contact : </strong><?=$memberData['phone']; ?></div>
        <div class="feedback-contact"> <strong>Email : </strong><?=$memberData['email']; ?> </div>
         
         <div class="feedback-contact"><strong><?= date_format(date_create(
            $model->createdtime),Yii::$app->params['dateFormat']['viewDateFormatandT24hr'])?></strong></div>
      </h4>
   </div>
   <div id="<?=$model->prayerrequestid; ?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne" aria-expanded="false" style="height: 0px;">
      <div class="panel-body">
         <div class="inlinerow"><?= $model->description?></div>
         <div class="inlinerow Mtop20"><strong>Reply Here</strong>&nbsp;<span class="manditory">*</span></div>
         <div class="inlinerow Mtop10">
            <textarea id="reply_<?=$model->prayerrequestid?>" rows="3" value="" class="form-control reply "></textarea>
            <div hidden="hidden" class="alert alert-danger text-center" id="ErrorMessageLabel" role="alert"><strong>Message Body Cannot be blank</strong></div>
         </div>
         <div class="inlinerow Mtop20"><strong>Email Addresses</strong>&nbsp;<span class="manditory">*</span></div>
         <div class="inlinerow Mtop10">
            <textarea id="emailid_<?=$model->prayerrequestid?>" rows="2" value="" class="form-control"><?=$memberData['email']; ?></textarea>
            <div hidden="hidden" class="alert alert-danger text-center" id="ErrorEmailLabel" role="alert"><strong>Email Id Cannot be blank</strong></div>
            <div hidden="hidden" class="alert alert-danger text-center" id="ErrorEmailvalid" role="alert"><strong>Email Id not valid</strong></div>
         </div>
         <div class="inlinerow Mtop5 nodisplay" id="emailerror_3">
            <div class="alert alert-danger text-center" role="alert"><strong>Error! Please check the fields again</strong></div>
         </div>
         <input type="hidden" id="tempmaildata" value=""/>
         <input type="hidden" id="tempmailid" value=""/>
         <div class="inlinerow Mtop20 text-center">
            <input id="prayermailid" url="prayerrequest/prayerrequestmail/" prid="<?=$model->prayerrequestid;?>" class="btn btn-success prayermailidclass" reload="prayerrequest/index/" value="Respond" userid="<?= $model->userid ?>"" type="button">
         </div>
      </div>
   </div>
   <?php } ?>
</div>