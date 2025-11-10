<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\assets\AppAsset;
use common\models\extendedmodels\ExtendedBevcoOrder;

$assetName = AppAsset::register($this);
$this->registerJsFile ( $assetName->baseUrl . '/theme/js/Remember.bevco.ui.js', ['depends' => [AppAsset::className ()]]);

$this->title = 'Beverage Booking';
$items = $order->items;
$slot = $order->slot;
$total = 0;
?>
<div class="col-md-12 col-sm-12 pageheader Mtop15"><?= $this->title ?></div>
<!-- Content -->
<div class="col-md-12 col-sm-12 contentbg">
   <div class="col-md-12 col-sm-12 Mtopbot20">
      <div class="blockrow">
      	<div class="col-xs-12">
          <h2 class="bevco-order-page-header">
            <small class="pull-right">Booking Date: <?= date('d F Y', strtotimeNew($order->order_date))?></small>
          </h2>
        </div>
      </div>
      <div class="blockrow bevco-order-info">
      	<div class="col-sm-4 bevco-order-col">
      	  	<b>Member Details</b><br><br>
          	<b>Name:</b> <?= $order->member->getFullNameWithMemberNo(); ?><br>
          	<b>Phone:  </b> <?= $order->member->member_mobile1?><br>
          	<b>Email:</b> <?= $order->member->member_email?>
        </div>
        <div class="col-sm-4 bevco-order-col">
          	<b>Order</b><br><br>
            <b>Order ID:</b> <?= $order->id ?><br>
            <b>Status:  </b> <?= $order->getStatuses($order->status)?><br>
            <b>Order Created On:</b> <?= date('d F Y H:i a', strtotimeNew($order->created_at))?>
        </div>
        <div class="col-sm-4 bevco-order-col">
        	<b>Slot</b><br><br>
          	<b>Slot Date:</b> <?= date('d F Y', strtotimeNew($slot->slot_date))?><br>
          	<b>Time:  </b> <?= $order->getFullSlot()?><br>
          	<b>Slot Number:</b> <?= $slot->slot_number?>
        </div>
      </div>
       <div class="blockrow bevco-order-items">
       	<div class="col-xs-12 table-responsive">
          <table class="table table-striped">
            <thead>
	            <tr>
	              <th>Product</th>
	              <th>Category</th>
                <th>Qty</th>
	              <th style="text-align: right;">Unit Price</th>
	              <th style="text-align: right;">Subtotal</th>
	            </tr>
            </thead>
            <tbody>
            	<?php if(!empty($items)) { ?>
	            	<?php foreach ($items as $k => $v) { 
                  $total += $v->product->price * $v->quantity;
                  ?>
	            		<tr>
			              <td><?= $v->product->name?></td>
			              <td><?= $v->product->category->name?></td>
                    <td><?= $v->quantity?></td>
			              <td style="text-align: right;"><?= Yii::$app->formatter->asCurrency($v->product->price, 'INR');?></td>
			              <td style="text-align: right;"><?= Yii::$app->formatter->asCurrency($v->product->price * $v->quantity, 'INR');?></td>
			            </tr>
	            	<?php }?>
            	<?php } ?>
            </tbody>
          </table>
        </div>
       </div>
      <div class="blockrow">
        <div class="col-xs-6">
          <div class="table-responsive">
            <table class="table">
              <tbody>
                <tr>
                  <th style="width:50%">Total:</th>
                  <td><?= Yii::$app->formatter->asCurrency($total, 'INR');?></td>
                </tr>
            </tbody>
          </table>
          </div>
        </div>
      </div>
      <div class="blockrow">
        	<div class="col-xs-12">
            <?php if($order->status == ExtendedBevcoOrder::STATUS_PLACED) { ?>
	          	<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#completeOrderModal">
  					     Complete Order
				      </button>
            <?php } ?>
            <?= Html::a('Go Back', ['beverages/manage-booking'], ['class' => 'btn btn-warning']) ?>
	        </div>
      </div>
  </div>
</div>
<!-- Modal -->
<div class="modal fade" id="completeOrderModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Order: <?= $order->id?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <?php $form = ActiveForm::begin([
    		'id' => 'complete-bevco-order',
    		'action' => ['beverages/complete-order/', 'id' => $order->id],
    		'options' => ['class' => 'form-horizontal'],
		]) ?>
		 <fieldset>
            <div class="form-group">
            <label class="col-md-3 control-label text-left">Status <span style="color: red;">*</span></label>
            <div class="col-md-6">
			        <?= $form->field($order, 'status') ->dropDownList(
    	            $order->getStatuses(null, [ExtendedBevcoOrder::STATUS_PLACED, ExtendedBevcoOrder::STATUS_EXPIRED]),[
    	                'prompt' => '--Select--'
    	            ])->label(false);
	            ?>
	    	</div>
			</div>
		</fieldset>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <?= Html::submitButton('Save', ['class' => 'btn btn-primary']) ?>
      </div>
      <?php ActiveForm::end() ?>
    </div>
  </div>
</div>