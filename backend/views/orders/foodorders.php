<?php
   use yii\helpers\Html;
   use backend\assets\AppAsset;
   use yii\widgets\ActiveForm;
   use yii\helpers\ArrayHelper;
   use yii\helpers\Url;
   
   $assetName = AppAsset::register($this);
   
   $this->registerJsFile(
      $assetName->baseUrl . '/theme/js/Remember.orders.ui.js',
      [
          'depends' => [
              AppAsset::className()
          ]
      ]
      );
   
   echo Html::hiddenInput(
      'reject-order',
      \Yii::$app->params['ajaxUrl']['reject-order'],
      [
          'id'=>'reject-order'
      ]
      );
   echo Html::hiddenInput(
      'update-status',
      \Yii::$app->params['ajaxUrl']['update-status'],
      [
          'id'=>'update-status'
      ]
      );
   
   
   /* @var $this yii\web\View */
   /* @var $searchModel common\models\extendedmodels\ExtendedOrdersSearch */
   /* @var $dataProvider yii\data\ActiveDataProvider */
   
   $this->title = 'Bookings';
   ?>
<!-- Contents -->
<div class="container">
   <div class="row">
      <!-- Content -->
      <div class="col-md-12 col-sm-12 contentbg">
         <div class="col-md-12 col-sm-12 Mtopbot20">
            <!-- Sponsor listing -->
            <fieldset class="Mtop20">
               <legend style="font-size: 20px;">Food Orders 
                  <a href="<?=Url::to(['/orders/index/'])?>" class="btn btn-primary btn-sm pull-right Mtop-5">Back to Listing</a>
               </legend>
               <div class="inlinerow">
                  <div class="inlinerow Mtop20">
                     <!-- Campaign -->
                     <div class="orderbox">
                        <!-- Title -->
                        <div class="orderhead">
                           <span class="glyphicon glyphicon-user "> </span> <?= $userDetails['firstName'].' '.$userDetails['middleName'].' '.$userDetails['lastName'];?>
                           <div class="pull-right"> <span class="glyphicon glyphicon-time "> </span> <?= $userDetails['ordertime'];?> </div>
                           <div class="pull-right"> <span class="glyphicon glyphicon-calendar "> </span> <?= date('d-F-Y',strtotimeNew($userDetails['orderdate']));?> </div>
                        </div>
                        <!-- /.Title --> 
                        <!-- Food booking list -->
                        <div class="order-contain">
                           <table cellpadding="0" cellspacing="0" class="table table-striped">
                              <tr>
                                 <th>Product</th>
                                 <th>Price</th>
                                 <th class="text-center">Quantity</th>
                                 <th class="text-right">Total</th>
                              </tr>
                              <?php $total = 0;
                                 $tax = 0;
                                 $orderItem = [];
                                 if(!empty($orderDetails)) {
                                    foreach ($orderDetails as $key => $data) {
                                    if(in_array($data['orderitemsid'], $orderItem)){
                                     continue;
                                    }
                                    array_push($orderItem, $data['orderitemsid']);
                                    
                                    ?>
                                 <tr>
                                    <td><?= $data['property'];?></td>
                                    <td>₹ <?= $data['price'];?></td>
                                    <td class="text-center"><?= $data['quantity']?></td>
                                    <td class="text-right">₹ <?= $data['price'] * $data['quantity'];?></td>
                                 </tr>
                                 <?php $total += $data['price']* $data['quantity'];
                                    ?>
                              <?php } }?>
                           </table>
                        </div>
                        <!-- /. Food booking list --> 
                        <!-- Food booking list -->
                        <div class="order-contain">
                           <!-- Row -->
                           <div class="campitembox ">
                              <div class="col-md-8 col-sm-9 text-right">
                                 <div class="inlinerow"><b>Total</b></div>
                              </div>
                              <div class="col-md-4 col-sm-3 text-right">
                                 <div class="inlinerow"><b>₹ <?= $total;?></b></div>
                              </div>
                           </div>
                           <?php 
                           if(!empty($orderDetails)) {
                           foreach ($orderDetails as $key => $data) { 
                              if(!empty($data['taxrate'])) { ?>
                                 <div class="campitembox ">
                                    <div class="col-md-8 col-sm-9 text-right">
                                       <div class="inlinerow"><b><?= $data['description']?></b></div>
                                    </div>
                                    <div class="col-md-4 col-sm-3 text-right">
                                       <div class="inlinerow"><b>₹ <?= $data['taxrate'];?></b></div>
                                    </div>
                                 </div>
                           <?php } ?>
                           <?php $tax += $data['taxrate'];?>
                           <?php } }?>
                           <div class="campitembox alert-success">
                              <div class="col-md-8 col-sm-9 text-right">
                                 <div class="inlinerow"><b>Grand Total</b></div>
                              </div>
                              <div class="col-md-4 col-sm-3 text-right">
                                 <div class="inlinerow"><b>₹ <?= $total + $tax;?></b></div>
                              </div>
                           </div>
                        </div>
                        <!-- /. Food booking list --> 
                     </div>
                     <!-- /.Campaign -->
                     <div class="inner-rows text-center">
                        <?php if($orderStatus == 0) {?>
                        <input type="button" value="Reject Order" title= 'Reject Order' class="btn btn-danger btn-extra" id="btnSubmit" data-toggle="modal" data-target="#rejectModal_<?= $orderId?>"> 
                        <?= Html::submitButton('Confirm Order', ['class' => 'btn btn-success btn-extra',
                           'id' =>'btn-confirm',
                           'data-status' => 1,
                           'title' => Yii::t('yii', 'Confirm Order'),
                           ]) ?>               
                        <?php }?>
                        <?php if($orderStatus == 1) {?>
                        <input type="button" value="Reject Order" title= 'Reject Order' class="btn btn-danger btn-extra" id="btnSubmit" data-toggle="modal" data-target="#rejectModal_<?= $orderId?>"> 
                        <?= Html::submitButton('Ready', ['class' => 'btn btn-warning btn-extra',
                           'id' =>'btn-confirm',
                           'data-status' => 2,
                           'title' => Yii::t('yii', 'Ready'),
                           ]) ?>             
                        <?php }?>
                        <?php if($orderStatus == 2) {?>
                        <input type="button" value="Reject Order" title= 'Reject Order' class="btn btn-danger btn-extra" id="btnSubmit" data-toggle="modal" data-target="#rejectModal_<?= $orderId?>"> 
                        <?= Html::submitButton('Handover', ['class' => 'btn btn-info btn-extra',
                           'id' =>'btn-confirm',
                           'data-status' => 3,
                           'title' => Yii::t('yii', 'Handover'),
                           ]) ?>                   
                        <?php }?>
                        <?php if($orderStatus == 6) {?>
                        <input type="button" value="Reject Order" title= 'Reject Order' class="btn btn-danger btn-extra" id="btnSubmit" data-toggle="modal" data-target="#rejectModal_<?= $orderId?>"> 
                        <?= Html::submitButton('Remove From My Order', ['class' => 'btn btn-info btn-extra',
                           'id' =>'btn-confirm',
                           'data-status' => 6,
                           'title' => Yii::t('yii', 'Remove'),
                           ]) ?>                   
                        <?php }?>
                     </div>
                  </div>
                  <?php if($orderStatus == 3){?>
                  <!-- Alert Message -->
                  <div class="inlinerow">
                     <div class="col-md-8 col-md-offset-2">
                        <div class="alert alert-danger pad10" role="alert">
                           <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                           <strong></strong> This order has been completed !
                        </div>
                     </div>
                  </div>
                  <!-- /.Alert Message -->
                  <?php }?>
                  <?php if($orderStatus == 5){?>
                  <!-- Alert Message -->
                  <div class="inlinerow">
                     <div class="col-md-8 col-md-offset-2">
                        <div class="alert alert-danger pad10" role="alert">
                           <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                           <strong></strong> This order has been cancelled by the member
                        </div>
                     </div>
                  </div>
                  <!-- /.Alert Message -->
                  <?php }?>
                  <?php if($orderStatus == 4){?>
                  <!-- Alert Message -->
                  <div class="inlinerow">
                     <div class="col-md-8 col-md-offset-2">
                        <div class="alert alert-danger pad10" role="alert">
                           <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                           <strong></strong> This order has been rejected
                        </div>
                     </div>
                  </div>
                  <!-- /.Alert Message -->
                  <?php }?>
                  <div class="blockrow Mtop25 text-right divhide">
                     <nav>
                        <ul class="pagination" id="pging">
                        </ul>
                     </nav>
                  </div>
               </div>
            </fieldset>
         </div>
         <!-- Employee Details Management --> 
      </div>
      <!-- /. Blockrow closed --> 
   </div>
</div>
<!-- Contents closed --> 
<!-- Reject Order Modal -->
<div class="modal fade reject" id="rejectModal_<?= $orderId?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="myModalLabel">Reject Order</h4>
         </div>
         <div class="modal-body">
            <p>Please describe a reason to reject this order </p>
            <p id="reason"><textarea class="form-control rejectreason" rows="2"></textarea></p>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-default btn-cancel" data-dismiss="modal">Cancel</button>
            <?= Html::activeHiddenInput($model, "orderid", ['id' => "orderid",'value' => $orderId])?>
            <?= Html::submitButton('Save', ['class' => 'btn btn-primary caption ',
               'id' =>'btn-reject-reason',
               'title' => Yii::t('yii', 'Save'),
               ]) ?>
         </div>
      </div>
   </div>
</div>