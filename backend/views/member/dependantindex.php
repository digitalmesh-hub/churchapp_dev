<?php

use yii\helpers\Html;
use yii\grid\GridView;
use backend\assets\AppAsset;
$assetName = AppAsset::register($this);

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

?>
<div class="extended-dependant-index">
 <input type ="hidden" id ="dependantIds" name="dependantIds" value = "<?= $dependantIds ?>">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
          //  ['class' => 'yii\grid\SerialColumn'],
        		[
        		'attribute' => 'dependantimage',
        				'label' => 'Dependent Name',
        				'format' =>'raw',
        				'enableSorting' => false,
        						'value' => function ($data) {
        						//return ($data->firstName) ?  $data->firstName : '';
        		
        		$image  = $data['dependantimage']?$data['dependantimage']:"/Member/default-user.png";
        				
        			return '<span><img class="dependantpic" src="'.Yii::$app->params['imagePath'].$image.'"></span><span>'.$data['dependanttitle'].' '.$data['dependantname'].'</span>';
        		},
        			//'contentOptions' => ['style' => 'width:25%']
        		],
        		[
        		'attribute' => 'dob',
        				'label' => 'DOB',
        				'format' =>'raw',
        				'enableSorting' => false,
        				'value' => function ($data) {
        			
        				$date = '';
        				if (!empty($data['dob'])){
        					$date  = date(yii::$app->params['dateFormat']['viewDateFormat'], strtotimeNew($data['dob']));
        					
        				}else{
        					
        					$date = "Not Available";
        				}
        		
        			return $date ;
        		},
        				//	'contentOptions' => ['style' => 'width:25%']
        			],
           
            'relation',
            [
            'attribute' => 'dependantspouseimage',
            		'label' => 'Spouse Name',
            		'format' =>'raw',
            		'enableSorting' => false,
            		'value' => function ($data) {
            		//return ($data->firstName) ?  $data->firstName : '';
            	$image  = $data['dependantspouseimage']?$data['dependantspouseimage']:"/Member/default-user.png";
            
            	return '<span><img class="dependantpic" src="'.Yii::$app->params['imagePath'].$image.'"></span><span>'.$data['spousetitle'].' '.$data['spousename'].'</span>';
        		},
            		//	'contentOptions' => ['style' => 'width:25%']
            	],
          
            [
            'attribute' => 'spousedob',
            'label' => 'Spouse DOB',
            'format' =>'raw',
            		'enableSorting' => false,
            				'value' => function ($data) {
            				 
            				$date = '';
            				if (!empty($data['spousedob'])){
            					$date  = date(yii::$app->params['dateFormat']['viewDateFormat'], strtotimeNew($data['spousedob']));
            					 
            					}else{
            					 
            							$date = "Not Available";
            					}
            
            					return $date ;
            					},
            				//	'contentOptions' => ['style' => 'width:25%']
            					],
           
            [
            'attribute' => 'weddinganniversary',
            		'label' => 'Wedding Anniversary',
            				'format' =>'raw',
            				'enableSorting' => false,
            				'value' => function ($data) {
            				 
            				$date = '';
            				if (!empty($data['weddinganniversary'])){
            					$date  = date(yii::$app->params['dateFormat']['viewDateFormat'], strtotimeNew($data['weddinganniversary']));
            
            					}else{
            
            					$date = "Not Available";
            					}
            
            							return $date ;
            					},
            				//	'contentOptions' => ['style' => 'width:25%']
            					],
        		
        		[
        		'class' => 'yii\grid\ActionColumn',
        				'header' => 'Action',
        				'template'=>'{update}&ensp;{delete}',
        						'buttons' => [
                   
        		'update' => function ($url, $model) {
        				return Html::button('<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>',
        			 ['class' => 'update updateDependant btn btn-warning btn-sm',
                        		'title' => 'update',
        						'data-id' =>  $model['id'],
        						]);
        				},
        						'delete' => function ($url, $model) {
        								return  Html::button('<span class="glyphicon glyphicon-trash"></span>', ['class' => 'delete btn btn-danger btn-dependant-delete btn-sm',
                    'id' =>'btn-dependant-delete','title' => Yii::t('yii', 'delete'),
        		                    		'data-dependant-id' => $model['id']]);
        				},
        				],
        		
        				],
        ],
    ]); ?>
</div>
<div class = 'dependantFrom'>
<?= $memberDependantForm ?>
</div>