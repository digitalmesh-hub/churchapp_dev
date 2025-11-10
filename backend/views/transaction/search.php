<?php
$i = (20 * $page) + 1;
$lastRecord = $i + 19;
if ($lastRecord > $count) {
	$lastRecord = $count;
}
?>


<?php
if ($listData) {
	?>
	<div class="summary">
	Showing&nbsp;<b><?php echo $i;?></b>-<b><?php echo $lastRecord;?></b> of <b><?php echo $count;?></b> items
	</div>
<table cellpadding="0" cellspacing="0" class="table table-bordered">
	<tr>
		<th>Sl.No</th>
		<th>Membership No.</th>
		<th>Name</th>
		<th>Transaction ID</th>
		<th>Status</th>
		<th>Amount</th>
		<th>Date</th>
		<th>Source</th>
		<?php if ($showOtherInfo) { ?> <th>Other Info</th> <?php } ?>
	</tr>
	<?php
	foreach ( $listData as $row ) {
		?>
    	<tr id="<?php echo $row['txnid'];?>">
			<td><?php echo $i;?></td>
			<td><?php echo $row['memberno'];?></td>
			<td><?php echo $row['firstName'].' '. $row['middleName'].' '. $row['lastName'];?></td>
			<td><?php echo $row['txnid'];?></td>
			<td>
				<?php 
					echo ($row['status'] == 'failure') ? 'failed' : $row['status'];
					if($showOtherInfo && $row['status'] == 'pending') {
				?>
				<br>
				<input type="button" class="btn btn-success" value="sync now" 
				id="syncPendingPayment" style='margin-top:5px;width:auto;' title="sync now"
				onclick="syncPendingPayment(<?=$row['memberId']?>,'<?=$row['txnid']?>')" 
				>
				<?php } ?>
				
			</td>
			<td><?php echo yii::$app->MoneyFormat->decimalWithComma($row['amount']);?></td>
			<td><?php 
				$date = new DateTime( $row['created'], new DateTimeZone('Asia/Kolkata'));
				echo $date->format('Y-F-d h:i a');
			?></td>
			<td><?php echo $row['source'];?></td>
			<?php if ($showOtherInfo) { ?><td>
				<ul class="list-unstyled">
					<li>&bull; BRN: <?php echo $row['brn'];?> </li>
					<li>&bull; MOP: <?php echo $row['pmd'];?> </li>
				</ul>
			</td><?php } ?>
		</tr>
		<?php
		$i ++;
	}
	?>
</table>
<div class="inlinerow text-left" id="testing">
	<nav aria-label="Page navigation">
		<ul class="pagination">
			<li class="first" id="first">
			<?php 
			if($page>=1){
				?>
				<a class="firstLink pagerLink" href="javascript:void(0)" aria-label="Prev" value="<?php echo 1; ?>"> 
					<?php }
					?>
					<span aria-hidden="true">&laquo;</span>
				</a>
			</li>
			<li class="prev" id="prev">
			<?php 
			if($page>=1){
				?> 
				<a class="pagerLink" href="javascript:void(0)" aria-label="Previous" value="<?php echo $page;?>">
				<?php }?>
					<span aria-hidden="true">&lsaquo;</span>
				<?php if($page>=1){?> 	
				</a>
				<?php 
				}
			?>
			</li>
            <?php
			for($i = 1; $i <= $totalCount; $i ++) {
				?>
                <li	class="<?=($page == 0 && $i == 1)?"active":"";?>">
                	<a class="pagerLink" value="<?php echo $i;?>" name="pagerIndex" href="javascript:void(0)">
                   			<?php echo $i;?>
                   	</a>
                </li>
            	<?php
			}
			?>
				
			<li class="next" id="next">
			<?php if($totalCount > ($page+1)) {?> 
				<a class="pagerLink" value="<?php echo $page+2;?>" href="javascript:void(0)" aria-label="Next">
			<?php }?>
					<span aria-hidden="true">&rsaquo;</span>
			<?php if($totalCount > ($page+1)){?> 	
			</a>
			<?php }?>
			</li>
			<li class="last" id="last">
			<?php if($totalCount > ($page+1)) {?> 
				<a class="lastLink pagerLink" href="javascript:void(0)" value="<?php echo $totalCount; ?>" aria-label="Next">
			<?php }
			?>
					<span aria-hidden="true">&raquo;</span>
				</a> 
			</li>
		</ul>
		<input type="hidden" id="count" value="<?php echo $totalCount; ?>"> 
		<input type="hidden" id="pagerValue" value="0">
	</nav>
</div>


<?php
} else {
	?>
<table class=table table-striped table-bordered>
	<tr>
		<td colspan="6">
			<div class="empty">No records found</div>
		</td>
	</tr>
</table>

<?php
}
?>
<script>
	function syncPendingPayment(memberId,txnId) {
		$(".overlay").show();
		$.ajax({
			url: 'transaction/sync-pending-payment',
			type: "GET",
			/* cache: false,
			async: true, */
			dataType: "json",
			data: {
				memberId: memberId,
				txnId: txnId
			},
			//  contentType: 'application/json',
			success: function(res) {
				$(".overlay").hide();
				if(res.error) {
					swal({
						title: 'Payment Status Update',
						text: 'An error occured while processing the request.',
						type: 'error',
					});
				}
				else {
					swal({
					title: 'Payment Status Update',
					text: 'Payment status checked successfully',
					type: 'success'
					});
					if(res.status.toLowerCase() !== 'pending') {
						$('#pagerValue').val($('ul.pagination').find('li.active a').attr("value"));
						TransactionJS._searchResult();
					}
				}
				return;
			},
			error: function(er) {
				$(".overlay").hide();
				swal({
					title: 'Payment Status Update',
					text: 'An error occured while processing the request.',
					type: 'error',
				});
				return;
			}
		});

	}

</script>