<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use backend\assets\AppAsset;

$assetName = AppAsset::register($this);
$this->title = 'Sunday Services';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sunday-service-index">
    <div class="col-md-12 col-sm-12 pageheader Mtop15"><?= Html::encode($this->title) ?></div>
    <div class="col-md-12 col-sm-12 contentbg">
        <div class="col-md-12 col-sm-12 Mtopbot20">
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
                                        ],
                                        'pluginOptions' => [
                                            'autoclose' => true,
                                            'format' => 'dd MM yyyy'
                                        ]
                                    ])->label('From Date'); ?>
                                </div>
                                <div class="col-md-6">
                                    <?= $form->field($searchModel, 'service_date_to')->widget(DatePicker::classname(), [
                                        'options' => [
                                            'placeholder' => 'To date',
                                        ],
                                        'pluginOptions' => [
                                            'autoclose' => true,
                                            'format' => 'dd MM yyyy'
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
                                        ],
                                        'pluginOptions' => [
                                            'autoclose' => true,
                                            'format' => 'dd MM yyyy'
                                        ]
                                    ])->label('From Date'); ?>
                                </div>
                                <div class="col-md-6">
                                    <?= $form->field($searchModel, 'created_at_to')->widget(DatePicker::classname(), [
                                        'options' => [
                                            'placeholder' => 'To date',
                                        ],
                                        'pluginOptions' => [
                                            'autoclose' => true,
                                            'format' => 'dd MM yyyy'
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
