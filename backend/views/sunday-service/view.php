<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\helpers\UserHelper;

$this->title = 'Sunday Service - ' . date(Yii::$app->params['dateFormat']['viewDateFormat'], strtotime($model->service_date));
$this->params['breadcrumbs'][] = ['label' => 'Sunday Services', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sunday-service-view">
    <div class="col-md-12 col-sm-12 pageheader Mtop15"><?= Html::encode($this->title) ?></div>
    <div class="col-md-12 col-sm-12 contentbg">
        <div class="col-md-12 col-sm-12 Mtopbot20">
            <div class="blockrow Mtop20">
                <p>
                    <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
                    <?= Html::a('Delete', ['delete', 'id' => $model->id], [
                        'class' => 'btn btn-danger',
                        'data' => [
                            'confirm' => 'Are you sure you want to delete this item?',
                            'method' => 'post',
                        ],
                    ]) ?>
                    <?= Html::a('Back to List', ['index'], ['class' => 'btn btn-default']) ?>
                </p>

                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        'id',
                        [
                            'attribute' => 'service_date',
                            'value' => date(Yii::$app->params['dateFormat']['viewDateFormat'], strtotime($model->service_date)),
                        ],
                        [
                            'attribute' => 'content',
                            'format' => 'raw',
                            'value' => $model->content,
                        ],
                        [
                            'attribute' => 'active',
                            'value' => $model->active == 1 ? 'Active' : 'Inactive',
                        ],
                        [
                            'attribute' => 'created_at',
                            'value' => date(Yii::$app->params['dateFormat']['viewDateFormat'], strtotime($model->created_at)),
                        ],
                        [
                            'attribute' => 'created_by',
                            'value' => UserHelper::getUserDisplayName($model->created_by),
                        ],
                        [
                            'attribute' => 'updated_at',
                            'value' => $model->updated_at ? date(Yii::$app->params['dateFormat']['viewDateFormat'], strtotime($model->updated_at)) : 'N/A',
                        ],
                        [
                            'attribute' => 'updated_by',
                            'value' => UserHelper::getUserDisplayName($model->updated_by),
                        ],
                    ],
                ]) ?>
            </div>
        </div>
    </div>
</div>
