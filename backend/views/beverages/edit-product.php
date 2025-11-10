<?php

use yii\helpers\Url;
use backend\assets\AppAsset;
use backend\components\widgets\FlashResult;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Edit Beverage Product';
$assetName = AppAsset::register($this);
$this->registerJsFile ( $assetName->baseUrl . '/theme/js/Remember.bevco.ui.js', ['depends' => [AppAsset::className ()]]);
?>
<div class="col-md-12 col-sm-12 pageheader Mtop15"><?= $this->title ?></div>
<!-- Content -->
<div class="col-md-12 col-sm-12 contentbg">
   <div class="col-md-12 col-sm-12 Mtopbot20">
      <?= FlashResult::widget(); ?>
      <div class="col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2 Mtop40">
          <div class="blockrow">
              <?php $form = ActiveForm::begin(['id' => 'edit-product']) ?>
                  <fieldset>
                        <?= $form->field($model, 'category_id') ->dropDownList(
                            $model->getCategories(),['prompt'=>' --Select category--']);?>
                        <?= $form->field($model, 'name'); ?>
                        <?= $form->field($model, 'description')->textArea(['maxlength' => true]); ?>
                        <?= $form->field($model, 'price')->textInput(['maxlength' => 10]); ?>
                        <?= $form->field($model, 'is_available')->checkbox() ?>
                  </fieldset>
                  <div class="box-footer">
                      <?= Html::submitButton('Update', ['class' => 'btn btn-primary']) ?>
                      <?= Html::a('Cancel', ['/beverages'], ['class'=> 'btn btn-default']) ?>
                  </div>
              <?php ActiveForm::end() ?>
          </div>
      </div>
   </div>
 </div>