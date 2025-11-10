
<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\assets\AppAsset;
use backend\components\widgets\FlashResult;

$assetName = AppAsset::register ($this );

?>
<fieldset>
    <?= FlashResult::widget(); ?>
    <?php echo @Html::hiddenInput('homeUrl', Url::home(), array('id'=>'homeUrl'));?> 
    <?php echo Html::hiddenInput(
        'admin-property-categories-Url',
        \Yii::$app->params['ajaxUrl']['admin-property-categories-Url'],
        [
            'id'=>'admin-property-categories-Url'
        ]
    ); ?>
    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
    <!-- Form Name -->
        <!-- Text input-->
        <div class="form-group">
            <label class="col-md-4 control-label text-left">Product Name<span style="color: red;"> *</span></label>
            <div class="col-md-8">
                <?= $form->field($model, 'property')->textInput(['maxlength' => true])->label(false) ?>
            </div>
        </div>
        <!-- dropdown-->
    <div class="form-group">
        <label class="col-md-4 control-label text-left">Category<span style="color: red;"> *</span></label>
        <div class="col-md-8">
            <?= $form->field($model, 'propertycategoryid')
                ->dropDownList($categoryList,
                    [
                     'id' => 'propertycategoryid',
                     'prompt' => 'Select',
                    ]
                )->label(false) ?>
        </div>
    </div>

     <!-- Text input-->
    <div class="form-group">
        <label class="col-md-4 control-label text-left">Price<span style="color: red;">
        *</span></label>
        <div class="col-md-8">
            <?= $form->field($model, 'price')->textInput(['maxlength' => true,'placeholder' => 0])->label(false) ?>
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-4 control-label text-left">Description</label>
        <div class="col-md-8">
            <?= $form->field($model, 'description')->textarea(['rows' => '2', 'cols' => '20'])->label(false) ?>
        </div>
    </div>

    <!-- photo upload-->

    <div class="form-group">
        <label class="col-md-4 control-label ">Thumbnail Image</label>
        <div class="col-md-8">
            <div class="btn btn-default image-preview-input btn-block">
                <span class="glyphicon glyphicon-picture"></span>
                <span class="image-preview-input-title">Browse</span>
                <?= $form->field($model, 'thumbnailimage')->fileInput(['class'=>'thumbnailimage'])->label(false) ?>  
                <!-- rename it -->
            </div>
        </div>
    </div>

     <div class="form-group">
        <label class="col-md-4 control-label Mtop10">Thumbnail Preview</label>
        <div class="col-md-6">
            <!-- Insert image here -->
            <?php
                if(!empty($model['thumbnailimage'])){ ?>
                    <img width="100%" id="thumbnailpreview" class="Mtop10" src="<?php echo Yii::$app->params['imagePath'] . $model['thumbnailimage']?>"/>
                <?php }
                else{ ?>
                    <img width="100%" id="thumbnailpreview" class="Mtop10" src="<?php echo $assetName->baseUrl .'/theme/images/propertythumbnail.jpg' ?>"/>
                <?php
                } 
            ?>
        </div>
    </div>

    <div id="MenuImages" class="row">
        <label class="col-md-4 control-label product-label Mtop10">Product Images</label>
    </div>
    <div class="row">

        <div class="col-xs-6 col-md-3 thumbnail-preview-input Mtop10">
            <a href="#" class="thumbnail">
                <?php
                    if(!empty($propertyImageModel[0])){ ?>
                        <img width="130px" height="120px" id="menuimage_1" src="<?php echo Yii::$app->params['imagePath'] . $propertyImageModel[0]?>" alt="...">
                    <?php } 
                    else { ?>
                        <img width="130px" height="120px" id="menuimage_1" src="<?php echo $assetName->baseUrl .'/theme/images/photoupload.jpg' ?>" alt="...">
                <?php } ?>
            </a>
            <?= $form->field($imageModel, 'image1')->fileInput(['class'=>'image','data-imageId' => 'menuimage_1'])->label(false) ?>
        </div>

        <div class="col-xs-6 col-md-3 thumbnail-preview-input Mtop10">
            <a href="#" class="thumbnail">
                <?php
                    if(!empty($propertyImageModel[1])){ ?>
                        <img width="130px" height="120px" id="menuimage_2" src="<?php echo Yii::$app->params['imagePath'] . $propertyImageModel[1]?>" alt="...">
                    <?php } 
                    else { ?>
                        <img width="130px" height="120px" id="menuimage_2" src="<?php echo $assetName->baseUrl .'/theme/images/photoupload.jpg' ?>" alt="...">
                <?php } ?>
            </a>
            <?= $form->field($imageModel, 'image2')->fileInput(['class'=>'image', 'data-imageId' => 'menuimage_2'])->label(false) ?>
        </div>

        <div class="col-xs-6 col-md-3 thumbnail-preview-input Mtop10">
            <a href="#" class="thumbnail">
                <?php
                    if(!empty($propertyImageModel[2])){ ?>
                        <img width="130px" height="120px" id="menuimage_3" src="<?php echo Yii::$app->params['imagePath'] . $propertyImageModel[2]?>" alt="...">
                    <?php } 
                    else { ?>
                        <img width="130px" height="120px" id="menuimage_3" src="<?php echo $assetName->baseUrl .'/theme/images/photoupload.jpg' ?>" alt="...">
                <?php } ?>
            </a>
            <?= $form->field($imageModel, 'image3')->fileInput(['class'=>'image', 'data-imageId' => 'menuimage_3'])->label(false) ?>
        </div>

        <div class="col-xs-6 col-md-3 thumbnail-preview-input Mtop10">
            <a href="#" class="thumbnail">
                <?php
                    if(!empty($propertyImageModel[3])){ ?>
                        <img width="130px" height="120px" id="menuimage_4" src="<?php echo Yii::$app->params['imagePath'] . $propertyImageModel[3]?>" alt="...">
                    <?php } 
                    else { ?>
                        <img width="130px" height="120px" id="menuimage_4" src="<?php echo $assetName->baseUrl .'/theme/images/photoupload.jpg' ?>" alt="...">
                <?php } ?>
            </a>
            <?= $form->field($imageModel, 'image4')->fileInput(['class'=>'image', 'data-imageId' => 'menuimage_4'])->label(false) ?>
        </div>
    </div>

    <div class="inlinerow Mtop30 text-center">
        <?= Html::submitButton('Save',['class' => 'btn btn-success btn-lg','title' => Yii::t('yii', 'Save')]) ?>

        <?= Html::a('Cancel', ['index'] ,['class' => 'btn btn-default btn-lg', 'title' => Yii::t('yii', 'Cancel')]) ?>
    </div>

    <?php ActiveForm::end(); ?>
</fieldset>