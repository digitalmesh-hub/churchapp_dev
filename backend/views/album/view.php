<?php
use yii\widgets\ActiveForm;
use backend\assets\AppAsset;
use yii\helpers\Html;
use common\models\extendedmodels\ExtendedEvent;

$assetName = AppAsset::register($this);

$this->registerJsFile(
		$assetName->baseUrl . '/theme/js/Remember.album.ui.js',
		[
				'depends' => [
						AppAsset::className()
				]
		]
		);
echo Html::hiddenInput(
		'delete-image',
		\Yii::$app->params['ajaxUrl']['delete-image'],
		[
				'id'=>'delete-image'
		]
		);
echo Html::hiddenInput(
		'change-caption',
		\Yii::$app->params['ajaxUrl']['change-caption'],
		[
				'id'=>'change-caption'
		]
		);
$this->title = 'View Album';
?>
          <div class="col-md-12 col-sm-12 pageheader Mtop15"><?= $this->title ?></div>
          <!-- Content -->
          <div class="col-md-12 col-sm-12 contentbg">
               <div class="col-md-12 col-sm-12 Mtopbot20">
                   <div class="row">
                       <div class="col-md-6 col-sm-6 col-xs-12">
                           <!-- Album info -->
                           <div class="albuminfo">
                               <div class="albumdate"><?= date('jS F Y g:i A',strtotimeNew($model->event->activitydate))?></div> 
                               <div class="albuminfo-head"><?= html::encode($model->albumname)?></div>
                               <div class="albumloc"><?= ($model->eventid) ? (($model->event->venue) ? html::encode($model->event->venue) :'' ): ''?></div>
                           </div>
                           <!-- /.Album info --> 
                       </div> 
                       <div class="col-md-6 col-sm-6 col-xs-12 text-right">
                           
                           
                           <div class="inlinerow Mtop15">
                           <!-- Controls -->
                           <a href="index" class="btn btn-default"><span class="glyphicon glyphicon-chevron-left"></span>&nbsp; Back</a>
                           
                           <?php if($model->ispublished == 0) { ?>	
       				                    	<?= Html::button('Publish', [
				                                    'class' => 'btn btn-primary publish', 
				                                    'id'    =>'btn-publish',
				                                    'title' => Yii::t('app', 'Publish'),
				                                    'album-id' => $model->albumid,
				                                    'publish' =>$model->ispublished,
				                                    'url' =>'album/publish',
                    							])?>
                                  	              
                                              <?php } else { ?> 
                                               <?= Html::button('Unpublish', [
				                                    'class' => 'btn btn-primary unpublish', 
				                                    'id'    =>'btn-unpublish',
				                                    'title' => Yii::t('app', 'Unpublish'),
				                                    'album-id' => $model->albumid,
				                                    'publish' =>$model->ispublished,
				                                    'url' =>'album/unpublish',
                    							])?>
                    							<?php }?>
                           
                           <!-- /.Controls -->
                           </div>
                       </div>
                  <!-- Photos -->
                   <div class="inlinerow albumseparate">
                   
                       <div class="col-md-3 col-sm-4 col-xs-6">
                           <a href="" data-toggle="modal" data-target="#addnew">
                               <div class="photobox">
                                <a href="" data-toggle="modal" data-target="#photos"><div class="addphoto"></div></a>
                           </div>
                           </a>
                       </div>
                       	         
                       <?php foreach ($imageModel as $key => $value){?>
                       
                        <?php $form = ActiveForm::begin(
    		
					    		[
					    			'action' => ['album/change-caption'],
					                 'options'=>[
					                // 'id' =>'form1',
					                 ],
								]		
					    				
					    ); 
					  
					    ?>
                       <div class="col-md-3 col-sm-4 col-xs-6">
                           <div class="photobox-reg">
                              <div class="photocontain relative">
                                   <a class="example-image-link" href="<?= Yii::$app->params['imagePath'].$value->imageurl;?>" data-lightbox="photos" data-title="Captions to be added here"><img src="<?= Yii::$app->params['imagePath'].$value->imageurl;?>"/></a>
                              </div>
                              <div class="photocontrols">
                                  <div class="phototitle"><?= $value->caption;?></div>
                                  <div class="inlinerow">  
                                  <?php if ($value->iscover == 1) {?>                                    
                                       <?= Html::button('<span class="glyphicon glyphicon-trash"></span>', ['class' => 'btn btn-danger btn-xs delete-image',
		                   									 'id' =>'btn-image-delete','title' => Yii::t('yii', 'Delete'),
		        				                    		'data-image-id' => $value->id,'disabled' => true]) ?>
                                      
                                      	<?= Html::tag('p', Html::encode("Make Album Cover"), ['class' => 'btn btn-success btn-xs','disabled' => true]) ?>
                                      <?php } else {?> 
                                      <?= Html::button('<span class="glyphicon glyphicon-trash"></span>', ['class' => 'btn btn-danger btn-xs delete-image',
		                   									 'id' =>'btn-image-delete','title' => Yii::t('yii', 'Delete'),
		        				                    		'data-image-id' => $value->id]) ?>
                                      <?= Html::a('Make Album Cover', ['album/make-album-cover', 'id' => $value->id], ['class' => 'btn btn-success btn-xs']) ?>
                                      <?php }?>
                                      
                                     
                                      <a href="" class="pull-right pencil" data-toggle="modal" data-target="#caption_<?= $value->id?>"></a>
                                      
                                  </div>
                              </div>
                           </div>
                       </div>
                      
                       <!-- photos end -->
                       <!-- Modal -->
			    <div class="modal fade imgcaption" id="caption_<?= $value->id?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
			      <div class="modal-dialog" role="document">
			        <div class="modal-content">
			          <div class="modal-header">
			            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			            <h4 class="modal-title" id="myModalLabel">Edit Caption</h4>
			          </div>
		
			          <div class="modal-body" id="imgcaption"> 
            				<div><?= $form->field($imageModelNew, 'caption')->textInput(['value' => $value->caption,'id' => 'captiontext'])->label(false) ?></div>
			          </div>
			          <div class="modal-footer" style="text-align:center;">
			            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			                 <?= Html::activeHiddenInput($imageModelNew, "id", ['id' => "imageId",'value' => $value->id])?>
			                <?= Html::activeHiddenInput($imageModelNew, "albumid", ['id' => "albumId",'value' => $value->albumid])?>
			                 <?= Html::submitButton('Save', ['class' => 'btn btn-primary caption',
		                   									 'id' =>'btn-caption-change',
			                 								'title' => Yii::t('yii', 'Save'),
			                 ]) ?>
			                  <?php ActiveForm::end(); ?>
			          </div>
			        </div>
			      </div>
			    </div>
			     <?php }?>
			      
			     </div>
     <!-- Modal -->
    
      <div class="modal fade" id="photos" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
        <?php $form = ActiveForm::begin(
    		
    		[
                 'options'=>[
                 'enctype' => 'multipart/form-data',
                 'id' =>'form',
                 ],
			]		
    				
    ); 
  
    ?>
  
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="myModalLabel">Add Photos</h4>
          </div>
          <div class="modal-body">
            <div><strong>Photo Location</strong></div>
            <div><?= $form->field($imageModelNew, 'imageurl')->fileInput()->label(false) ?></div>
            <br/>
            <div><strong>Caption</strong></div>
            <div><?= $form->field($imageModelNew, 'caption')->textInput()->label(false) ?></div>
             
          </div>
          <div class="modal-footer" style="text-align:center;">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <?= Html::activeHiddenInput($model, "eventid", ['id' => "eventId",'value'=>""])?>
           <?= Html::submitButton('Save', ['class' => 'btn btn-primary btnsave']) ?>
          </div>
          <?php ActiveForm::end(); ?>
        </div>
      		</div>
    	  </div>
      </div>
   </div>
</div>
      
     
     
     
     
     