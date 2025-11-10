<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;
use backend\assets\AppAsset;
use backend\components\widgets\FlashResult;
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Title';
$this->params['breadcrumbs'][] = $this->title;

$assetName = AppAsset::register ( $this );
$this->registerJsFile ( $assetName->baseUrl . '/theme/js/Remember.title.ui.js',[
        'depends' => [
            AppAsset::className ()
    ]
] );
?> 
<div class="container">
    <div class="row">
        <!-- Header -->
        <div class="col-md-12 col-sm-12 pageheader Mtop15">Title</div>
          
        <!-- Content -->
        <div class="col-md-12 col-sm-12 contentbg">
            <div class="col-md-12 col-sm-12 Mtopbot20">
                <div class="inlinerow Mtop20">
                    <div class="col-md-12 col-sm-12">
                        <?= FlashResult::widget(); ?>
                        <?php $form = ActiveForm::begin() ?>
                            <?php echo @Html::hiddenInput('homeUrl', Url::home(), array('id'=>'homeUrl'));?> 
                            <?php echo Html::hiddenInput(
                                'admin-title-deactivation-Url',
                                \Yii::$app->params['ajaxUrl']['admin-title-deactivation-Url'],
                                [
                                    'id'=>'admin-title-deactivation-Url'
                                ]
                                ); ?>
                            <?php echo Html::hiddenInput(
                                    'admin-title-activation-Url',
                                    \Yii::$app->params['ajaxUrl']['admin-title-activation-Url'],
                                    [
                                        'id'=>'admin-title-activation-Url'
                                    ]
                                ); ?>
                            <div class="row">
                                <strong>Please enter Title</strong>
                            </div>
                            <div class="row">
                                <div class="col-md-4 col-sm-4">
                                    <div class="row Mtop10">
                                        <?= $form->field($model, 'Description')->textInput(['maxlength' => true])->label(false)  ?>
                                    </div>
                                </div>
                                <div class="col-md-4 col-sm-4">      
                                       
                                    <?= Html::submitButton('Add', ['class' => 'btn btn-primary glyphicon glyphicon-plus-sign Mtop10 btn-add-title', 'title' => Yii::t('yii', 'Add')]) ?>
                                    <?= Html::a('Clear',['index'],['class' => 'btn btn-primary glyphicon Mtop10', 'title' => Yii::t('yii', 'Clear')]) ?>
                                       
                                </div>
                            </div>
                        <?php ActiveForm::end(); ?>
                    </div>
                    <?php Pjax::begin(); ?>
                        <?= GridView::widget([
                            'dataProvider' => $dataProvider,
                            'layout' => '{items} </tbody></table><div class="blockrow Mtop25 text-right">{pager}</div>',
                            'columns' => [
                                ['attribute'=>'Description',
                                    'value'=>'Description',
                                    'contentOptions'=>['style'=>'width: 75%;']],
                                [
                                'class' => 'yii\grid\ActionColumn',
                                'contentOptions'=>['class' => 'text-center','style'=>'vertical-align: middle;'],
                                'headerOptions' => ['class' => 'text-center','style'=>'vertical-align: middle;'],
                                'header' => 'Actions',
                                'template' => '{edit} {deactivate}',
                                    'buttons' => [
                                        'edit'=>function($url, $model, $key) {
                                            return Html::a('Edit', ['view-title', 'id'=>$model->TitleId],['class' => 'btn btn-primary', 'title' => Yii::t('yii', 'Edit')]);
                                        },
                                        'deactivate'=>function($url, $model, $key) { 
                                            if($model->active == 1){
                                                return Html::button('Deactivate',['class' => 'btn btn-danger btn-deactivate', 'title' => Yii::t('yii', 'Deactivate'),'data-titleId' => $model->TitleId,
                                                    'style' => 'width:50%']);
                                            }
                                            else{
                                                return Html::button('Activate',['class' => 'btn btn-success btn-activate', 'title' => Yii::t('yii', 'Activate'),
                                                    'data-titleId' => $model->TitleId,
                                                    'style' => 'width:50%;']
                                                );
                                            }
                                        }
                                    ],
                                ]
                            ]
                        ]); ?>
                    <?php Pjax::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>





