<?php

use yii\helpers\Html;
use backend\assets\AppAsset;

$assetName = AppAsset::register($this);
$this->registerJsFile ( $assetName->baseUrl . '/theme/js/Remember.menuManagement.ui.js',[
        'depends' => [
            AppAsset::className ()
    ]
]);

$this->title = 'Food Menu';
$this->params['breadcrumbs'][] = ['label' => 'Extended Propertycategories', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->propertycategoryid, 'url' => ['view', 'id' => $model->propertycategoryid]];
$this->params['breadcrumbs'][] = 'Update';
?>

<!-- Contents -->
<div class="container">
    <div class="row">
        <div class="col-md-12 col-sm-12 pageheader Mtop15">Add Products</div>
        <!-- Content -->
        <div class="col-md-12 col-sm-12 contentbg">
            <div class="col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2 Mtop40">

                <!-- Section head -->
                <div class="blockrow">
                	<?= $this->render('_form',['model' => $model,
                        'imageModel' => $imageModel, 
                        'propertyImageModel' => $propertyImageModel,
                	    'categoryList' => $categoryList]) ?>
            	</div>
            <!-- /. section closed -->
            </div>
        </div>
        <!-- /. Blockrow closed -->
    </div>
</div>
