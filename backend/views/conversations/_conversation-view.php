<?php
use yii\helpers\Html;
use backend\assets\AppAsset;
use common\models\basemodels\BaseModel;
$assetName = AppAsset::register($this); 

$titleDate = BaseModel::convertToUserTimezone($subjectTitle[0]['createddatetime'], yii::$app->user->identity->institution->timezone, true);
?>

<div class="inlinerow Mtop20">
	<div class="topic-detailpanel">
		<div class="topicname"><?= Html::encode($subjectTitle[0]['subjecttitle'])?></div>
		<div class="row">
			<div class="topicimg">
				<img
					src="<?= Html::encode($subjectTitle[0]['memberimage'])? Yii::$app->params['imagePath'].Html::encode($subjectTitle[0]['memberimage']):'/Member/default-user.png'?>">
			</div>
			<div class="col-md-8 col-sm-8 col-xs-8 pad0">
				<div class="startedby-label">Started by</div>
				<div class="startedby-value"><?= Html::encode($subjectTitle[0]['username'])?></div>
			</div>
		</div>
	</div>
	<div class="topic-content">
		<p class="word-wrap"><?= Html::encode($subjectTitle[0]['subject'])?></p>
		<div class="datetime"><?= $titleDate->format('jS F Y') ?>&nbsp;<?= $titleDate->format('g:i a') ?> </div>
	</div>
</div>

<?php
if ($dataProvider) {
    foreach ($dataProvider as $model) {
        ?>
<div class="replypanel Mtop10">
	<div class="row">
		<div class="topicimg">
			<img
				src="<?= Yii::$app->params['imagePath'].Html::encode($model['memberimage'])?>">
		</div>
		<div class="col-md-8 col-sm-8 col-xs-8 pad0">
			<div class="replyby-label">Replied by</div>
			<div class="repliedby-value"><?= Html::encode($model['username'])?></div>
		</div>
	</div>
	<div class="replycontent">
		<p class="word-wrap"><?= Html::encode($model['conversation'])?> </p>
		<?php $date = BaseModel::convertToUserTimezone($model['createddatetime'], yii::$app->user->identity->institution->timezone, true); ?>
		<div class="datetime"><?= $date->format('jS F Y') ?>&nbsp;<?= $date->format('g:i a') ?> </div>
	</div>
</div>
<?php
    }
} else { ?>
<div class="row text-center">No Chat Record found</div>
<?php
}
?>

