<?php

use yii\helpers\Html;
use yii\grid\GridView;
use backend\assets\AppAsset;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $status string|null */

$assetName = AppAsset::register($this);
$this->registerJsFile(
    $assetName->baseUrl . '/theme/js/Remember.whatsappSettings.ui.js',
    ['depends' => [AppAsset::className()]]
);

echo Html::hiddenInput('base-url', $assetName->baseUrl, ['id' => 'base-url']);
echo Html::hiddenInput('whatsapp-resend-message-Url', \Yii::$app->params['ajaxUrl']['whatsapp-resend-message-Url'], ['id' => 'whatsapp-resend-message-Url']);

$this->title = 'WhatsApp Message Log';
?>
<div class="col-md-12 col-sm-12 contentbg Mtop15">

    <h1><?= Html::encode($this->title) ?></h1>
    <p><?= Html::a('&larr; Back to Settings', ['whatsapp/settings']) ?></p>

    <div class="col-md-12 col-sm-12 Mtop10">
        <?= Html::a('All', ['whatsapp/message-log'], ['class' => 'btn btn-xs ' . (empty($status) ? 'btn-primary' : 'btn-default')]) ?>
        <?= Html::a('Sent', ['whatsapp/message-log', 'status' => 'sent'], ['class' => 'btn btn-xs ' . ($status === 'sent' ? 'btn-primary' : 'btn-default')]) ?>
        <?= Html::a('Failed', ['whatsapp/message-log', 'status' => 'failed'], ['class' => 'btn btn-xs ' . ($status === 'failed' ? 'btn-primary' : 'btn-default')]) ?>
        <?= Html::a('Queued', ['whatsapp/message-log', 'status' => 'queued'], ['class' => 'btn btn-xs ' . ($status === 'queued' ? 'btn-primary' : 'btn-default')]) ?>
    </div>

    <div class="col-md-12 col-sm-12 Mtop20">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'tableOptions' => ['class' => 'table'],
            'columns' => [
                ['attribute' => 'memberid', 'label' => 'Member ID'],
                ['attribute' => 'recipient_type', 'label' => 'Recipient'],
                ['attribute' => 'event_type', 'label' => 'Event'],
                ['attribute' => 'event_date', 'label' => 'Event Date'],
                ['attribute' => 'phone', 'label' => 'Phone'],
                [
                    'attribute' => 'status',
                    'format' => 'raw',
                    'value' => function (\common\models\extendedmodels\ExtendedWhatsappMessageLog $model): string {
                        $class = ['sent' => 'label-success', 'failed' => 'label-danger', 'queued' => 'label-default'];
                        return '<span class="label ' . ($class[$model->status] ?? 'label-default') . '">' . Html::encode(ucfirst($model->status)) . '</span>';
                    },
                ],
                ['attribute' => 'error_detail', 'label' => 'Error'],
                ['attribute' => 'sent_at', 'label' => 'Sent At'],
                [
                    'label' => '',
                    'format' => 'raw',
                    'value' => function (\common\models\extendedmodels\ExtendedWhatsappMessageLog $model): string {
                        if ($model->status === 'sent') {
                            return '';
                        }
                        return Html::button('Resend', [
                            'class' => 'btn btn-xs btn-default btn-whatsapp-resend-message',
                            'data-id' => $model->id,
                        ]);
                    },
                ],
            ],
        ]) ?>
    </div>

</div>
