<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\extendedmodels\ExtendedEvent */
$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Extended Events', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="extended-event-view">
    <h1><?= Html::encode($this->title) ?></h1>
    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
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
            'id',
            'institutionid',
            'notehead',
            'notebody',
            'activitydate',
            'createddate',
            'activatedon',
            'noteurl',
            'eventtype',
            'createduser',
            'venue',
            'time',
            'expirydate',
            'rsvpavailable',
            'modifiedby',
            'modifieddatetime',
            'iseventpublishable',
            'familyunitid',
        ],
    ]) ?>
</div>
