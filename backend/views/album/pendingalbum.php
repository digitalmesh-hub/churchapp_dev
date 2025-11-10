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
   /* echo Html::hiddenInput(
      'change-caption-of-pending',
      \Yii::$app->params['ajaxUrl']['change-caption-of-pending'],
      [
          'id'=>'change-caption-of-pending'
      ]
      ); */
   echo Html::hiddenInput(
      'approve-album',
      \Yii::$app->params['ajaxUrl']['approve-album'],
      [
          'id'=>'approve-album'
      ]
      );
   
   $this->title = 'Pending Approval';
   ?>
<!-- Contents -->
<div class="container">
   <div class="row">
      <!-- Header -->
      <div class="col-md-12 col-sm-12 pageheader Mtop15"><?= $this->title ?></div>
      <!-- Content -->
      <div class="col-md-12 col-sm-12 contentbg">
         <div class="col-md-12 col-sm-12 Mtopbot20">
            <div class="row">
               <div class="col-md-6 col-sm-6 col-xs-12">
                  <!-- Album info -->
                  <div class="albuminfo">
                     <div class="albumdate"><?= date('jS F Y g:i A',strtotimeNew($tempAlbumData['activitydate']))?></div>
                     <div class="albuminfo-head"><?= $tempAlbumData['albumname']?></div>
                  </div>
                  <!-- /.Album info --> 
               </div>
               <div class="col-md-6 col-sm-6 col-xs-12 text-right">
                  <div class="inlinerow">
                     <?= Html::a('Back', ['album/index'], ['class' => 'btn btn-default glyphicon glyphicon-chevron-left','title' => 'Back']) ?>
                     <input type="button" class="btn btn-primary btn-add-pending"  value="Add to Album"/>       
                  </div>
               </div>
            </div>
            <!-- Photos -->
            <div class="inlinerow albumseparate">
               <?php foreach ($model as $key => $value) { ?> 
               <div class="col-md-3 col-sm-4 col-xs-6">
                  <div class="photobox">
                     <div class="photocontain relative">
                        <a class="example-image-link" href="<?= Yii::$app->params['imagePath'].$value['pending_imageurl'];?>" data-lightbox="photos" data-title="Captions to be added here"><img src="<?= Yii::$app->params['imagePath'].$value['pending_imageurl'];?>"/></a>
                     </div>
                     <div class="photocontrols">
                        <div class="uploadby">Uploaded by : <?= $value['name'];?></div>
                        <div class="phototitle"><?= $value['caption']?></div>
                        <div class="inlinerow">
                           <div class="checkbox">
                              <label>
                              <input type="checkbox" id="approve" value="<?= $value['id']?>"> Approve
                              </label>
                              <a href="#" class="pull-right pencil" data-toggle="modal" data-target="#caption_<?= $value['id']?>"></a> 
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <?php $form = ActiveForm::begin(
                  [
                    'action' => ['album/change-caption-of-pending'],
                             'options'=>[
                             'id' =>'form2',
                             ],
                  ]   
                      
                  ); 
                  ?>
               <!-- Modal -->
               <div class="modal fade" id="caption_<?= $value['id']?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                  <div class="modal-dialog" role="document">
                     <div class="modal-content">
                        <div class="modal-header">
                           <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                           <h4 class="modal-title" id="myModalLabel">Edit Caption</h4>
                        </div>
                        <div class="modal-body">
                           <div><?= $form->field($imageModelNew, 'caption')->textInput(['value' => $value['caption'],'id' => 'captiontext'])->label(false) ?></div>
                        </div>
                        <div class="modal-footer" style="text-align:center;">
                           <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                           <?= Html::activeHiddenInput($imageModelNew, "id", ['id' => "imageId",'value' => $value['pending_imageid']])?>
                           <?= Html::activeHiddenInput($imageModelNew, "albumid", ['id' => "albumId",'value' => $value['albumid']])?>
                           <?= Html::submitButton('Save', ['class' => 'btn btn-primary caption',
                              'id' =>'btn-caption-change',
                              'title' => Yii::t('yii', 'Save'),
                              
                              
                              
                              ]) ?>
                           <?php ActiveForm::end(); ?>
                        </div>
                     </div>
                  </div>
               </div>
               <!-- Modal -->
               <div class="modal fade" id="approval" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                  <div class="modal-dialog" role="document">
                     <div class="modal-content">
                        <div class="modal-header">
                           <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                           <h4 class="modal-title" id="myModalLabel">Approve Photos</h4>
                        </div>
                        <div class="modal-body text-center">
                           <p id="approvepara"> </p>
                        </div>
                        <div class="modal-footer" style="text-align:center;">
                           <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
                           <button type="button" id="approvealbum" class="btn btn-primary">Yes</button>            
                        </div>
                     </div>
                  </div>
               </div>
               <?php }?>
            </div>
            <!-- /.Photos -->
         </div>
      </div>
   </div>
</div>
<!-- Contents closed -->