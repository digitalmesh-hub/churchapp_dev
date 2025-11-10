<?php

use yii\helpers\Url;
use backend\assets\AppAsset;
use backend\components\widgets\FlashResult;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use leparkour\dynamicform\DynamicFormWidget;
use kartik\date\DatePicker;
use yii\web\JsExpression;

$this->title = 'New Booking';
$assetName = AppAsset::register($this);
$this->registerJsFile ( $assetName->baseUrl . '/theme/js/Remember.bevco.ui.js', ['depends' => [AppAsset::className ()]]);
?>
<div class="col-md-12 col-sm-12 pageheader Mtop15"><?= $this->title ?></div>
<!-- Content -->
<div class="col-md-12 col-sm-12 contentbg">
   <div class="col-md-12 col-sm-12 Mtopbot20">
      <?= FlashResult::widget(); ?>
      <?php $form = ActiveForm::begin(['id' => 'bevco-booking-form', 'action' => ['complete-booking']]); ?>
        <div class="blockrow-head"><h2>Booking Details</h2></div>
        <div class="blockrow">
          <div class="col-xs-12 col-sm-6">
              <?= $form->field($model, 'order_date')->widget(DatePicker::classname(), [
                  'options' => ['placeholder' => '--Select Order Date--'],
                  'pluginOptions' => [
                      'autoclose'=>true,
                      'todayHighlight' => true,
                      'format' => 'dd MM yyyy',
                      'startDate' => date('d M Y'),
                      //'endDate' => date('d M Y',strtotime('last day of this month'))
                  ]
              ]); ?>
          </div>
          <div class="col-xs-12 col-sm-6">
              <?= $form->field($model, 'member_name')->widget(\yii\jui\AutoComplete::classname(), [
                  'clientOptions' => [
                      'source' =>  $model->getMembers(),
                      'minLength'=> 2,
                      'change' => new JsExpression("function( event, ui ) {
                         if(!ui.item) {
                            $('#beveragebookingform-member_name').val('').trigger('change.yiiActiveForm');
                            $('#beveragebookingform-member_id').val('').trigger('change.yiiActiveForm');
                         }
                      }"),
                      'select' => new JsExpression("function( event, ui ) {
                          $('#beveragebookingform-member_id').val(ui.item.id).trigger('change.yiiActiveForm');
                      }"),
                      'autoFill' => true
                  ],
                  'options' => [
                      'class' => 'form-control',
                      'placeholder' => 'Type atleast 2 letter'
                  ], 
              ]) ?>
          </div>
        </div>
        <div class="blockrow">
            <?php DynamicFormWidget::begin([
                'widgetContainer' => 'custom_data_container',
                'widgetItem' => '.custom-data-item',
                'widgetBody' => '#custom-data-body',
                'model' => $item[0],
                'formId' => $form->id,
                'formFields' => [
                    'product_id',
                    'quantity'
                ],
                'insertButton' => '.custom-data-clone',
                'deleteButton' => '.custom-data-delete',
                'limit' => 5,
                'min' => 1
            ]); ?>
             <table class="custom_data_container table table-striped table-bordered table-condensed">
                <thead>
                <tr>
                    <th>Category</th>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th><button type="button" class="custom-data-clone btn btn-info btn-xs"><i class="glyphicon glyphicon-plus"></i> Add</button>
                </tr>
                </thead>
                <tbody id="custom-data-body">
                <?php foreach ($item as $i => $b): ?>
                    <tr class="custom-data-item">
                        <td>
                          <?= $form->field($b, "[{$i}]category")->dropDownList(
                            $b->getCategories(),[
                              'prompt'=>' --Select Category--',
                              'onchange'=>'
                                var __this = $(this);
                                $.get( "'.Yii::$app->urlManager->createUrl('beverages/get-products?cat_id=').'"+$(__this).val(), function( data ) {
                                    var cur_el = $(__this).attr("id");
                                    var to_el = "#"+cur_el.substr(0, cur_el.lastIndexOf("-") + 1)+ "product_id";
                                    $(to_el).html(data)
                                });
                              '])->label(false);?> 
                        </td>
                        <td>
                          <?= $form->field($b, "[{$i}]product_id")->dropDownList([], [
                              'prompt'=>' --Select Product--'
                          ])->label(false);?>
                        </td>
                        <td>
                            <?= $form->field($b, "[{$i}]quantity")->label(false) ?>
                        </td>
                        <td class="table-actions">
                            <button type="button" class="custom-data-delete btn btn-danger btn-xs"><i class="fa fa-remove"></i></button>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
          <?php DynamicFormWidget::end(); ?>
        </div>
      <div class="blockrow-head"><h2>Available Slots</h2></div>
      <div class="blockrow slot-panel">
        <span class="bevco-slot-hint">Please choose an order date to populate slots</span>
      </div>
      <div class="blockrow">
        <div class="bevco-booking-error-msg" style="display: none;">
          <div id="booking-error"></div>
        </div>
        <div class="col-xs-12 col-sm-6">
            <?= Html::activeHiddenInput($model, 'slot', ['class' => 'slot-id']) ?>
            <?= Html::activeHiddenInput($model, 'member_id') ?>
            <?= Html::submitButton('Complete Booking', ['class' => 'btn btn-primary btn-lg']) ?>
            <?= Html::a('Cancel', ['/beverages/manage-booking'], ['class'=> 'btn btn-default btn-lg']) ?>
        </div>
      </div>
      <?php ActiveForm::end(); ?>
  </div>
</div>