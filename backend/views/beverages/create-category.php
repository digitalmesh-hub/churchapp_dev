<?php

use yii\helpers\Url;
use backend\assets\AppAsset;
use backend\components\widgets\FlashResult;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Create Beverage Category';
$assetName = AppAsset::register($this);
$this->registerJsFile ( $assetName->baseUrl . '/theme/js/Remember.bevco.ui.js', ['depends' => [AppAsset::className ()]]);
?>
<div class="col-md-12 col-sm-12 pageheader Mtop15"><?= $this->title ?></div>
<!-- Content -->
<div class="col-md-12 col-sm-12 contentbg">
   <div class="col-md-12 col-sm-12 Mtopbot20">
      <?= FlashResult::widget(); ?>
      <div class="blockrow">
          <?php $form = ActiveForm::begin(['id' => 'add-category']) ?>
                  <fieldset>
                  <?= $form->field($category_model, 'name')->label('Name <span style="color: red;">*</span>'); ?>
                  <?= $form->field($category_model, 'description')->textArea(['maxlength' => true])->label(); ?>
                  <table class="custom_data_container table table-striped table-bordered table-condensed">
                      <thead>
                      <tr>
                          <th>Field</th>
                          <th>Value</th>
                      </tr>
                      </thead>
                      <tbody id="custom-data-body">
                          <tr class="custom-data-item">
                              <td>
                                  <label class="control-label">Maximum Allowed Per Interval</label>
                              </td>
                              <td>
                                <?= $form->field($category_custom_data_model, 'maximum_allowed_per_interval')->label(false);?>
                              </td>
                          </tr>
                      </tbody>
                  </table>
                   </fieldset>
              <div class="box-footer">
                  <?= Html::submitButton('Save', ['class' => 'btn btn-primary']) ?>
                  <?= Html::a('Cancel', ['/beverages'], ['class'=> 'btn btn-default']) ?>
               </div>
               <?php ActiveForm::end() ?>
      </div>
   </div>
 </div>