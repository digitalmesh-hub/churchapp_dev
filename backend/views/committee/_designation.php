<?php 
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
?>

<div role="tabpanel" class="tab-pane fade active in" id="committeedesignation">
    <?php $form = ActiveForm::begin() ?>
        <?php echo Html::hiddenInput(
                        'designationid',0,
                        [
                            'id'=>'designationid'
                        ]
                    ); ?>
        <?php echo Html::hiddenInput(
            'admin-save-committee-designation-Url',
            \Yii::$app->params['ajaxUrl']['admin-save-committee-designation-Url'],
            [
                'id'=>'admin-save-committee-designation-Url'
            ]
        ); ?>
        <?php echo Html::hiddenInput(
            'admin-get-committee-designation-Url',
            \Yii::$app->params['ajaxUrl']['admin-get-committee-designation-Url'],
            [
                'id'=>'admin-get-committee-designation-Url'
            ]
        ); ?>
        <?php echo Html::hiddenInput(
            'admin-update-committee-designation-order-Url',
            \Yii::$app->params['ajaxUrl']['admin-update-committee-designation-order-Url'],
            [
                'id'=>'admin-update-committee-designation-order-Url'
            ]
        ); ?>
        <div class="inlinerow Mtop20">
            <div class="col-md-12 col-sm-12">
                <strong>Please enter title for designation  <span style="color: red;">*</span></strong>
            </div>
            <div class="inlinerow Mtop10">
                <div class="col-md-4 col-sm-4">
                    <?= $form->field($designationModel, 'description',['enableClientValidation' => false])->textInput(['maxlength' => true,'class' => 'designation form-control'])->label(false)  ?>
                </div>
                <div class="col-md-4 col-sm-4">
                    <?= Html::button('<span class="glyphicon glyphicon-plus-sign"></span>&nbsp;Add', ['class' => 'btn btn-primary add-committee-designation button-designation-add', 'title' => Yii::t('yii', 'Add'),"id"=>"button-add"]) ?>
                    <?= Html::button('Update', ['class' => 'btn btn-primary add-committee-designation button-designation-update', 'title' => Yii::t('yii', 'Update'),"id"=>"button-update", 'style'=>"display:none"]) ?>
                    <?= Html::button('Clear',['class' => 'btn btn-primary button-clear','title' => Yii::t('yii', 'Clear')]) ?>

                </div>

                <!-- Validation errors -->
                <div class="col-md-6 col-sm-6 nodisplay" id="DesignationError">
                    <div class="alert alert-danger text-center" role="alert" id="DesignationErrorLabel">
                        <strong>Error! Please check again</strong>
                    </div>
                </div>
                <!-- /.Validation errors -->
            </div>
        </div>
    <?php ActiveForm::end(); ?>
    <div class="col-md-12 col-sm-12 Mtop20">
        <?php Pjax::begin(); 
            $GLOBALS['index'] = 0 ;
            $GLOBALS['count'] = $designationCount ;?>
            <?= GridView::widget([
                'dataProvider' => $designationProvider,
                'layout' => '{items}',
                'rowOptions' => function ($model, $key, $index, $grid) {
                    Yii::$app->session['index'] = $index;
                    return ['id' => 'designation_'.$model->designationid,'data-order' => $model->designationorder];
                },
                'columns' => [
                    [
                        'attribute' => 'description',
                        'label'=>'Titles',
                        'value'=>function ($model) { 
                            return $model->description ? $model->description : '';
                        },
                    ],
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'contentOptions'=>['class' => 'text-center','style'=>'vertical-align: middle;'],
                        'headerOptions' => ['class' => 'text-center','style'=>'vertical-align: middle;'],
                        'header' => 'Sort',
                        'template' => '<div class="sortbox">{up}</div> <div class="sortbox">{down}</div>',
                        'buttons' => [
                            'up' => function($url, $model, $key) {
                                if((int)$GLOBALS['index'] != 0){
                                    $GLOBALS['index'] = 
                                        (int)$GLOBALS['index'] + 1;
                                    return Html::button('',
                                        ['class' => 'sortarrow-up designation-up', 'data-order' =>$model->designationorder,
                                            'data-key' => $model->designationid
                                            ,'key' => $key
                                            , 'title' => Yii::t('yii', 'Up')
                                        ]
                                    );
                                }
                                else{
                                    $GLOBALS['index'] = (int)$GLOBALS['index'] +1;
                                }
                            },
                            'down' => function($url, $model, $key) {
                                if($GLOBALS['index'] !=  $GLOBALS['count']){
                                    return Html::button('',
                                        ['class' => 'sortarrow-down designation-down',
                                        'data-order' =>$model->designationorder,
                                        'data-key' => $model->designationid,
                                        'title' => Yii::t('yii', 'Down')]
                                    );
                                }
                            },
                        ],
                    ],
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'contentOptions'=>['class' => 'text-center','style'=>'vertical-align: middle;'],
                        'headerOptions' => ['class' => 'text-center','style'=>'vertical-align: middle;'],
                        'header' => 'Actions',
                        'template' => '{edit}',
                        'buttons' => [
                            'edit' => function($url, $model, $key) {
                                return Html::button('Edit',
                                    ['class' => 'btn btn-primary btn-sm edit-committee-designation', 'data-designationid' => $model->designationid,'data-description' => $model->description, 'title' => Yii::t('yii', 'Edit')
                                    ]
                                );
                            },
                        ],
                    ],
                ],
            ]); ?>
        <?php Pjax::end(); ?>
    </div>
</div>