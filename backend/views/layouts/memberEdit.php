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
       <div class="loader"><img width="50px" src="<?php echo $assetName->baseUrl; ?>/theme/images/loader.gif"/></div>
    </div>
    <!-- /.overlay -->
    <!-- Header -->
    <div class="header">
         <div class="container">
         <div class="row">
              <div class="col-md-12 col-sm-12 Mtop10">
                   <div class="col-md-4 col-sm-5"><a href="#"><img src="<?php echo $assetName->baseUrl; ?>/theme/images/remember-logo.png" title="Go home"/></a></div>
                   <div class="col-md-4 col-sm-5 pull-right text-right accountinfo">
                        
                       
                   </div>
              </div>
         </div>
         </div>
    </div>
    <!-- Header closed -->
    
    <!-- Menubar -->

    <!-- Modal -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body">
                <div class="content-div"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary Mtop10" data-dismiss="modal" title="Cancel">Cancel</button>
                <button type="button" class="btn btn-primary Mtop10" data-dismiss="modal" title="OK">OK</button>
            </div>
        </div>
    </div>
</div>
    <!-- modal end --> 

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