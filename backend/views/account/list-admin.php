<?php 

use yii\grid\GridView;
use yii\widgets\Pjax;
use backend\assets\AppAsset;
use common\components\admin\widgets\ListAdminWidget;

/* @var $this yii\web\View */
/* @var $searchModel common\models\searchmodels\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$assetName = AppAsset::register($this);

$this->title = 'Admin List';
?>
<!-- Header -->
<div class="col-md-12 col-sm-12 col-xs-12 pageheader Mtop15">
    <?= $this->title ?>
</div>
<div class="extended-userprofile-index">
<!-- Content -->
<div class="col-md-12 col-sm-12 col-xs-12 contentbg">
    <div class="col-md-12 col-sm-12  col-xs-12">
            <!-- Dashboard -->
            <div class="inlinerow Mtop50">
               <?= ListAdminWidget::widget() ?>    
            </div>
            <!-- /. Dashboard --> 
    </div>
</div>
</div>