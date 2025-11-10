<?php

use backend\assets\AppAsset;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use backend\components\widgets\FlashResult;
use yii\web\View;
$assetName = AppAsset::register($this); 

$this->title = 'Bills';

?>
<!-- Header -->
<div class="col-md-12 col-sm-12 col-xs-12 pageheader Mtop15">
    <?= $this->title ?>
</div>
 <!-- Content -->
        <div class="col-md-12 col-sm-12 contentbg">
                <div class="col-md-12 col-sm-12 Mtopbot20">
                	 <?= FlashResult::widget();?>
                	 <fieldset>
                	 	<div class="Mtop15">
                        <legend style="font-size: 20px;">Failed records <?= Html::a('Back', ['bill/index'], ['class' => 'btn btn-danger btn-sm manage rightalign Mtop-5 back-btn']) ?></legend></div>
                      
<div style="overflow-x:auto;">
<table id="tblBills" class="table table-bordered table-responsive">
    <thead>
        <tr>
 			<?php  if (!empty($heading)) { 
 			 		foreach ($heading as $head) { ?>
 						<th class="text-center"><?= Html::encode($head)?></th>
 			<?php } } ?>
        </tr>
    </thead>
 <tbody id="billsbody">
 	
        <?php if (!empty($data)) { 
                foreach ($data as $value) { ?>
                    <tr>
                    <td class="text-center error-td-border">
                    	<?= Html::encode($value['data']['mem_alias']) ?>
                    </td>
                    <td class="text-center"><?= Html::encode($value['data']['mem_name']) ?>
                    </td>
                    <td class="text-center">
					<?= Html::encode($value['data']['mem_type'] ) ?>
                    </td>
                    <td class="text-center">
                    	<?= Html::encode($value['data']['mem_order']) ?>
                    </td>
                    <td class="text-center">
                    	<?= Html::encode($value['data']['bill_date']) ?>
                    </td>
                    <td class="text-center">
						<?= Html::encode($value['data']['item_no']) ?>
                    	</td>
                    <td class="text-center">
                    	<?= Html::encode($value['data']['item_desc']) ?>
                    	</td>
                    <td class="text-center">
                    	<?= Html::encode($value['data']['trans_date']) ?>
                    </td>
                    <td class="text-center">
                    	<?= Html::encode($value['data']['trans_desc']) ?>
                    	</td>
                    <td class="text-center">
                    	<?= Html::encode($value['data']['db_amt']) ?>
                    </td>
                    <td class="text-center">
                    	<?= Html::encode($value['data']['cr_amt']) ?>
                    </td>
                    <tr><td colspan="11" class="text-center" style="color:red;"><?=$value['message']?></td></tr>
                    </tr>
               <?php }
           } else { ?>
            <tr>
            <td colspan="7" class="text-center"><span style="color: red;">No Records Found</span> </td>
            </tr>
        <?php } ?>
    </tbody>
</table>
</div>
</fieldset>
</div>
</div>