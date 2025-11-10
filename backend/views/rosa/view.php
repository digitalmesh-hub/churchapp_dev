<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\extendedmodels\ExtendedRosa */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Extended Rosas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="extended-rosa-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->rosaid], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->rosaid], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'rosaid',
            'year',
            'name',
            'mobile',
            'dob',
            'email:email',
            'createdby',
            'createddatetime',
            'modifiedby',
            'modifieddatetime',
            'middlename',
            'lastname',
            'countrycode',
        ],
    ]) ?>

</div>
