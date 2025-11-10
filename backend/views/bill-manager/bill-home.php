<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\Url;
use backend\assets\AppAsset;

/* @var $this yii\web\View */
/* @var $searchModel common\models\searchmodels\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$assetName = AppAsset::register($this);
$this->registerJsFile(
    $assetName->baseUrl . '/theme/js/Remember.statement.ui.js',
    [
   'depends' => [
    AppAsset::className()
    ]
    ]
);
$this->title = 'Manage Bills';
?>
<!-- Header -->
<div class="col-md-12 col-sm-12 col-xs-12 pageheader Mtop15">
	<?= $this->title ?>
</div>
<div class="extended-userprofile-index">
<!-- Content -->
<div class="col-md-12 col-sm-12 col-xs-12 contentbg">
	<div class="col-md-12 col-sm-12  col-xs-12">
	
			<!-- Dashboard -->
            <div class="inlinerow Mtop50">
				 <?= Html::a('Debit All',Url::to(['/subscription' ]), ['class'=>'btn btn-success pull-right Mbot30','title' =>'subscription fee']) ?>                 
				<?php Pjax::begin(); ?>    
                 
                 <?= GridView::widget([
				        'dataProvider' => $dataProvider,
				        'filterModel' => $searchModel,
				        'id' => 'gridTable',
                 		'columns' => [
				            ['class' => 'yii\grid\SerialColumn'],
				
				            
				            [
					            	'attribute' => 'FullName',
					            	'label' => 'Full Name',
					            	'value' => function($model){
						            		return $model->firstName . ' ' . $model->middleName. ' ' . $model->lastName;
						        	},
                 			],
                 			[
                 					'attribute' => 'memberno',
                 					'label' => 'Member No.'
							],
				            [
				            		'attribute' => 'member_email',
				            		'label' => 'Email Address',
						    ],
				            [
				            		'attribute' => 'member_mobile1',
				            		'label' => 'Mobile Number',
				            ],
				            [
				            		'header' => 'Action',
				            		'headerOptions' => ['style' => 'width:20%'],
				            		'value' => function($model){
				            		$str1=Html::a('Manage Bills', Url::to(['/bill-manager', 'id' => $model->memberid]), ['class' => 'btn btn-primary', 'title' => 'Manage Bills']);
				            		$str2= Html::button('Statement',['class' => 'btn btn-primary', 'data-id' => $model->memberid,'title' => 'Account Statement','id'=>"statement",'data-target'=>'#transactions','data-toggle'=>'modal']);
				            		return $str1 ."&nbsp;". $str2;
								    },
								    'format' => 'raw',
						    ],
				        ],
				    ]); ?>
				<?php Pjax::end(); ?> 
                
                        
            </div>
            <!-- /. Dashboard -->	
            
	
	</div>
</div>
</div>
<div class="modal fade" id="transactions" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
<div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Transactions</h4>
      </div>
      <div class="modal-body">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>        
      </div>
    </div>
  </div>
</div>
<?php echo Html::hiddenInput('statementUrl', Url::to('bill-manager/statement'), ['id' => 'statementUrl'])?>


