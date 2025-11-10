<?php
use yii\helpers\Html;
use yii\helpers\Url;
use backend\assets\DatePickAsset;

$assetName = DatePickAsset::register ( $this );
$this->registerJsFile ( $assetName->baseUrl . '/theme/js/Remember.billManager.ui.js', [
		'depends' => [
				DatePickAsset::className ()
		]
] );
?>
<?php // Commented the edit button bcoz opening balance calculation is automated. Done by DM on 11/08/2017 ?>
<!-- <div class="inlinerow bg-primary text-right">
	<h4 class="col-md-5">Opening Balance</h4>
	<span class="col-md-3">
		<input  type="text" class="form-control openBalance text-right" name="openingBalance" id="openingBalance" maxlength="12"
			value="<?php // echo ($openingBalance != '') ? $openingBalance: 0;?>" <?php //if($billsData || ($openingBalance != '')){?>readonly<?php // } ?>>
		
		<button class="btn btn-warning btn-xs editOpenbalance glyphicon glyphicon-pencil OpenbalanceButtons <?php // if(!$billsData && ($openingBalance == '')){?> nodisplay <?php //}?>" title="Edit Opening Balance"></button>
		<button class="btn btn-success btn-xs saveOpeningBalance glyphicon glyphicon-ok OpenbalanceButtons <?php // if($billsData || ($openingBalance != '')){?> nodisplay <?php // }?>" title="Save Opening Balance"></button>
	</span>
</div> -->
<!-- Accounting table -->
<table cellpadding="0" cellspacing="0" class="table table-bordered accountingtab">
	<tr>
		<th width="14%">Transaction Date</th>
		<th width="28%">Description</th>
		<th width="16%">Debit (Dr)</th>
		<th width="18%">Credit (Cr)</th>
		<th width="22%" colspan="2" class="text-center">Actions</th>
	</tr>
	<tr class="opening-balance">
		<td><span class="col-md-12 nonEditables"></span></td>
		<td><strong>Opening Balance</strong></td>
		<td>
			<span class="col-md-12 text-right nonEditables">
				<strong>
					<?= ($openingBalanceType === 'debit') ? yii::$app->MoneyFormat->decimalWithComma($openingBalance) : '' ?>
				</strong>
			</span>
		</td>
		<td>
			<span class="col-md-12 text-right nonEditables">
				<strong>
				<?= ($openingBalanceType === 'credit') ? yii::$app->MoneyFormat->decimalWithComma($openingBalance) : '' ?>
				</strong>
			</span>
		</td>
		<td colspan="2"></td>
	</tr>
	<?php
	$totalDebits = ($openingBalanceType === 'debit') ? $openingBalance : 0;
	$totalCredits = ($openingBalanceType === 'credit') ? $openingBalance: 0;
	$balance = 0;
	if($billsData){
		foreach ($billsData as $eachBill){
			$totalDebits += !empty($eachBill['debit']) ? $eachBill['debit'] : 0;
			$totalCredits += !empty($eachBill['credit']) ? $eachBill['credit'] : 0;
			?>
			<tr data-bid = "<?php echo Html::encode($eachBill['billid']);?>">
				<td>
					<div class="input-group nodisplay editables">
						<input type="text" class="form-control transactionDate" name="transactionDate" value="<?php echo date(yii::$app->params['dateFormat']['sqlDateFormat'], strtotimeNew($eachBill['transactiondate']));?>" readonly/>
						<span class="input-group-addon">
					        <i class="glyphicon glyphicon-calendar"></i>
					    </span>
					</div>
					<span class="col-md-12 nonEditables"><?php echo date(yii::$app->params['dateFormat']['viewDateFormat'], strtotimeNew($eachBill['transactiondate']));?></span>
				</td>
				<td>
					<input type="text" class="form-control nodisplay editables description" name="description" value="<?php echo Html::encode($eachBill['description']);?>" maxlength="499"/>
					<span class="col-md-12 nonEditables"><?php echo Html::encode($eachBill['description']);?></span>
				</td>
				<td>
					<input type="text" class="form-control nodisplay editables debit" name="debit" value="<?php echo Html::encode($eachBill['debit']);?>" maxlength="12"/>
					<span class="col-md-12 text-right nonEditables "><?php echo !empty($eachBill['debit']) ? Html::encode(yii::$app->MoneyFormat->decimalWithComma($eachBill['debit'])):'';?></span>
				</td>
				<td>
					<span class="col-md-12 text-right nonEditables"><?php echo !empty($eachBill['credit']) ? Html::encode(yii::$app->MoneyFormat->decimalWithComma($eachBill['credit'])):'';?></span>
					<div class="inlinerow relative nodisplay editables">
						<input type="text" class="form-control amountbox credit" name="credit" value="<?php echo Html::encode($eachBill['credit']);?>" maxlength="12"/>
						<button type="submit" class="btn btn-warning btn-xs paybtn" data-backdrop="static"  data-toggle="modal" data-target="#paymentmethod"  title="Add Payment Details">Payment</button>
					</div>
				</td>
				<td class="text-center" width="12%">
					<button class="btn btn-primary btn-sm editBtn" title="Edit">
						<span class="glyphicon glyphicon-edit"></span>
					</button>
					<button class="btn btn-danger btn-sm delBtn" title="Delete">
						<span class="glyphicon glyphicon-trash"></span>
					</button>
					<button class="btn btn-success btn-sm nodisplay updateBtn" title="Update">
						<!-- <span class="glyphicon glyphicon-ok"></span> -->
						Update
					</button>
				</td>
				
				<td class="text-center" width="12%">
				<?php if($eachBill['credit']){?>
					<a href=<?php echo Url::to(['/bill-manager/download-reciept', 'id' => $eachBill['billid']])?> class="btnreset " title="View Receipt">
						<img src="<?php echo $assetName->baseUrl; ?>/theme/images/pdf.png"/>
					</a>
					<button class="btnreset btnMail"  title="Email the Receipt">
						<img src="<?php echo $assetName->baseUrl; ?>/theme/images/mail.png"/>
					</button>
				<?php }?>
				</td>
				
			</tr>
			<?php 
		}
	}
	?>
	
	<tr data-bid = "">
		<td>
			<div class="input-group">
				<input type="text" class="form-control transactionDate new-date" name="transactionDate" readonly/>
				<span class="input-group-addon">
			        <i class="glyphicon glyphicon-calendar"></i>
			    </span>
			</div>
		</td>
		<td><input type="text" class="form-control description" name="description" maxlength="499"/></td>
		<td><input type="text" class="form-control debit" name="debit" maxlength="12"/></td>
		<td>
			<div class="inlinerow relative">
				<input type="text" class="form-control amountbox credit" name="credit" maxlength="12"/>
				<button type="submit" class="btn btn-warning btn-xs paybtn" data-backdrop="static" data-toggle="modal" data-target="#paymentmethod" title="Add Payment Details">Payment</button>
			</div>
		</td>
		<td class="text-center" colspan="2">
			<button class="btn btn-success btn-sm saveBtn" title="Save">
				<!-- <span class="glyphicon glyphicon-ok"></span> -->
				Save
			</button>
		</td>
	</tr>

	<tr>
		<td colspan="2" class="totalbox"><strong>Total</strong></td>
		<td class="text-right totalbox"><strong><?php echo yii::$app->MoneyFormat->decimalWithComma($totalDebits);?></strong></td>
		<td class="text-right totalbox"><strong><?php echo yii::$app->MoneyFormat->decimalWithComma($totalCredits);?></strong></td>
		<td colspan="2" class="totalbox">&nbsp;</td>
	</tr>
	<tr>
	<?php $balance = $totalDebits - $totalCredits;?>
		<td colspan="2" class="balancebox"><strong>Balance : (total debits - total credits)</strong></td>
		<td class="text-right balancebox text-danger"><strong><?php if($balance >= 0){ echo '('.yii::$app->MoneyFormat->decimalWithComma($balance).')';}?></strong></td>
		<td class="text-right balancebox text-success"><strong><?php if($balance < 0){ echo yii::$app->MoneyFormat->decimalWithComma(-1 * $balance);}?></strong></td>
		<td colspan="2" class="balancebox">&nbsp;</td>
	</tr>
	
</table>

<!-- /.Accounting table -->

</div>
	</div>
</div>
<!-- /. Blockrow closed -->

<!-- Contents closed -->

<!-- Modal -->
<div class="modal fade" id="paymentmethod" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="myModalLabel">Payment Method</h4>
			</div>
			<div class="modal-body">

				<table cellpadding="0" cellspacing="0" class="table paytable">
					<tr>
					<td class="text-center">
							<div class="radio">
								<label> 
									<input type="radio" name="paymentType" class="paymentType" id="paymentTypeCard" value="card"> Card
								</label>
							</div>
						</td>
						<td class="text-center">
							<div class="radio">
								<label> 
									<input type="radio" name="paymentType" class="paymentType" id="paymentTypeCash" value="cash"> Cash
								</label>
							</div>
						</td>
						<td class="text-center">
							<div class="radio">
								<label> 
									<input type="radio" name="paymentType" class="paymentType" id="paymentTypeCheque" value="cheque"> Cheque
								</label>
							</div>
						</td>
						<td class="text-center">
							<div class="radio">
								<label> 
									<input type="radio" name="paymentType" class="paymentType" id="paymentTypeUpi" value="upi"> UPI
								</label>
							</div>
						</td>
						<td class="text-center">
							<div class="radio">
								<label> 
									<input type="radio" name="paymentType" class="paymentType" id="paymentTypeNeft" value="neft"> NEFT
								</label>
							</div>
						</td>
						
					</tr>
				</table>
				<input type="hidden" name="savedpaymentType" id="savedpaymentType" />

				<!-- Hide this, if payment is cash -->
				<table cellpadding="0" cellspacing="0" class="table paytable nodisplay" id="chequeDetails">
					<tr><td><input type="hidden" id="payTypeBillId" value=""></td></tr>
					<tr>
						<td>Cheque Number<span class="manditory"></span></td>
						<td>
							<input type="text" class="form-control" name="ChequeNo" id="ChequeNo" maxlength="20"/>
							<input type="hidden" name="savedChequeNo" id="savedChequeNo" />
						</td>
					</tr>
					<tr>
						<td>Cheque Date<span class="manditory"></span></td>
						<td>
							<div class="input-group">
								<input type="text" class="form-control chequeDate" name="ChequeDate" id="ChequeDate" readonly/>
								<input type="hidden" name="savedChequeDate" id="savedChequeDate" />
								<span class="input-group-addon">
							        <i class="glyphicon glyphicon-calendar"></i>
							    </span>
							</div>
						</td>
					</tr>
					<tr>
						<td>Bank Name<span class="manditory"></span></td>
						<td>
							<input type="text" class="form-control" name="Bank" id="Bank" maxlength="100"/>
							<input type="hidden" name="savedBank" id="savedBank" />
						</td>
					</tr>
					<tr>
						<td>Branch Name<span class="manditory"></span></td>
						<td>
							<input type="text" class="form-control" name="Branch" id="Branch" maxlength="44"/>
							<input type="hidden" name="savedBranch" id="savedBranch" />
						</td>
					</tr>
				</table>
				<!-- hide -->

				<!-- Hide this, if payment is cash -->
				<table cellpadding="0" cellspacing="0" class="table paytable nodisplay" id="upiDetails">
					<tr><td><input type="hidden" id="payTypeBillId" value=""></td></tr>
					<tr>
						<td>UPI Id<span class="manditory"></span></td>
						<td>
							<input type="text" class="form-control" name="UpiId" id="UpiId" maxlength="20"/>
							<input type="hidden" name="savedUpiId" id="savedUpiId" />
						</td>
					</tr>
					<tr>
						<td>Transaction Date<span class="manditory"></span></td>
						<td>
							<div class="input-group">
								<input type="text" class="form-control chequeDate" name="UpiDate" id="UpiDate" readonly/>
								<input type="hidden" name="savedUpiDate" id="savedUpiDate" />
								<span class="input-group-addon">
							        <i class="glyphicon glyphicon-calendar"></i>
							    </span>
							</div>
						</td>
					</tr>
					<tr>
						<td>UPI Transaction Id<span class="manditory"></span></td>
						<td>
							<input type="text" class="form-control" name="TxnId" id="TxnId" maxlength="100"/>
							<input type="hidden" name="savedTxnId" id="savedTxnId" />
						</td>
					</tr>
				</table>
				<!-- hide -->
				
				<!-- Hide this, if payment is cash && cheque-->
				<table cellpadding="0" cellspacing="0" class="table paytable nodisplay" id="neftDetails">
					<tr><td><input type="hidden" id="payTypeBillId" value=""></td></tr>
					<tr>
						<td>NEFT Number<span class="manditory"></span></td>
						<td>
							<input type="text" class="form-control" name="NeftNo" id="NeftNo" maxlength="20"/>
							<input type="hidden" name="savedNeftNo" id="savedNeftNo" />
						</td>
					</tr>
					<tr>
						<td>NEFT Date<span></span></td>
						<td>
							<div class="input-group">
								<input type="text" class="form-control chequeDate" name="NeftDate" id="NeftDate" readonly/>
								<input type="hidden" name="savedNeftDate" id="savedNeftDate" />
								<span class="input-group-addon">
							        <i class="glyphicon glyphicon-calendar"></i>
							    </span>
							</div>
						</td>
					</tr>	
					<tr>
						<td>Bank Name<span></span></td>
						<td>
							<input type="text" class="form-control" name="NeftBank" id="NeftBank" maxlength="100"/>
							<input type="hidden" name="savedNeftBank" id="savedNeftBank" />
						</td>
					</tr>
					<tr>
						<td>Branch Name<span></span></td>
						<td>
							<input type="text" class="form-control" name="NeftBranch" id="NeftBranch" maxlength="44"/>
							<input type="hidden" name="savedNeftBranch" id="savedNeftBranch" />
						</td>
					</tr>
					
				</table>
				<!-- hide -->
				
				
				<!-- Hide this, if payment is cash && cheque && neft-->
				<table cellpadding="0" cellspacing="0" class="table paytable nodisplay" id="cardDetails">
					<tr><td><input type="hidden" id="payTypeBillId" value=""></td></tr>
					<tr>
						<td>Last four digits of your card<span class="manditory"></span></td>
						<td>
							<input type="text" class="form-control" name="CardNo" id="CardNo" maxlength="4"/>
							<input type="hidden" name="savedCardNo" id="savedCardNo" />
						</td>
					</tr>
					
					<tr>
						<td>Card Issuer<span></span></td>
						<td>
							<input type="text" class="form-control" name="CardBank" id="CardBank" maxlength="100"/>
							<input type="hidden" name="savedCardBank" id="savedCardBank" />
						</td>
					</tr>
					
				</table>
				<!-- hide -->
				
				
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default cancelPaymentDetails" data-dismiss="modal" aria-label="Cancel">Cancel</button>
				<button type="button" class="btn btn-primary savePaymentDetails">Save</button>
			</div>
		</div>
	</div>
	