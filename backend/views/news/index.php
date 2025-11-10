<?php

use yii\helpers\Html;
use yii\grid\GridView;
use backend\assets\AppAsset;
use yii\widgets\ActiveForm;


$this->title = 'News';

$assetName = AppAsset::register($this);
$this->registerJsFile(
    $assetName->baseUrl . '/theme/js/Remember.Event.ui.js',
    [
        'depends' => [
                AppAsset::className()
        ]
    ]
);

echo Html::hiddenInput(
        'news-delete-url',
        \Yii::$app->params['ajaxUrl']['news-delete-url'],
        [
                'id'=>'news-delete-url'
        ]
);

/* @var $this yii\web\View */
/* @var $searchModel common\models\searchmodels\EventSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

?>
<div class="extended-event-index">
    <div class="col-md-12 col-sm-12 pageheader Mtop15">Notice Board Listing</div>
    <div class="col-md-12 col-sm-12 contentbg">
    <div class="col-md-12 col-sm-12 Mtopbot20">
    
     <div class="blockrow Mtop25">
                        <div class="col-md-12">
                             <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
                            <div class="col-md-4 col-md-offset-3 col-sm-4 col-sm-offset-3">
                            <?= $form->field($searchModel, 'searchKeyword')->textInput(['placeholder' => 'Search by Title, Notice Board Date'])->label(false) ?>
                            </div>
                            <div class="col-md-2 col-sm-2">
                                
                                <?= Html::submitButton('Search', ['class' => 'btn btn-primary','title' => 'Search']) ?>
                            </div>
                        </div>
   <?php ActiveForm::end(); ?>
                    </div>

    
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
       // 'filterModel' => $searchModel,
        
        'layout' => '{items}</tbody></table><div class="table-btn text-right">{pager}</div>',
        'tableOptions' => ['class' => 'table table-fixed eventlist'],
        'columns' => [

            [
                'attribute' => 'notehead',
               'enableSorting' => false,
                'label' => 'Title',
                'contentOptions' => ['style' =>'width:28%'],

            ],
            [
                'attribute' => 'activitydate',
                'enableSorting' => false,
                'label' => 'Notice Board Date',
                'contentOptions' => ['style' =>'width:13%'],
                'value' => function($model) {
                    return ($model->activitydate) ? date("d-M-Y", strtotimeNew($model->activitydate))  : "--";
                }
            ],
            [
                'attribute' => 'expirydate',
                'enableSorting' => false,
                'label' => 'Expiry On',
                'contentOptions' => ['style' =>'width:12%'],
                'value' => function($model) {
                    return ($model->expirydate) ? date("d-M-Y", strtotimeNew($model->expirydate)): "--";
                }
                
            ],
            [
                'attribute' => 'createddate',
                'enableSorting' => false,
                'label' => 'Created On',
                'contentOptions' => ['style' =>'width:15%'],
                'value' => function($model) {
                    return ($model->createddate) ? date("d-M-Y H:i", strtotimeNew($model->createddate)): "--";
                }
            ],
            [
                'attribute' => 'publishedon','contentOptions' => ['style' =>'width:11%'],
                'enableSorting' => false,
                'label' => 'Published On',
                'value' => function ($model) {
                        $datePublish = ($model->iseventpublishable && $model->publishedon ) ? date("d-M-Y H:i",strtotimeNew($model->publishedon)) : "--";
                    return $datePublish;
                },
            ],
            
             [
              'contentOptions' => ['class' =>'text-center'],
           
              'class' => 'yii\grid\ActionColumn',
              'header' => 'Actions',
              'headerOptions' => ['style' => '','class' =>'text-center'],
              'template' => '{update}{publish}&nbsp&nbsp{delete}',
              'buttons' => [
                'update' => function ($url, $model) {
                        return Html::a('<button class="btn btn-success btn-sm manage">Manage</button>&nbsp;&nbsp;', $url, [
                                        'title' => Yii::t('app', 'Manage'),
                            ]);
                            },
                'publish' => function ($url, $model) {
                            if ($model->iseventpublishable == 0 && (strtotimeNew($model->expirydate) >= strtotimeNew(date('Y-m-d'))) ) {
                                return Html::button('Publish', [
                                    'class' => 'btn btn-primary btn-sm publish w80', 
                                    'id'    =>'btn-news-publish',
                                    'title' => Yii::t('app', 'Publish'),
                                    'event-id' => $model->id,
                                    'publish' => $model->iseventpublishable,
                                		'activity-date' => $model->activitydate,
                                		'event-title' => $model->notehead,
                                		'familyUnitId' => $model->familyunitid,
                                    	'url' =>'news/publish/',
          
                    			]);
                   		    } else {
                        			return Html::button('Publish', [
	                                    'class' => 'disabled btn btn-primary btn-sm publish w80', 
	                                    'id'    =>'btn-news-publish',
	                                    'publish' => $model->iseventpublishable,
                        				'activity-date' => $model->activitydate,
                        				'event-title' => $model->notehead,
                        				'familyUnitId' => $model->familyunitid,
                        				'event-id' => $model->id,
	                                    'title' => Yii::t('app', 'Publish'),
                                        'disabled' => true
                    				]);        
                    		} 
                },
                 'delete' => function ($url, $model) {
                        return Html::button('Delete' ,[ 'class' => 'btn btn-danger btn-sm delete', 'id' => 'btn-news-delete', 'title' => Yii::t('yii', 'Delete'),'news-id' => $model->id]);
                }

              ],
  
            ],
        ],
    ]); ?>
    </div>
</div>
</div>      
