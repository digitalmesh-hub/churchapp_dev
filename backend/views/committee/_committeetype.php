<?php 
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
?>

<div role="tabpanel" class="tab-pane fade active in" id="committeegroup">
    <?php $form = ActiveForm::begin() ?>
        <?php echo Html::hiddenInput(
                        'committeeGroupId',0,
                        [
                            'id'=>'committeeGroupId'
                        ]
                    ); ?>
        <?php echo Html::hiddenInput(
            'admin-save-committee-type-Url',
            \Yii::$app->params['ajaxUrl']['admin-save-committee-type-Url'],
            [
                'id'=>'admin-save-committee-type-Url'
            ]
        ); ?>
        <?php echo Html::hiddenInput(
            'admin-activate-deactivate-committee-type-Url',
            \Yii::$app->params['ajaxUrl']['admin-activate-deactivate-committee-type-Url'],
            [
                'id'=>'admin-activate-deactivate-committee-type-Url'
            ]
        ); ?>
        <?php echo Html::hiddenInput(
            'admin-get-committee-type-Url',
            \Yii::$app->params['ajaxUrl']['admin-get-committee-type-Url'],
            [
                'id'=>'admin-get-committee-type-Url'
            ]
        ); ?>
        <?php echo Html::hiddenInput(
            'admin-update-committee-type-order-Url',
            \Yii::$app->params['ajaxUrl']['admin-update-committee-type-order-Url'],
            [
                'id'=>'admin-update-committee-type-order-Url'
            ]
        ); ?>
        <div class="inlinerow Mtop20">
            <div class="col-md-12 col-sm-12">
                <strong>Please enter committee name <span style="color: red;">*</span></strong>
            </div>
            <div class="inlinerow Mtop10">
                <div class="col-md-4 col-sm-4">
                    <?= $form->field($model, 'description',['enableClientValidation' => false])->textInput(['maxlength' => true,'class' => 'description form-control'])->label(false)  ?>
                </div>
                <div class="col-md-4 col-sm-4">
                    <?= Html::button('<span class="glyphicon glyphicon-plus-sign"></span>&nbsp;Add', ['class' => 'btn btn-primary add-committee-type add-type', 'title' => Yii::t('yii', 'Add'),"id"=>"button-add"]) ?>
                    <?= Html::button('Update', ['class' => 'btn btn-primary add-committee-type update-committee-type', 'title' => Yii::t('yii', 'Update'),"id"=>"button-update", 'style'=>"display:none"]) ?>
                    <?= Html::button('Clear',['class' => 'btn btn-primary', 'id'=>'button-clear','title' => Yii::t('yii', 'Clear')]) ?>

                </div>
                <!-- Validation errors -->
                <div class="col-md-6 col-sm-6 nodisplay" id="ErrorDiv">
                    <div class="alert alert-danger text-center" id="ErrorMessageLabel" role="alert">
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
            $GLOBALS['count'] = $count ;?>
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'layout' => '{items}',
                'rowOptions' => function ($model, $key, $index, $grid) {
                    Yii::$app->session['index'] = $index;
                    return ['id' => 'group_'.$model->committeegroupid,'data-order' => $model->order];
                },
                'columns' => [
                    [
                        'attribute' => 'description',
                        'label'=>'Committee',
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
                                        ['class' => 'sortarrow-up commiteeup', 'data-order' =>$model->order,
                                            'data-key' => $model->committeegroupid
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
                                        ['class' => 'sortarrow-down commiteedown',
                                        'data-order' =>$model->order,
                                        'data-key' => $model->committeegroupid,
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
                        'template' => '{edit} {status}',
                        'buttons' => [
                            'edit' => function($url, $model, $key) {
                                return Html::button('Edit',
                                    ['class' => 'btn btn-primary btn-sm edit-committee-type', 'data-committeegroupid' => $model->committeegroupid,'data-description' => $model->description, 'title' => Yii::t('yii', 'Edit')
                                    ]
                                );
                            },
                            'status'=>function($url, $model, $key) { 
                                        if($model->active == 1){
                                            return Html::button('Deactivate',['class' => 'btn btn-danger btn-sm activate w80 btn-type-active', 'title' => Yii::t('yii', 'Deactivate'),'data-committeegroupid' => $model->committeegroupid,
                                                'data-active'=>$model->active,
                                                'style' => 'width:50%']);
                                        }
                                        else{
                                            return Html::button('Activate',['class' => 'btn btn-success btn-sm activate w80 btn-type-active', 'title' => Yii::t('yii', 'Activate'),
                                                'data-committeegroupid' => $model->committeegroupid,
                                                'data-active'=>$model->active,
                                                'style' => 'width:50%;']
                                            );
                                        }
                                    }
                        ],
                    ],
                ],
            ]); ?>
        <?php Pjax::end(); ?>
    </div>
</div>
