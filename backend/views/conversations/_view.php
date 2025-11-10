<?php
use yii\helpers\Html;
use common\models\basemodels\BaseModel;
$date = BaseModel::convertToUserTimezone($model['createddatetime'], yii::$app->user->identity->institution->timezone, true); 
?>
<div id="search-result">
    <div class="inlinerow Mtop20 convrow" 
        data-topicid="<?= Html::encode($model['conversationtopicid'])?>">
        <div class="topicpanel Mtop15">
            <?php echo Html::hiddenInput(
                'admin-view-conversation-Url',
                \Yii::$app->params['ajaxUrl']['admin-view-conversation-Url'],
                [
                    'id'=>'admin-view-conversation-Url'
                ]
                ); ?>
            <div class="topicname">
                 <div class="col-md-6 col-sm-6 col-xs-6">
                    <?= Html::encode($model['subjecttitle'])?>
                </div>
                <div class="col-md-6 col-sm-6 col-xs-6">
                    <?= Html::button('View',['class' => 'btn btn-success btn-view-conversations', 'title' => Yii::t('yii', 'View'),'data-topicid' => Html::encode($model['conversationtopicid']), 'style' =>"float: right;"]); ?>
                </div>
            <div class="col-md-6 col-sm-6 col-xs-6">
                <div class="startedby-label">Started By</div>
                <div class="startedby-value"><?= Html::encode(
                    $model['membername'])?></div>
            </div>
            <div class="col-md-6 col-sm-6 col-xs-6">
                <div class="datetime2"><?= $date->format('jS F Y') ?></div>
                <div class="datetime2"><?= $date->format('g:i a') ?></div>
                <div class="col-md-6 col-sm-6 col-xs-6">
                </div>
            </div>
        </div>
    </div>
</div>
