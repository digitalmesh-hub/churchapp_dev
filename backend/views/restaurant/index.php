<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;
use backend\assets\AppAsset;

$this->title = 'Menu Management';
$this->params['breadcrumbs'][] = $this->title;

$assetName = AppAsset::register($this);
$this->registerJsFile ( $assetName->baseUrl . '/theme/js/Remember.menuManagement.ui.js',[
        'depends' => [
            AppAsset::className ()
    ]
]);

?>
<div class="container">
    <div class="row">
        <div class="col-md-12 col-sm-12 pageheader Mtop15">Menu Management</div>

        <!-- Content -->
        <div class="col-md-12 col-sm-12 contentbg">
            <div class="col-md-12 col-sm-12 Mtopbot20">
                <!-- Section head -->
                <div class="blockrow">

                    <!-- Tabs -->
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="active">
                            <a href="#products" aria-controls="Feedbacks" role="tab" data-toggle="tab" id="foodproducts">Products</a>
                        </li>
                        <li role="presentation">
                            <a href="#categories" aria-controls="FeedbackSettings" role="tab" data-toggle="tab" id="foodcategories">Category</a>
                        </li>
                  
                    </ul>

                    <!-- Tab panes -->
                    <div class="tab-content tabcontentborder">
                        <!-- Food Item Tab -->
                        <div role="tabpanel" class="tab-pane fade active in" id="products">
                            <div class="col-md-12 col-sm-12 Mtop10">
                                <?= Html::a('Add A Product', ['create'], ['class' => 'btn btn-primary pull-right', 'title' => Yii::t('yii', 'Add A Product')])?>
                                <?php echo @Html::hiddenInput('homeUrl', Url::home(), array('id'=>'homeUrl'));?> 
                                <?php echo Html::hiddenInput(
                                    'admin-available-unavailable-Url',
                                    \Yii::$app->params['ajaxUrl']['admin-available-unavailable-Url'],
                                    [
                                        'id'=>'admin-available-unavailable-Url'
                                    ]
                                ); ?>
                                <?php echo Html::hiddenInput(
                                    'admin-property-categories-Url',
                                    \Yii::$app->params['ajaxUrl']['admin-property-categories-Url'],
                                    [
                                        'id'=>'admin-property-categories-Url'
                                    ]
                                ); ?>
                                <?php echo @Html::hiddenInput('institutionId', $institutionId, ['id'=>'institutionId']);
                                ?>
                                <?php echo Html::hiddenInput(
                                    'admin-save-category-Url',
                                    \Yii::$app->params['ajaxUrl']['admin-save-category-Url'],
                                    [
                                        'id'=>'admin-save-category-Url'
                                    ]
                                ); ?>
                                <?php echo @Html::hiddenInput('propertyCategoryId', 0, ['id'=>'propertyCategoryId']);
                                ?>
                                <?php echo Html::hiddenInput(
                                    'admin-available-unavailable-category-Url',
                                    \Yii::$app->params['ajaxUrl']
                                    ['admin-available-unavailable-category-Url'],
                                    [
                                        'id'=>'admin-available-unavailable-category-Url'
                                    ]
                                ); ?>
                                <?php echo Html::hiddenInput('base-url',
                                    $assetName->baseUrl,
                                    [
                                        'id'=>'base-url'
                                    ]
                                );
                                ?>
                            </div>
                            <?= $this->render('_content', [
                                'categoryList' => $categoryList, 
                                'propertyList' => $propertyList,
                            ]) ?>     
                        </div>
                        <!-- /Food Item Tab -->
                        <!-- Food Category Tab -->
                        <div role="tabpanel" class="tab-pane fade" id="categories">
                            <?= $this->render('_category', [
                                'categoryList' => $categoryAll,
                            ]) ?>
                        </div>
                        <!-- / Food Category Tab -->
                    </div>
                    <!-- /.Tabs -->
                </div>
                <!-- /. section closed -->
            </div>
        </div>
        <!-- /. Blockrow closed -->
    </div>
</div>
<!-- Contents closed -->