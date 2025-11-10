 <?php
 use yii\helpers\Html;
 use common\models\extendedmodels\ExtendedFeedback;
 
 ?>
<?php 
    $memberData = ExtendedFeedback::getRequestData($model->feedbackid);
    $feedBackImages = ($model->feedbackimagedetails);
    if(isset($memberData)) {
    ?>
        <div class="panel panel-default">
            <div class="panel-heading" role="tab" id="headingOne">
                <h4 class="panel-title">
                    <a role="button" data-toggle="collapse" data-parent="#accordion" href="#<?= $model->feedbackid ?>" aria-expanded="false" aria-controls="<?= $model->feedbackid ?>" class="collapsed">
                        <div id="feedbacktype_<?= $model->feedbackid ?>"><?= $memberData['feedbacktype'] ?? ''; ?></div>   
                        <div class="feedbacksub" id="description_<?= $model->feedbackid ?>"><?= $model->description ?></div>
                    </a>
                    <div class="feedbackname">
                   
                    <?= Html::encode(($memberData['title'] ?? '') . ' ' . ($memberData['username'] ?? '')) ?>
                    </div>
                    <div class="feedback-contact"><strong>Contact : </strong> <?= Html::encode(($memberData['phone'] ?? '')) ?> </div>
                    <div class="feedback-contact"> <strong>Email : </strong> <?= Html::encode(($memberData['email'] ?? ''))?> </div>
                    <div class="ratingbox Mtop10">
                        <div class="s<?=$memberData['feedbackrating'] ?? '' ?>"></div>
                    </div> 
                    <div class="feedback-date text-right"><?= Html::encode(date(yii::$app->params['dateFormat']['viewDandTFormat'],strtotimeNew(($memberData['createddatetime'] ?? '')))) ?></div>
                    <!-- Gallery -->
                    <?php if (!empty($feedBackImages)) { ?>
                    <div class="row w100p flexdiv Mtop10"> 
                       <?php foreach ($feedBackImages as $key => $images) { ?>
                        
                            <div class="col-md-2 col-sm-3 flexdiv">
                                <a class="example-image-link" href="<?=yii::$app->params['imagePath'].$images->feedbackimage?>" data-lightbox="feedbackimage" data-title="">
                                    <?= Html::img(yii::$app->params['imagePath'].$images->feedbackimage, ['alt' => '','class' => 'feedbackimage' ]) ?>
                            </a>
                        </div>         
                        
                   <?php }?></div> <?php } ?>
                    <!-- /.Gsllery -->
                </h4>
            </div>
            <div id="<?= $model->feedbackid ?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne" aria-expanded="false" style="height: 0px;">
                <div class="panel-body">
                    <div class="inlinerow prewrap" id="description_<?= $model->feedbackid ?>"><?= $model->description ?></div>
                    <div class="inlinerow Mtop20"><strong>Reply Here<span style="color: red;">*</span></strong></div>
                    <div class="inlinerow Mtop10">
                        <textarea rows="3" class="form-control" id="emailcontent_<?= $model->feedbackid ?>"></textarea>
                        <div hidden="hidden" class="alert alert-danger text-center" id="ErrorMessageLabel" role="alert"><strong>Message Body Cannot be blank</strong></div>
                    </div>
                    <div class="inlinerow Mtop20"><strong>Email Addresses</strong></div>
                    <div class="inlinerow Mtop10">
                        <textarea rows="2" class="emailcontent form-control" id="email_<?= $model->feedbackid ?>"><?php echo ($memberData['email'] ?? '') ?></textarea>
                         <div hidden="hidden" class="alert alert-danger text-center" id="ErrorEmailLabel" role="alert"><strong>Email Id Cannot be blank</strong></div>
                        <div hidden="hidden" class="alert alert-danger text-center" id="ErrorEmailvalid" role="alert"><strong>Email Id not valid</strong></div>
                    </div>
                    <div class="inlinerow Mtop5 nodisplay" id="emai lerror_<?= $model->feedbackid ?>">
                        <div class="alert alert-danger text-center" role="alert"><strong>Error! Please check the fields again</strong></div>
                    </div>
                    <div class="inlinerow Mtop5 nodisplay" id="emailerror_<?= $model->feedbackid ?>">
                        <div class="alert alert-danger text-center" role="alert"><strong>Error! Please check the fields again</strong></div>
                    </div>
                    <div class="inlinerow Mtop20 text-center">   
                        <input class="btn btn-success feedbackrespondbtn" id="RespondButton" value="Respond" feedbackid="<?= $model->feedbackid ?>" userid="<?= $model->userid ?>" type="button" url="feedback/feedbackmail/" reload="feedback/index/"> 
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>

   