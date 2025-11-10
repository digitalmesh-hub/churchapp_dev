<?php 

use yii\helpers\Html;
use backend\assets\AppAsset;
use backend\assets\DatePickAsset;
use yii\widgets\ActiveForm;
use yii\web\View;
use yii\grid\GridView;
use yii\widgets\Pjax;

$assetName = AppAsset::register($this);

$this->registerJsFile ( $assetName->baseUrl . '/theme/js/Remember.subscription.ui.js', [
    'depends' => [DatePickAsset::className ()]
]);

$this->title = 'Monthly Subscription Fee';
?>

<?= Html::hiddenInput('memberCount', $memberCount, [ 'id' => 'member-count']);
?>

<!-- Header -->
<div class="col-md-12 col-sm-12 col-xs-12 pageheader Mtop15">
	<?= $this->title ?>
</div>
<div class="extended-userprofile-index">
<!-- Content -->
<div class="col-md-12 col-sm-12 col-xs-12 contentbg">
    <?php
        if(!empty(Yii::$app->session->getAllFlashes())) { ?>
            <div class="Mtop20">
                <?php
                    foreach (Yii::$app->session->getAllFlashes() as $key => $message) {
                        echo '<div class="flash text-center alert alert-' . $key . '">' . $message . '</div>';
                }?>
            </div>
    <?php } ?>
    <div class="col-md-4 col-sm-12  col-xs-12">
		<!-- Dashboard -->
        <div class="inlinerow Mtop50">
        <?php 
        $form = ActiveForm::begin(
            [
            'options'=> ['id' => 'sub-form',
            'validateOnSubmit' => true,
            ],
            ]
        );
        ?>
        <?= $form->field($model, 'transactionDate')->textInput(['class' =>'subscripitonTransactionDate form-control calendaricon w70p']);?>
        <?= $form->field($model, 'amount')->textInput(['id'=>'input-amount']);?>
        <?= $form->field($model, 'description')->textInput();?>
        <?= Html::a('Cancel', ['/bill-manager/home'], ['class' => 'btn btn-danger','title' =>'Cancel']) ?>
        <?= Html::submitButton('Save', [
            'class' => 'submit btn btn-primary',
            'id' => 'btnFormSubmit', 'title' =>'Save Subscription Fee'
         ]
         ) ?>
        <?php
        ActiveForm::end();
        ?>
        </div>
    </div>
    <div class="inlinerow Mtop50">

  <?php Pjax::begin([]);?>
    <?= GridView::widget(
    [
    'dataProvider' => $dataProvider,
    //'filterModel' => $searchModel,
    'tableOptions' => ['class' => 'table table-bordered'],
    'columns' => [
        [
                'class' => 'yii\grid\SerialColumn',
                'options' => ['style' => 'width:6%;']
            ],
      [
        'attribute' => 'transactionDate',
        'options' => ['style' => 'width:14%'],
        'value' => function ($data) {
            return date(
                yii::$app->params['dateFormat']['viewDateFormat'],
                strtotimeNew($data->transactionDate)
            );
        }
    ],
    [
        'attribute' => 'description',
        'options' => ['style' => 'width:28%'],
        
    ],
    [
        'attribute' => 'amount',
        'options' => ['style' => 'width:16%'],
        'value' => function ($data) {
            return yii::$app->MoneyFormat->decimalWithComma($data->amount);
        }
    ],
    [
        'attribute' => 'status',
        'options' => ['style' => 'width:14%'],
        'value' => function ($data) {
            return yii::$app->params['subscriptionFee']['status'][$data->status];
        }
    ],
      [
                'class' => 'yii\grid\ActionColumn',
                'options' => ['style' => 'width:5%;'],
                'header' => 'Action',
                'template'=>'{delete}',
                'buttons' => [
                    'delete' => function ($url, $model) {
                                $url=Yii::$app->getUrlManager()->createUrl(
                                    ['subscription/delete', 'id'=>$model['id']]
                                );
                                $button = \yii\helpers\Html::a(
                                    '<span class="glyphicon glyphicon-trash"></span>',
                                    $url,
                                    ['title' => Yii::t('yii', 'Delete'), 'data-pjax' => '0',
                                    'id' =>'btn-delete'
                                ]
                                );
                        if ($model->status == 0) {
                                    return $button;
                        } else {
                            return'';
                        }
                    },
                ],
                
            ],
    ],
     ]
);
     ?>
     <?php Pjax::end(); ?>
</div>
</div>
</div>


