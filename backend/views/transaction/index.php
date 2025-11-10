<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use backend\assets\AppAsset;
use yii\helpers\Url;
use yii\data\ActiveDataProvider;
use backend\assets\TransactionAsset;
use SebastianBergmann\CodeCoverage\Report\PHP;

/* @var $this yii\web\View */
/* @var $searchModel common\models\searchmodels\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$assetName = TransactionAsset::register ( $this );
$this->title = 'Transactions';
?>



<div class="col-md-12 col-sm-12 col-xs-12 pageheader Mtop15">

<?= $this->title ?>
</div>

<!-- Content -->

<div class="col-md-12 col-sm-12 contentbg">
	<div class="col-md-12 col-sm-12 Mtopbot20">

		<!-- Section head -->
		<fieldset>
			<legend style="font-size: 20px;">Transactions</legend>

			<!-- Filter -->
			<div class="inlinerow Mtop20">
			
				<div class="col-md-2 col-sm-4">
					<div class="inlinerow">
						<strong>Start Date</strong>
					</div>
					<div class="inlinerow">
						<input class="form-control" type="text" value="<?php echo date('Y-m-d', strtotimeNew('-30 days')); ?>" name="startdate" id="startdate" readonly="true" />
					</div>
				</div>
				<div class="col-md-2 col-sm-4">
					<div class="inlinerow">
						<strong>End Date</strong>
					</div>
					<div class="inlinerow">
						<input class="form-control" type="text" value="<?php echo date('Y-m-d');?>" name="enddate" id="enddate" readonly="true" />
					</div>
				</div>
			
				<div class="col-md-2 col-sm-4">
					<div class="inlinerow">
						<strong>Transaction ID</strong>
					</div>
					<div class="inlinerow">
						<input type="text" id="txnid" class="form-control" />
					</div>
				</div>
				<div class="col-md-2 col-sm-4">
					<div class="inlinerow">
						<strong>Membership No.</strong>
					</div>
					<div class="inlinerow">
						<input type="text" id="memberno" class="form-control" />
					</div>
				</div>
				<div class="col-md-2 col-sm-4">
					<div class="inlinerow">
						<strong>Name</strong>
					</div>
					<div class="inlinerow">
						<input type="text" id="name" name="name" class="form-control" />
					</div>
				</div>

				<div class="col-md-2 col-sm-4">
					<div class="inlinerow">
						<strong>Status</strong>
					</div>
					<div class="inlinerow">
						<select id="status" class="form-control">
							<option value=''>All</option>
							<option value='Success' selected>Success</option>
							<option value='Pending'>Pending</option>
							<option value='Failure'>Failed</option>
						</select>
					</div>
				</div>

				

			</div>
			<div class="inlinerow Mtop20">
				<div class="col-md-2 col-sm-3">
					<div class="inlinerow">&nbsp;</div>
					<div class="inlinerow">
						<input type="button" class="btn btn-primary" value="Search"
							id="search" title="Search"/>
					</div>
				</div>
				<div class="col-md-2 col-sm-3">
					<div class="inlinerow">&nbsp;</div>
					<div class="inlinerow">
						<input type="button" class="btn btn-primary" value="Download"
							id="download" title="Download"/>
					</div>
				</div>
			</div>

			<div class="inlinerow Mtop20" id="listview"></div>
			
			<!-- Modal -->
			
			<div id="alertModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close" data-dismiss="modal">&times;</button>
                
            </div>
            <div class="modal-body">
                <p></p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-default" data-dismiss="modal">
                    Close</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>

<!-- Modal closed -->
<?php echo Html::hiddenInput('searchUrl', Url::to('transaction/search'), ['id' => 'searchUrl'])?>
<?php echo Html::hiddenInput('downloadUrl', Url::to('transaction/download'), ['id' => 'downloadUrl'])?>



