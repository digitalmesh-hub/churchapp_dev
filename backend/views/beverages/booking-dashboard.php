<?php

use yii\helpers\Url;
use backend\assets\AppAsset;
use backend\components\widgets\FlashResult;
use yii\helpers\Html;
use yii\grid\GridView;
use common\models\extendedmodels\ExtendedBevcoOrder;
use kartik\date\DatePicker;
use yii\web\JsExpression;

$this->title = 'Manage Bookings';
$assetName = AppAsset::register($this);
$this->registerJsFile ( $assetName->baseUrl . '/theme/js/Remember.bevco.ui.js', ['depends' => [AppAsset::className ()]]);
?>
<div class="col-md-12 col-sm-12 pageheader Mtop15"><?= $this->title ?></div>
<!-- Content -->
<div class="col-md-12 col-sm-12 contentbg">
   <div class="col-md-12 col-sm-12 Mtopbot20">
      	<?= FlashResult::widget(); ?>
      	<div class="blockrow">
      	<?= Html::a('Take New Booking', ['/beverages/new-booking'], ['class'=> 'btn btn-primary pull-right']) ?>
      	</div>
       	<div class="blockrow">
         	<?= GridView::widget([
                    'dataProvider' => $orderProvider,
                    'filterModel' => $filterModel,
                    'layout' => '{items}</tbody></table><div class="table-btn text-right">{pager}</div>',
                    'rowOptions' => function($model,$key,$index) {
                        if ($model->status === ExtendedBevcoOrder::STATUS_PLACED) {
                            return ['class' => 'info'];
                        } else if($model->status === ExtendedBevcoOrder::STATUS_COMPLETED) {
                            return ['class' => 'success'];
                        } else if($model->status === ExtendedBevcoOrder::STATUS_CANCELLED) {
                            return ['class' => 'danger'];
                        } else {
                        	return ['class' => 'bevco-order-expired'];
                        }
                      },
                    'tableOptions' => ['class' => 'table table-bordered table-responsive'],
                    'columns' => [
                        [
                            'attribute' => 'id',
                            'contentOptions' => ['style' => 'width:8%;'],                                         
                        ],
                        [
                            'label' => 'Status',
                            'attribute' => 'status', 
                            'content' => function ($model, $key, $index, $column) {
                                  return $model->getStatuses($model->status);
                            },
                            'filter' => $filterModel->getStatuses(),
                            'filterInputOptions' => ['prompt' => 'All', 'class' => 'form-control']
                        ],
                        [
                            'label' => 'Member',
                            'attribute' => 'member_name',
                            'content' => function ($model, $key, $index, $column) {
                                return $model->member->getFullNameWithMemberNo();
                            },
                            'filter' => \yii\jui\AutoComplete::widget([
                                'attribute' => 'member_name',
                                'model' => $filterModel,
                                'clientOptions' => [
                                    'source' => $filterModel->getMembers(),
                                    'minLength' => 2,
                                    'change' => new JsExpression("function( event, ui ) {
                                       if(!ui.item) {
                                          $('#beveragebookingform-member_name').val('').trigger('change.yiiActiveForm');
                                       }
                                    }"),
                                ],
                                'options' => [
                                    'class' => 'form-control',
                                    'placeholder' => 'Type atleast 2 letter'
                                ],
                            ]),
                        ],
                        [
                            'label' => 'Order Date',
                            'attribute' => 'order_date',
                            'content' => function($model, $key, $index, $column){
                                return date('d F Y', strtotimeNew($model->order_date));
                            },
                            'contentOptions' => ['style' => 'width:20%;'],  
                            'filter' => DatePicker::widget([
                                'attribute' => 'order_date',
                                'model' => $filterModel, 
                                'type' => DatePicker::TYPE_COMPONENT_APPEND,
                                'pluginOptions' => [
                                    'autoclose'=>true,
                                    'format' => 'dd-M-yyyy'
                                ]
                            ]),
                        ],
                        [
                          'label' => 'Slot',
                          'attribute' => 'slot_id',
                          'content' => function ($model, $key, $index, $column) {
                              return $model->getFullSlot();
                          },
                        ],
                        [
                          'label' => 'Created At',
                          'attribute' => 'created_at',
                          'format' => 'date',
                          'contentOptions' => ['style' => 'width:20%;'], 
                          'filter' => DatePicker::widget([
                                'attribute' => 'created_at',
                                'model' => $filterModel,
                                'type' => DatePicker::TYPE_COMPONENT_APPEND,
                                'pluginOptions' => [
                                    'autoclose'=>true,
                                    'format' => 'dd-M-yyyy'
                                ]
                          ]),
                        ],
                        [
                          'class' => 'yii\grid\ActionColumn',
                          'header' => 'Actions',
                          'template' => '{view}',
                          'buttons' => [
                              'view' => function ($url, $model) {
                                  return Html::a('View', ['/beverages/booking/', 'id' => $model->id], ['class'=> 'btn btn-info btn-sm manage']);
                              },
                          ],
                        ],
                    ],
                ]); ?>
              </div>
  </div>
</div>