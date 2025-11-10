<?php 
use yii\helpers\Html;
use backend\assets\AppAsset;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;

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
		'delete-album',
		\Yii::$app->params['ajaxUrl']['delete-album'],
		[
				'id'=>'delete-album'
		]
		);


$this->title = 'Photo Albums';
?>

<!-- Header -->
<div class="col-md-12 col-sm-12 pageheader Mtop15"><?= $this->title ?></div>
<!-- Content -->
<div class="col-md-12 col-sm-12 contentbg">
    <div class="col-md-12 col-sm-12 Mtopbot20">
        <!-- Tab Panels -->
        <div class="blockrow">

            <!-- Nav tabs -->
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active"><a href="#albumlist" aria-controls="home" role="tab" data-toggle="tab">Photo Albums</a></li>
                <li role="presentation"><a href="#approval" aria-controls="profile" role="tab" data-toggle="tab">Photos Pending Approval <span>(<?= $pendingAlbumCount?>)</span></a></li>
                <?= Html::a('Create Album', ['create'], ['class' => 'btn btn-success pull-right','title' => Yii::t('yii', 'Create Album')])?>
            </ul>

            <!-- Tab panels -->
            <div class="tab-content">

                <!-- Member list -->
                <div role="tabpanel" class="tab-pane fade in active" id="albumlist">
              <?php $form = ActiveForm::begin(
			    		[
			                 'options'=>[
			                 'id' =>'form',

			                 ],
			    			'method' => 'get',
						]		
			    ); 
			    ?>
                    <!-- content -->
                    <div class="blockrow Mtop25">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-3 col-sm-4">
                                    <div class="inlinerow"><strong>Event Name</strong></div>
                                    <div class="inlinerow">
                                           <?= $form->field($searchModel, 'albumname')->textInput(['placeholder' => 'Search by event name'])->label(false); ?>
                                    </div>
                                </div>
                               <div class="col-md-3 col-sm-2">
                                    <div class="inlinerow"><strong>Event Start Date</strong></div>
                                    <div class="inlinerow">
                                        <?= DatePicker::widget([
                    											'name' => 'eventStartDate', 
                                          'class' => "form-control",
                    											'options' => ['placeholder' => 'Event Start Date'],
                                                            	'value' => (!empty($startDate))?$startDate:date('d-M-Y'),
                    											'pluginOptions' => [
                    												'format' => 'dd-M-yyyy',
                    												'autoclose'=>true,											
                  											]
                  										]);?>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-2">
                                    <div class="inlinerow"><strong>Event End Date</strong></div>
                                    <div class="inlinerow">
                                         <?= DatePicker::widget([
                  											'name' => 'eventEndDate', 
                                        'class' => "form-control",
                  											'options' => ['placeholder' => 'Event End Date'],
                                        'value' => (!empty($endDate))?$endDate:date('d-M-Y'),
                  											'pluginOptions' => [
                  												'format' => 'dd-M-yyyy',
                  												'autoclose'=>true,
                    											]
                    										]);?>
                                    </div>
                                </div>

                                <div class="col-md-2 col-sm-2">
                                      <?= Html::submitButton('Search', ['class' => 'btn btn-primary Mtop20','title' => Yii::t('yii', 'Search')]) ?>
                                </div>
                                <div class="col-md-2 col-sm-2 pull-right text-right"></div>
                            </div>
                        </div>
                    </div>
                     <?php ActiveForm::end(); ?>             
                    <!-- /. Blockrow closed -->
                    <!-- Employee Listing Table -->
                    <div class="blockrow Mtop20">
                        <?php 
                        if($imageModel){
                        foreach ($imageModel as $key => $value){ ?>
                            <div class="col-md-4 col-sm-4 col-xs-12">
                                <div class="albumbox">
                                    <div class="albumbanner">
                                        <div class="photocontain relative">
                                             <a class="example-image-link" href="<?= Yii::$app->params['imagePath'].$value['imageurl'];?>" data-lightbox="photos" data-title="Captions to be added here"><img src="<?= Yii::$app->params['imagePath'].$value['imageurl'];?>"/></a>
                                        </div>
                                    </div>
                                    <div class="albuminfo">
                                        <div class="albumdate">
                                            <?= date('jS F Y g:i A',strtotimeNew($value['activitydate']))?>
                                        </div>
                                        <div class="albuminfo-head">
                                            <?= $value['albumname'];?>
                                        </div>
                                        <div class="albumloc">
											                      <?= $value['venue'];?>
										                    </div>
                                    </div>
                                    <div class="albumcontrols">
                                        <div class="row">
                                            <div class="col-md-5 col-sm-6">
                                                <?= Html::a('View Album', ['album/view', 'id' => $value['albumid']], ['class' => 'btn btn-success btn-xs','title' => 'view album']) ?>
                                            </div>
                                      
                                            <div class="col-md-7 col-sm-6 text-right">
                             				<?php if($value['ispublished'] == 0) { ?>	
       				                    	<?= Html::button('Publish', [
				                                    'class' => 'btn btn-primary btn-xs publish', 
				                                    'id'    =>'btn-publish',
				                                    'title' => Yii::t('app', 'Publish'),
				                                    'album-id' => $value['albumid'],
				                                    'publish' =>$value['ispublished'],
				                                    'url' =>'album/publish',
                    							])?>
                                  	              
                                              <?php } else { ?> 
                                               <?= Html::button('Unpublish', [
				                                    'class' => 'btn btn-primary btn-xs unpublish', 
				                                    'id'    =>'btn-unpublish',
				                                    'title' => Yii::t('app', 'Unpublish'),
				                                    'album-id' => $value['albumid'],
				                                    'publish' =>$value['ispublished'],
				                                    'url' =>'album/unpublish',
                    							])?>
                    							<?php }?>
                                               <?= Html::button('<span class="glyphicon glyphicon-trash"></span>', ['class' => 'btn btn-danger btn-xs delete-album',
                   									 'id' =>'btn-album-delete','title' => Yii::t('yii', 'Delete'),
        				                    		'data-album-id' => $value['albumid']]) ?>
                                                <!-- <a href="" class="btn btn-danger btn-xs"><span class="glyphicon glyphicon-trash"></span></a> -->
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                      <?php } 
                        }else{
                        echo 'No results found';
                        }?>
                    </div>

                </div>
                <!-- /.Member list -->


 <!-- Approval List -->
                        <div role="tabpanel" class="tab-pane fade" id="approval">
                        <!-- Listing  -->
                            <div class="blockrow Mtop20">
                             <?php foreach ($tempAlbumModel as $key => $data){ ?>
                                <div class="col-md-4 col-sm-4 col-xs-12">                                 
                                    <div class="albumbox">
                                       <div class="albumbanner">
                                       <div class="photocontain relative">
                                            <a class="example-image-link" href="<?= Yii::$app->params['imagePath'].$data['coverimageurl'];?>" data-lightbox="photos" data-title="Captions to be added here"><img src="<?= Yii::$app->params['imagePath'].$data['coverimageurl'];?>"/></a>
                                        </div>
                                       </div>
                                       <div class="albuminfo">
                                           <div class="albumdate"><?= date('jS F Y g:i A',strtotimeNew($data['activitydate']));?></div> 
                                           <div class="albuminfo-head"><?= $data['albumname'];?></div>
                                       </div>
                                       <div class="albumcontrols">
                                           <div class="row">
                                               <div class="col-md-6 col-sm-6">  <?= Html::a('View Album', ['album/pending-album', 'id' => $data['albumid']], ['class' => 'btn btn-success btn-xs','title' => 'view album']) ?></div>
                                               <div class="col-md-6 col-sm-6 text-right">                                                   
                                                <div class="pendingcount">Pending <?= ($pendingImageCount[$data['albumid']])?></div> 
                                               </div>
                                           </div>                                         
                                           
                                       </div>
                                    </div>                                    
                                </div>
                                <?php } ?>
                            </div>
                            <!-- /. Listing -->
                        
                        </div>
                        <!-- /. Approval List -->
            </div>
        </div>
        <!-- /.Tab Panels -->
        <!-- Section head -->
        <!--<div class="pagesubhead">Employee Listing</div>-->
    </div>
</div>