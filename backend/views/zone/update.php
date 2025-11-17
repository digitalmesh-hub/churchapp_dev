<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\extendedmodels\ExtendedZone */

$this->title = 'Update Zone: ' . $model->zoneid;
$this->params['breadcrumbs'][] = ['label' => 'Zones', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->zoneid, 'url' => ['view', 'id' => $model->zoneid]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="extended-zone-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
