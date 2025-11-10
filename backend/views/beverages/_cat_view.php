<?php
use yii\helpers\Html;
use yii\helpers\HtmlPurifier;
use yii\helpers\Url;
use backend\assets\AppAsset;
$assetName = AppAsset::register($this);
?>           
<tr>
    <td><?= HtmlPurifier::process($model->name) ?></td>
    <td width="20%" class="text-right"> 
    <div class="btn-group">
    <?php if($model->is_available == 1){ ?>
        <?= Html::button('Set as Inactive',['class' => 'btn btn-success btn-sm manage', 'id' => 'deactivate-bevco-cat', 'title' => Yii::t('yii', 'UnAvailable'),'data-url' => Url::toRoute(['beverages/category-deactivate', 'id' =>$model->id])]); 
        ?>
    <?php } else{ ?>
        <?= Html::button('Set as Active',['class' => 'btn btn-warning btn-sm manage', 'id' => 'activate-bevco-cat', 'title' => Yii::t('yii', 'Available'),'data-url' => Url::toRoute(['beverages/category-activate', 'id' =>$model->id])])?>
    <?php } ?>
    </div> 
    <div class="btn-group">
        <?= Html::a('Edit', ['/beverages/edit-category', 'id' => $model->id], ['class'=> 'btn btn-primary btn-sm manage pull-right']) ?>
    </div>
    </td>
</tr