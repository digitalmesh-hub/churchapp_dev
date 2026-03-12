<?php

use yii\helpers\Html;

$this->title = 'Update Sunday Service: ' . date(Yii::$app->params['dateFormat']['viewDateFormat'], strtotime($model->service_date));
$this->params['breadcrumbs'][] = ['label' => 'Sunday Services', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="sunday-service-update">
    <div class="col-md-12 col-sm-12 pageheader Mtop15"><?= Html::encode($this->title) ?></div>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
