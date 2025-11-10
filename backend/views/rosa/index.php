<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use backend\assets\AppAsset;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Extended Rosas';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="extended-rosa-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php Pjax::begin(); ?>

    <p>
        <?= Html::a('Create Extended Rosa', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'rosaid',
            'year',
            'name',
            'mobile',
            'dob',
            //'email:email',
            //'createdby',
            //'createddatetime',
            //'modifiedby',
            //'modifieddatetime',
            //'middlename',
            //'lastname',
            //'countrycode',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
