<?php 
use yii\widgets\ActiveForm;
use backend\assets\AppAsset;
use yii\helpers\Html;
use common\models\extendedmodels\ExtendedEvent;
use backend\components\widgets\FlashResult;

$assetName = AppAsset::register($this);

$this->registerJsFile(
		$assetName->baseUrl . '/theme/js/Remember.album.ui.js',
		[
				'depends' => [
						AppAsset::className()
				]
		]
		);
$this->title = 'Create Album';

?>
<!-- Header -->
<div class="col-md-12 col-sm-12 pageheader Mtop15"><?= $this->title ?></div>
<!-- Content -->
<div class="col-md-12 col-sm-12 contentbg">
               <div class="col-md-12 col-sm-12 Mtopbot20">  
               <?= FlashResult::widget(); ?>         
                   <div class="row Mtop10">
                      <div class="col-md-4 col-sm-4 col-xs-12">
                           <div class="inlinerow"><strong>Event Name</strong></div>                        
                           <div class="inlinerow Mtop10">                
			<?= Html::dropDownList('event_id', null, $eventsArray, [
                                            'prompt'=>'Please Select',
                                            'id' => 'Albumlist',
                                            'class' => "form-control"
                                            ]) ?>                         
                             </div>
               		</div>
                      <div class="col-md-6 col-sm-6 col-xs-12 pull-right text-right">
                          <?= Html::a('Back to Albums', ['album/index'], ['class' => 'btn btn-primary','title' => 'Back to albums']) ?>
                      </div>
            
                   </div>
                   <!-- Photos -->
                   <div class="inlinerow albumseparate Mtop15">
          
                       <div class="col-md-3 col-sm-4 col-xs-6">
                           <div class="photobox">
                                <a href="" data-toggle="modal" data-target="#photos"><div class="addphoto"></div></a>
                           </div>
                       </div>
                   </div>
                   <!-- /.Photos -->
               </div>
          </div>
     </div>
</div>
<!-- Contents closed -->

    <!-- Modal -->
    
      <div class="modal fade" id="photos" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
        <?php $form = ActiveForm::begin(
    		[
            'options'=>[
            'enctype' => 'multipart/form-data',
            'id' =>'form'],
			  ]					
    ); ?>
  
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="myModalLabel">Add Photos</h4>
          </div>
          <div class="modal-body">
            <div><strong>Photo Location</strong></div>
            <div><?= $form->field($imageModel, 'imageurl')->fileInput()->label(false) ?></div>
            <br/>
            <div><strong>Caption</strong></div>
            <div><?= $form->field($imageModel, 'caption')->textInput()->label(false) ?></div> 
          </div>
          <div class="modal-footer" style="text-align:center;">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <?= Html::activeHiddenInput($model, "eventid", ['id' => "eventId"])?>
           <!-- <input type="hidden" id="eventId" value=""> -->
           <?= Html::submitButton('Save', ['class' => 'btn btn-primary btnsave']) ?>
          </div>
          <?php ActiveForm::end(); ?>
        </div>
      </div>
      </div>

