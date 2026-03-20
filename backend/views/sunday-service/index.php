<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use backend\assets\AppAsset;
use backend\components\widgets\FlashResult;

$assetName = AppAsset::register($this);
$this->title = 'Sunday Services';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sunday-service-index">
    <div class="col-md-12 col-sm-12 pageheader Mtop15"><?= Html::encode($this->title) ?></div>
    <div class="col-md-12 col-sm-12 contentbg">
        <div class="col-md-12 col-sm-12 Mtopbot20">
            <?= FlashResult::widget(); ?>
            <div class="blockrow Mtop20">
                <div class="inlinerow Mtop10">
                    <div class="col-md-12">
                        <?= Html::a('Create Sunday Service', ['create'], ['class' => 'btn btn-success']) ?>
                    </div>
                </div>

                <!-- Date Range Filters -->
                <div class="inlinerow Mtop20">
                    <?php $form = ActiveForm::begin(['method' => 'get', 'action' => ['index']]); ?>
                    <div class="row">
                        <div class="col-md-6">
                            <h5><strong>Service Date Range</strong></h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <?= $form->field($searchModel, 'service_date_from')->widget(DatePicker::classname(), [
                                        'options' => [
                                            'placeholder' => 'From date',
                                            'id' => 'service-date-from'
                                        ],
                                        'pluginOptions' => [
                                            'autoclose' => true,
                                            'format' => 'dd MM yyyy',
                                            'todayHighlight' => true
                                        ]
                                    ])->label('From Date'); ?>
                                </div>
                                <div class="col-md-6">
                                    <?= $form->field($searchModel, 'service_date_to')->widget(DatePicker::classname(), [
                                        'options' => [
                                            'placeholder' => 'To date',
                                            'id' => 'service-date-to'
                                        ],
                                        'pluginOptions' => [
                                            'autoclose' => true,
                                            'format' => 'dd MM yyyy',
                                            'todayHighlight' => true
                                        ]
                                    ])->label('To Date'); ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h5><strong>Created Date Range</strong></h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <?= $form->field($searchModel, 'created_at_from')->widget(DatePicker::classname(), [
                                        'options' => [
                                            'placeholder' => 'From date',
                                            'id' => 'created-at-from'
                                        ],
                                        'pluginOptions' => [
                                            'autoclose' => true,
                                            'format' => 'dd MM yyyy',
                                            'todayHighlight' => true
                                        ]
                                    ])->label('From Date'); ?>
                                </div>
                                <div class="col-md-6">
                                    <?= $form->field($searchModel, 'created_at_to')->widget(DatePicker::classname(), [
                                        'options' => [
                                            'placeholder' => 'To date',
                                            'id' => 'created-at-to'
                                        ],
                                        'pluginOptions' => [
                                            'autoclose' => true,
                                            'format' => 'dd MM yyyy',
                                            'todayHighlight' => true
                                        ]
                                    ])->label('To Date'); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 text-right">
                            <?= Html::submitButton('Filter', ['class' => 'btn btn-primary']) ?>
                            <?= Html::a('Reset', ['index'], ['class' => 'btn btn-default']) ?>
                        </div>
                    </div>
                    <?php ActiveForm::end(); ?>
                </div>

                <div class="inlinerow Mtop20">
                    <?php Pjax::begin(); ?>
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => null,
                        'tableOptions' => ['class' => 'table table-striped table-bordered'],
                        'columns' => [
                            ['class' => 'yii\grid\SerialColumn'],

                            [
                                'attribute' => 'service_date',
                                'label' => 'Service Date',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    return date(Yii::$app->params['dateFormat']['viewDateFormat'], strtotime($model->service_date));
                                },
                            ],
                            [
                                'attribute' => 'content',
                                'label' => 'Content Preview',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    return \yii\helpers\StringHelper::truncate(strip_tags($model->content), 100);
                                },
                            ],
                            [
                                'attribute' => 'active',
                                'label' => 'Status',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    return $model->active == 1 ? 
                                        '<span class="label label-success">Active</span>' : 
                                        '<span class="label label-danger">Inactive</span>';
                                },
                            ],
                            [
                                'attribute' => 'created_at',
                                'label' => 'Created',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    return date(Yii::$app->params['dateFormat']['viewDateFormat'], strtotime($model->created_at));
                                },
                            ],

                            [
                                'class' => 'yii\grid\ActionColumn',
                                'template' => '{view} {update} {delete}',
                                'buttons' => [
                                    'view' => function ($url, $model) {
                                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, [
                                            'title' => 'View',
                                            'class' => 'btn btn-sm btn-primary',
                                        ]);
                                    },
                                    'update' => function ($url, $model) {
                                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [
                                            'title' => 'Update',
                                            'class' => 'btn btn-sm btn-warning',
                                        ]);
                                    },
                                    'delete' => function ($url, $model) {
                                        return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
                                            'title' => 'Delete',
                                            'class' => 'btn btn-sm btn-danger',
                                            'data' => [
                                                'confirm' => 'Are you sure you want to delete this item?',
                                                'method' => 'post',
                                            ],
                                        ]);
                                    },
                                ],
                            ],
                        ],
                    ]); ?>
                    <?php Pjax::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs("
    // Function to parse date string 'dd MM yyyy' to timestamp for comparison
    function parseDateToTimestamp(dateStr) {
        if (!dateStr || dateStr.trim() === '') return null;
        var parts = dateStr.trim().split(' ');
        if (parts.length !== 3) return null;
        
        var months = ['January', 'February', 'March', 'April', 'May', 'June', 
                      'July', 'August', 'September', 'October', 'November', 'December'];
        var monthIndex = months.indexOf(parts[1]);
        if (monthIndex === -1) return null;
        
        var day = parseInt(parts[0]);
        var year = parseInt(parts[2]);
        var date = new Date(year, monthIndex, day);
        return date.getTime();
    }

    // Service Date Range Validation
    $('#service-date-from').on('change', function() {
        var fromDateStr = $(this).val();
        var toDateStr = $('#service-date-to').val();
        
        if (fromDateStr && toDateStr) {
            var fromTime = parseDateToTimestamp(fromDateStr);
            var toTime = parseDateToTimestamp(toDateStr);
            
            if (fromTime && toTime && fromTime > toTime) {
                $('#service-date-to').val('');
                alert('From date cannot be greater than To date. To date has been cleared.');
            }
        }
    });

    $('#service-date-to').on('change', function() {
        var fromDateStr = $('#service-date-from').val();
        var toDateStr = $(this).val();
        
        if (fromDateStr && toDateStr) {
            var fromTime = parseDateToTimestamp(fromDateStr);
            var toTime = parseDateToTimestamp(toDateStr);
            
            if (fromTime && toTime && toTime < fromTime) {
                $('#service-date-from').val('');
                alert('To date cannot be less than From date. From date has been cleared.');
            }
        }
    });

    // Created Date Range Validation
    $('#created-at-from').on('change', function() {
        var fromDateStr = $(this).val();
        var toDateStr = $('#created-at-to').val();
        
        if (fromDateStr && toDateStr) {
            var fromTime = parseDateToTimestamp(fromDateStr);
            var toTime = parseDateToTimestamp(toDateStr);
            
            if (fromTime && toTime && fromTime > toTime) {
                $('#created-at-to').val('');
                alert('From date cannot be greater than To date. To date has been cleared.');
            }
        }
    });

    $('#created-at-to').on('change', function() {
        var fromDateStr = $('#created-at-from').val();
        var toDateStr = $(this).val();
        
        if (fromDateStr && toDateStr) {
            var fromTime = parseDateToTimestamp(fromDateStr);
            var toTime = parseDateToTimestamp(toDateStr);
            
            if (fromTime && toTime && toTime < fromTime) {
                $('#created-at-from').val('');
                alert('To date cannot be less than From date. From date has been cleared.');
            }
        }
    });
", \yii\web\View::POS_READY);
?>
