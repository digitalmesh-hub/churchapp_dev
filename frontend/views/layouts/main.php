<?php
/* @var $this \yii\web\View */
/* @var $content string */
use yii\helpers\Html;
use frontend\assets\AppAsset;
$assetName = AppAsset::register($this);
?>

<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="<?php echo $assetName->baseUrl; ?>/theme/images/favicon.ico?v=1" type="image/x-icon" />
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>

<div class="mainbg">
       
       <!-- Menu bar -->
       <nav class="navbar navbar-inverse topbarbox">
          <div class="container">
            <div class="navbar-header">
              
              <a class="navbar-brand padtop5" href="https://re-member.co.in/"><img src="https://re-member.co.in/images/menu-logo.png"></a>
            </div>
            <div class="collapse navbar-collapse menuborder">
              <ul class="nav navbar-nav menutop30">
              <div class="maintitle"><a class="navbar-brand padtop5" href="http://re-member.co.in/" style="color: white;">Re-member</a></div>
              </ul>
            </div>
          </div>
        </nav>
       <!-- /.menu bar -->
       
       <!-- Banner -->
       <div class="container Mtop100">
           <div class="inlinerow">
                <div class="row">
                    <?php $this->beginBody() ?>
                    <?= $content ?>
                    <?php $this->endBody() ?>
                </div>
           </div>
       </div>
    </div>
    <!-- footer -->
    <div class="footerstripe">©1999 - <script>new Date().getFullYear()>1999&&document.write("-"+new Date().getFullYear());</script>-2021  Digital Mesh Softech India (P) Limited, Kochi</div>
    <!-- /.Footer -->
</body>
</html>
<?php $this->endPage() ?>
