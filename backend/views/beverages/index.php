<?php

use yii\widgets\Pjax;
use yii\helpers\Url;
use backend\assets\AppAsset;
use backend\components\widgets\FlashResult;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\ListView;
use kartik\time\TimePicker;
use yii\grid\GridView;
use common\models\extendedmodels\ExtendedBevcoProducts;

$this->title = 'Manage Beverages';
$assetName = AppAsset::register($this);
$this->registerJsFile ( $assetName->baseUrl . '/theme/js/Remember.bevco.ui.js', ['depends' => [AppAsset::className ()]]);
?>
<div class="col-md-12 col-sm-12 pageheader Mtop15"><?= $this->title ?></div>
<!-- Content -->
<div class="col-md-12 col-sm-12 contentbg">
   <div class="col-md-12 col-sm-12 Mtopbot20">
      <?= FlashResult::widget(); ?>
      <div class="blockrow">
          <ul class="nav nav-tabs" id="myTab">
            <li class="active"><a data-toggle="tab" href="#manage_products">Products</a></li>
            <li><a data-toggle="tab" href="#manage_category">Category</a></li>
            <li><a data-toggle="tab" href="#manage_settings">settings</a></li>
          </ul>
        <div class="tab-content">
          <div id="manage_products" class="tab-pane fade in active">
              <div class="blockrow">
                <?= Html::a('Add New Product', ['/beverages/add-product'], ['class'=> 'btn btn-primary pull-right']) ?>
              </div>
              <div class="blockrow">
                <?= GridView::widget([
                    'dataProvider' => $product_data_provider,
                    'filterModel' => $product_search_model,
                    'layout' => '{items}</tbody></table><div class="table-btn text-right">{pager}</div>',
                    'rowOptions' => function($model,$key,$index) {
                          if ($model->is_available === ExtendedBevcoProducts::IS_NOT_AVAILABLE) {
                              return ['class' => 'danger'];
                          } else if($model->is_available === ExtendedBevcoProducts::IS_AVAILABLE) {
                              return ['class' => 'success'];
                          }
                      },
                    'tableOptions' => ['class' => 'table table-bordered'],
                    'columns' => [
                        [
                            'attribute' => 'id'                                          
                        ],
                        [
                            'attribute' => 'name',
                        ],
                        [
                            'attribute' => 'price',
                            'content' => function ($model, $key, $index, $column) {
                                return Yii::$app->formatter->asCurrency($model->price, 'INR');
                            }
                        ],
                        [
                            'label' => 'Category',
                            'attribute' => 'category_id',
                            'content' => function ($model, $key, $index, $column) {
                                  return $model->category->name;
                            },
                            'filter' => $product_search_model->getCategories(),
                            'filterInputOptions' => ['prompt' => 'All', 'class' => 'form-control']
                        ],
                        [
                            'label' => 'Updated At',
                            'attribute' => 'updated_at',
                            'content' => function($model, $key, $index, $column) {
                                return date('d F Y H:i:s', strtotimeNew($model->updated_at));
                            }
                        ],
                        [
                          'class' => 'yii\grid\ActionColumn',
                          'header' => 'Actions',
                          'template' => '{edit}',
                          'buttons' => [
                              'edit' => function ($url, $model) {
                                  return Html::a('Edit', ['/beverages/edit-product', 'id' => $model->id], ['class'=> 'btn btn-primary btn-sm manage']);
                              },
                          ],
                        ],
                    ],
                ]); ?>
              </div>
          </div>
          <div id="manage_category" class="tab-pane fade">
              <div class="blockrow">
                <?= Html::a('Add New Category', ['/beverages/create-category'], ['class'=> 'btn btn-primary pull-right']) ?>
              </div>
              <div class="blockrow">
              <div class="panel panel-default">
                  <div class="panel-heading category-title" role="tab">
                    <h4 class="panel-title">
                        <div class="category-link">Categories</div>
                    </h4>
                  </div>
                  <div id="bevco-category-list" class="panel-collapse" role="tabpanel"  aria-labelledby="headingThree">
                      <div class="panel-body">
                          <div class="col-md-12 col-sm-2">
                              <table class="table table-hover food-table" cellspacing="0" cellpadding="0">
                                  <tbody>
                                    <?= ListView::widget(
                                      [
                                         'dataProvider' => $category_data_provider,
                                         'itemView' => '_cat_view',
                                         'layout' => '{items}{pager}'
                                      ]
                                    ); ?>
                                  </tbody>
                              </table>
                          </div>
                      </div>
                  </div>
              </div>
              </div>
          </div>
          <div id="manage_settings" class="tab-pane fade">
             <div class="blockrow">
              <?php $settingsForm = ActiveForm::begin(['id' => 'add-settings', 'action' => ['beverages/add-settings']]) ?>
                  <fieldset>
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
                                    <label class="control-label">Start Time</label>
                                </td>
                                <td>
                                  <?= $settingsForm->field($settings, 'start_time')->widget(TimePicker::classname(), [
                                    'pluginOptions' => [
                                        'showSeconds' => false,
                                        'showMeridian' => true,
                                    ],
                                    'options' => [
                                        'readonly' => true,
                                    ]
                                  ])->label(false);?>
                                </td>
                            </tr>
                            <tr class="custom-data-item">
                                <td>
                                    <label class="control-label">End Time</label>
                                </td>
                                <td>
                                  <?= $settingsForm->field($settings, 'end_time')->widget(TimePicker::classname(), [
                                    'pluginOptions' => [
                                        'showSeconds' => false,
                                        'showMeridian' => true,
                                    ],
                                    'options' => [
                                        'readonly' => true,
                                    ]
                                  ])->label(false);?>
                                </td>
                            </tr>
                            <tr class="custom-data-item">
                                <td>
                                    <label class="control-label">Slot Duration(In Minutes)</label>
                                </td>
                                <td>
                                  <?= $settingsForm->field($settings, 'slot_duration') ->dropDownList(
                                    $settings->getDurations(),
                                    [
                                      'prompt'=>'Please choose duration',
                                  ])->label(false);?>
                                </td>
                            </tr>
                            <tr class="custom-data-item">
                                <td>
                                    <label class="control-label">Sales Per Slot</label>
                                </td>
                                <td>
                                    <?= $settingsForm->field($settings, 'sales_per_slot')->label(false);?>
                                </td>
                            </tr>
                            <tr class="custom-data-item">
                              <td>
                                  <label class="control-label">Interval Unit</label>
                              </td>
                              <td>
                                <?= $settingsForm->field($settings, 'interval_unit') ->dropDownList(
                                    $settings->getIntervalUnit(),['prompt'=>'Please choose interval unit',])->label(false);?>
                              </td>
                          </tr>
                        </tbody>
                    </table>
                  </fieldset>
                  <div class="box-footer">
                    <?= Html::submitButton('Save', ['class' => 'btn btn-primary']) ?>
                  </div>
               <?php ActiveForm::end() ?>
            </div>
          </div>
        </div>
      </div>
   </div>
 </div>