<?php

/* @var $this \yii\web\View */
/* @var $content string */

use backend\assets\AppAsset;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\Url;
use yii\web\User;
use yii\helpers\ArrayHelper;

$assetName = AppAsset::register($this);
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
    <title><?= Html::encode($this->title) ?></title>
	<?php $this->head() ?>
  </head>

  <body>
  <?php $this->beginBody() ?>
  <?php echo @Html::hiddenInput('homeUrl', Url::home(), array('id'=>'homeUrl'));?>
  <?php echo @Html::hiddenInput('controller', yii::$app->controller->id, array('id'=>'controller'));?>
  	<!-- overlay -->
	<div class="overlay" style="display: none;">
	   <div class="loader"><img width="150px" src="<?php echo $assetName->baseUrl; ?>/theme/images/loader.gif"/></div>
	</div>
	<!-- /.overlay -->
	<!-- Header -->
	<div class="header">
	     <div class="container">
	     <div class="row">
	          <div class="col-md-12 col-sm-12 Mtop10">
	               <div class="col-md-4 col-sm-5"><a href="<?php echo Url::to(['/account/home']); ?>"><img src="<?php echo $assetName->baseUrl; ?>/theme/images/remember-logo.png" title="Go home"/></a></div>
	               <div class="col-md-4 col-sm-5 pull-right text-right accountinfo">
	               		<?php if (Yii::$app->user->isGuest) : ?>
		               		<a href="<?php echo Url::to(['/account/login']); ?>">Login</a>
	               		<?php else : ?>
	                   		<a href="javascript:void(0);">User :  <?php echo Html::encode(Yii::$app->user->identity->userprofile->firstname. ' ' . Yii::$app->user->identity->userprofile->lastname); ?></a>
	                   		&nbsp;&nbsp;
	                   		<a href="<?php echo Url::to(['/account/logout']); ?>"><img id="LogOut" src="<?php echo $assetName->baseUrl; ?>/theme/images/logout-icon.png" title="Logout"></a>
	                   <?php endif; ?>
	                   
	               </div>
	          </div>
	     </div>
	     </div>
	</div>
	<!-- Header closed -->
	
	<!-- Menubar -->
<div class="menubar">
    <div class="container">
          <nav class="navbar navbar-inverse">
                <div class="navbar-header">
                  <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                  </button>                  
                </div>
             <div id="navbar" class="navbar-collapse collapse">
                  <ul class="nav navbar-nav">
                    <li>
                       <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Admins<span class="caret"></span></a>
                       <ul class="dropdown-menu" role="menu">
                        <li class="active"><a href="<?=Url::to(['/account/list-admin/'])?>">List Admins</a></li>
                        <li><a href="<?=Url::to(['/account/manage-admin/'])?>">Manage Admins</a></li>
                      </ul>
                    </li>
                    <li>
                       <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Institution<span class="caret"></span></a>
                       <ul class="dropdown-menu" role="menu">
                        <li><a href="<?=Url::to(['/institution/list-institution/'])?>">Institutions </a></li>                   
                        <li><a href="<?=Url::to(['/institution/create-institution/'])?>">Add Institution </a></li>                                          
                      </ul>
                    </li>
                    <li><a href="<?=Url::to(['/login-report/index/'])?>">Login Report</a></li>
                    <li><a href= "<?=Url::home(true)?>help/index.html" target="_blank">Help</a></li>
                  </ul>
                </div><!--/.nav-collapse -->
            
            </nav> 
    </div>
</div>
	<!-- Contents -->
	<div class="container">
	     <div class="row">
	          <?= $content ?> 
	     </div>
	</div>
  <?php $this->endBody() ?>
  </body>
  
</html>
<?php $this->endPage() ?>