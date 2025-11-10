<?php
use yii\helpers\Html;
use backend\assets\LoginAsset;

$assetName = LoginAsset::register($this);

/* @var $this yii\web\View */
/* @var $content string */
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="en">
  	<head>
	    <meta charset="utf-8">
	    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	    <meta name="viewport" content="width=device-width, initial-scale=1">
	    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    	<meta name="description" content="">
    	<meta name="author" content="">
    	<link rel="shortcut icon" href="<?php echo $assetName->baseUrl; ?>/theme/images/favicon.ico?v=1" type="image/x-icon" />
	    <?= Html::csrfMetaTags() ?>
	    <title>Re-Member</title>
	    <?php $this->head() ?>
	</head>
	<body class="loginbg">
		<?php $this->beginBody() ?>
	    	<?= $content ?>
		<?php $this->endBody() ?>
	</body>
</html>
<?php $this->endPage() ?>