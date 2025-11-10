<?php

use yii\helpers\Html;

?>
<?php if (Yii::$app->session->hasFlash('error')) { ?>
    <div class="flash alert alert-danger alert-dismissable">
     <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
        <ul>
        <?php foreach(Yii::$app->session->getFlash('error') as $flash): ?>
             <?php if (is_array($flash)) { ?>
                  <?php foreach ($flash as $key => $value): ?>
                       <li><?= $value ?></li>
                  <?php endforeach ?>
            <?php } else { ?>
                <li><?= $flash ?></li>
               <?php } ?>
            
        <?php endforeach ?>
        </ul>
    </div>
<?php } elseif(Yii::$app->session->hasFlash('success')) { ?>
 <div class="flash alert alert-success alert-dismissable">
  <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
        <ul>
        <?php foreach(Yii::$app->session->getFlash('success') as $flash): ?>
            <li><?= $flash ?></li>
        <?php endforeach ?>
        </ul>
    </div>

  <?php  } ?>
