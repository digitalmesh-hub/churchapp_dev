<?php

use yii\helpers\Html;
use yii\grid\GridView;
use backend\assets\AppAsset;
use yii\widgets\ActiveForm;


$this->title = 'Members';

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
    <div class="col-md-12 col-sm-12 pageheader Mtop15">Newly Registered Members</div>
    <div class="col-md-12 col-sm-12 contentbg">
    <div class="col-md-12 col-sm-12 Mtopbot20">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
       // 'filterModel' => $searchModel,
        
        'layout' => '{items}</tbody></table><div class="table-btn text-right">{pager}</div>',
        'tableOptions' => ['class' => 'table table-fixed eventlist'],
        'columns' => [

            [
                'attribute' => 'membership_no',
                'enableSorting' => false,
                'label' => 'Membership #',
            ],
            [
                'attribute' => 'title',
                'enableSorting' => false,
                'label' => 'Title',
                // 'contentOptions' => [],

                'value' => function($data) {
                    return $data->membertitle0['Description'] ? $data->membertitle0['Description']:'' ;
                }
            ],
            [
                'attribute' => 'first_name',
                'enableSorting' => false,
                'label' => 'First Name',
            ],
            [
                'attribute' => 'middle_name',
                'enableSorting' => false,
                'label' => 'Middle Name',
                // 'contentOptions' => ['style' =>'width:13%']
            ],
            [
                'attribute' => 'last_name',
                'enableSorting' => false,
                'label' => 'Last Name',
            ],
            [
                'attribute' => 'mobile',
                'enableSorting' => false,
                'label' => 'Mobile',
            ],
            [
                'attribute' => 'email',
                'enableSorting' => false,
                'label' => 'Email',
            ],
            [
                'attribute' => 'comments',
                'enableSorting' => false,
                'label' => 'Comments',
            ],
        ],
    ]); ?>
    </div>
</div>
</div>      
