<?php
   use yii\helpers\Html;
   use backend\assets\AppAsset;
   use yii\widgets\ActiveForm;
   use kartik\date\DatePicker;
   use yii\helpers\ArrayHelper;
   use yii\helpers\Url;
   
   $assetName = AppAsset::register($this);
   
   
   /* @var $this yii\web\View */
   /* @var $searchModel common\models\extendedmodels\ExtendedOrdersSearch */
   /* @var $dataProvider yii\data\ActiveDataProvider */
   
   $this->title = 'Food Bookings';
   ?>
<div class="col-md-12 col-sm-12 pageheader Mtop15"><?= $this->title ?></div>
<!-- Contents -->
<div class="container">
<div class="row">
   <!-- Content -->
   <div class="col-md-12 col-sm-12 contentbg">
      <div class="col-md-12 col-sm-12 Mtopbot20">
         <!-- Sponsor listing -->
         <fieldset class="Mtop20">
            <legend style="font-size: 20px;">Food Bookings</legend>
            <!-- Filter -->
            <div class="inlinerow Mtop20">
               <?php $form = ActiveForm::begin(
                  [
                    'action' => ['orders/index'],
                             'options'=>[
                             'id' =>'form',
                  
                             ],
                    'method' => 'get',
                  ]   
                  ); 
                  
                  ?>
               <div class="col-md-3 col-sm-3">
                  <div class="inlinerow"><strong>Start Date</strong></div>
                  <div class="inlinerow">
                     <?=$form->field($searchModel, 'start_date')->widget(DatePicker::classname(), [
                        'options' => [
                            'placeholder' => 'Start Date',
                        ],
                        'pluginOptions' => [
                            'format' => 'dd-M-yyyy',
                            'autoclose' => true,
                            'todayHighlight' => true,
                            'endDate' => "0d"
                        ]
                        ])->label(false); ?>
                  </div>
               </div>
               <div class="col-md-3 col-sm-3">
                  <div class="inlinerow"><strong>End Date</strong></div>
                  <div class="inlinerow">
                     <?=$form->field($searchModel, 'end_date')->widget(DatePicker::classname(), [
                        'options' => [
                            'placeholder' => 'End Date',
                        ],
                        'pluginOptions' => [
                        'format' => 'dd-M-yyyy',
                        'autoclose' => true,
                        'endDate' => "+7d",
                        'todayHighlight' => true
                        ]
                        ])->label(false); ?>
                  </div>
               </div>
               <div class="col-md-3 col-sm-3">
                  <div class="inlinerow"><strong>Status</strong></div>
                  <div class="inlinerow">
                     <?= $form->field($searchModel, 'orderstatus')->dropDownList(
                        $orderStatusArray,[
                               'prompt' => 'All'
                        ])->label(false);?>       
                  </div>
               </div>
               <div class="col-md-2 col-sm-2">
                  <div class="inlinerow"> 
                     <?= Html::submitButton('Search', ['class' => 'btn btn-primary Mtop20','id' => 'btnSearch','title' => Yii::t('yii', 'Search')]) ?>
                  </div>
               </div>
            </div>
            <?php ActiveForm::end(); ?>
            <div class="inlinerow">
               <div class="inlinerow Mtop20">
                  <!-- Campaign -->
                  <?php if(!empty($orderModel)){?>
                  <div class="orderbox">
                     <!-- Title -->
                     <div class="campboxhead">Orders</div>
                     <!-- /.Title --> 
                     <?php //echo "<pre>";print_r($orderModel);die;
                        foreach ($orderModel as $key => $data) {
                          //echo "<pre>"; print_r($data);die;
                        ?>
                     <!-- Row -->
                     <!-- Food booking list -->
                     <div class="campcontain">
                        <div class="dateholder today-head"> <span class="glyphicon glyphicon-calendar"> </span><?= date('dS F Y',strtotimeNew($key))?></div>
                        <!-- Campaigns -->
                        <div class="campitems">
                           <?php foreach ($data as $value) { ?>
                           <div class="campitembox">
                              <div class="col-md-1 col-sm-1"> <a href="#" data-toggle="modal" data-target="#userProfile_<?= $value['orderid']?>"><input type="button" class="infobtn" id="book"></a> </div>
                              <a href="<?=Url::to(['/orders/food-orders/'.$value['orderid']])?>">
                                 <div class="col-md-4 col-sm-3">
                                    <div class="inlinerow"><?= $value['membername']?></div>
                                 </div>
                                 <div class="col-md-3 col-sm-3">
                                    <div class="inlinerow"><?= $value['phone']?></div>
                                 </div>
                                 <div class="col-md-3 col-sm-3">
                                    <?php if($value['orderstatusid'] == 0) { ?>
                                    <div class="inlinerow"><span class="label label-primary statusLable"><?= $value['orderstatus']?></span></div>
                                    <?php }elseif ($value['orderstatusid'] ==1 ){?>
                                    <div class="inlinerow"><span class="label label-success statusLable"><?= $value['orderstatus']?></span></div>
                                    <?php }elseif ($value['orderstatusid'] == 2){?>
                                    <div class="inlinerow"><span class="label label-warning statusLable"><?= $value['orderstatus']?></span></div>
                                    <?php }elseif ($value['orderstatusid'] == 3) {?>
                                    <div class="inlinerow"><span class="label label-info statusLable"><?= $value['orderstatus']?></span></div>
                                    <?php }elseif ($value['orderstatusid'] == 4) {?>
                                    <div class="inlinerow"><span class="label label-default statusLable"><?= $value['orderstatus']?></span></div>
                                    <?php }elseif ($value['orderstatusid'] == 5) {?>
                                    <div class="inlinerow"><span class="label label-danger statusLable"><?= $value['orderstatus']?></span></div>
                                    <?php }elseif ($value['orderstatusid'] == 6){?>
                                    <div class="inlinerow"><span class="label label-danger statusLable"><?= $value['orderstatus']?></span></div>
                                    <?php }?>
                                 </div>
                                 <div class="col-md-1 col-sm-1 text-right"> <img src="<?php echo $assetName->baseUrl; ?>/theme/images/view-more.png" /> </div>
                              </a>
                           </div>
                           <?php } 
                              ?>
                        </div>
                        <!-- /.Campaigns --> 
                     </div>
                     <!-- Edit category Modal -->
                     <?php foreach ($data as $value){?>
                     <div class="modal fade" id="userProfile_<?= $value['orderid']?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                        <div class="modal-dialog" role="document">
                           <div class="modal-content">
                              <button type="button" class="close profile-close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                              <div class="modal-body">
                                 <form class="form-horizontal">
                                    <fieldset>
                                       <div class="row">
                                          <div class="col-sm-6 col-md-4">
                                             <?php if(!empty($value['memberimage'])) { ?>
                                             <img src="<?= Yii::$app->params['imagePath'].$value['memberimage'];?>" alt="" class="img-rounded img-responsive" style = "width: 150px; height: 150px;" />
                                             <?php } else { ?>
                                             <img src="<?= $assetName->baseUrl; ?>/theme/images/default-user.png" class="img-rounded img-responsive" style = "width: 150px; height: 150px;"/>
                                             <?php }?>
                                          </div>
                                          <div class="col-sm-6 col-md-8">
                                             <h4><?= $value['membername'] ?></h4>
                                             <ul class="pro-details">
                                                <li> <i class="glyphicon glyphicon-phone-alt"></i> <?= $value['phone']?>
                                                </li>
                                                <li>  <i class="glyphicon glyphicon-envelope"></i> <?= $value['email']?>
                                                </li>
                                                <li><i class="glyphicon glyphicon-map-marker pull-left"></i> <?= $value['residence_address1'].','.$value['residence_address2']?><br>
                                                   <?= $value['residence_district'].','.$value['residence_state']?></p>
                                                </li>
                                             </ul>
                                          </div>
                                       </div>
                                    </fieldset>
                                 </form>
                              </div>
                           </div>
                        </div>
                     </div>
                     <?php }
                        }
                        } else {
                        echo 'No results found.';
                        }?>
                  </div>
               </div>
         </fieldset>
         </div>   
      </div>
      <!-- /. Blockrow closed --> 
   </div>
</div>
<!-- Contents closed -->