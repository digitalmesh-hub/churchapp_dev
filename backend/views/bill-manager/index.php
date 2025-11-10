<?php
use yii\helpers\Html;
use yii\helpers\Url;
use backend\assets\DatePickAsset;

/* @var $this yii\web\View */
/* @var $searchModel common\models\searchmodels\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$assetName = DatePickAsset::register ( $this );
$this->registerJsFile ( $assetName->baseUrl . '/theme/js/Remember.billManager.ui.js', [
		'depends' => [
				DatePickAsset::className ()
		]
] );
$this->title = 'Bills';
?>
<!-- Header -->
<div class="col-md-12 col-sm-12 col-xs-12 pageheader Mtop15">
	<?= $this->title ?>
</div>



<!-- Content -->


<div class="col-md-12 col-sm-12 contentbg">
	<div class="col-md-12 col-sm-12 Mtopbot20">
	<!-- Year and month filter -->
		<div class="blockrow Mtop25">
			<div class="row">
				<div class="col-md-2 col-sm-3">
					<div class="inlinerow">
						<strong>Year</strong>
					</div>
					<div class="inlinerow">
                 		<?php echo Html::dropDownList ( 'year', date('Y') , $years, [ 'id' => 'year', 'class' => 'form-control', 'prompt' => 'Please Select'] );?>
                    </div>
				</div>
				<div class="col-md-2 col-sm-3">
					<div class="inlinerow">
						<strong>Month</strong>
					</div>
					<div class="inlinerow">
                    	<?php echo Html::dropDownList ( 'month', '', $months, ['id' => 'month', 'class' => 'form-control', 'prompt' => 'Please Select'] );?>
                    </div>
				</div>

				<div class="col-md-3 col-sm-4 pull-right text-right">
					<div class="inlinerow billname">
						<strong><?php echo $member->firstName . ' ' . $member->middleName. ' ' . $member->lastName;?></strong>
					
					</div>
					<div class="inlinerow"><?php echo $member->member_mobile1;?></div>
					<div class="inlinerow" id="memberEmail"><?php echo $member->member_email;?></div>
					
					<?php echo Html::hiddenInput('memberid', $member->memberid, ['id' => 'memberid'])?>
					<?php echo Html::hiddenInput('monthlyBillUrl', Url::to('bill-manager/monthly-bill'), ['id' => 'monthlyBillUrl'])?>
					<?php echo Html::hiddenInput('billUploadUrl', Url::to('bill-manager/upload-bill'), ['id' => 'billUploadUrl'])?>
					<?php echo Html::hiddenInput('paymentDetailsUrl', Url::to('bill-manager/payment-details'), ['id' => 'paymentDetailsUrl'])?>
					<?php echo Html::hiddenInput('billDeleteUrl', Url::to('bill-manager/delete-bill'), ['id' => 'billDeleteUrl'])?>
					<?php echo Html::hiddenInput('openBalUploadUrl', Url::to('bill-manager/upload-openbalance'), ['id' => 'openBalUploadUrl'])?>
					<?php echo Html::hiddenInput('mailRecieptUrl', Url::to('bill-manager/mail-reciept'), ['id' => 'mailRecieptUrl'])?>
					<?= Html::hiddenInput('defaultChequeUrl', Url::to('bill-manager/last-cheque-data'), ['id' => 'defaultChequeUrl']) ?>
					<?= Html::hiddenInput('defaultNeftUrl', Url::to('bill-manager/last-neft-data'), ['id' => 'defaultNeftUrl']) ?>
					<?= Html::hiddenInput('defaultUpiUrl', Url::to('bill-manager/last-upi-data'), ['id' => 'defaultUpiUrl']) ?>		
				</div>
			</div>
		</div>
		<!-- /.Year and month filter -->
		<div id="billsTableDiv">
</div>


<?php 
//finds the value of next and previous index
$key =array_search($memberid,array_column($data,'memberid'));
$nextIndex = $key + 1;
$prevIndex = $key - 1;
end($data);
 $last_id=key($data)+1;
?>


 <!-- save -->
                <?php 
                if($prevIndex != -1)
                {
                	
                 echo Html::a('Previous', Url::to(['/bill-manager', 'id' => $data[$prevIndex]['memberid']]),['url'=>Url::to(['/bill-manager','id' => $data[$prevIndex]['memberid']]),'class' => 'btn btn-primary pull-left pageNav','title' => 'Previous Member']);
                  
                }
                if($nextIndex != $last_id)
                {
                echo Html::a('Next', Url::to(['/bill-manager', 'id' => $data[$nextIndex]['memberid']]),['url'=>Url::to(['/bill-manager','id' => $data[$nextIndex]['memberid']]),'class' => 'btn btn-primary pull-right pageNav', 'title' => 'Next Member']);
                }
                ?>
                    
 <!-- /.save -->
 
 <!-- Modal -->
			
			<div id="alertModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close" data-dismiss="modal">&times;</button>
                
            </div>
            <div class="modal-body">
                <p>
                <?php $message = Yii::$app->params['message'];
                	echo $message;?></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" id="modal-btn-no" data-dismiss="modal">NO</button>
        		<button type="button" class="btn btn-success goNav" id="modal-btn-si" data-dismiss="modal" value="">YES</button>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>

<!-- Modal closed -->
