
<?php
use yii\helpers\Html;
use yii\helpers\HtmlPurifier;
use yii\helpers\Url;
use backend\assets\AppAsset;
$assetName = AppAsset::register($this);
?>
             
<tr style="display: table-row;">
    <td width="10%"> 
     <?php if (!empty($model->institutionlogo)) { ?>
               <?= Html::img(yii::$app->params['imagePath'].$model->institutionlogo,
               ['alt' => 'Institution logo','style' => "width: 150px; height: 150px;"]) ?>
                <?php } else { ?>
                    <img src="<?= $assetName->baseUrl; ?>/theme/images/institution-icon-grey.png" style = "width: 150px; height: 150px;"/>
                <?php } ?>
    </td> 
    <td width="20%"><?= HtmlPurifier::process($model->name) ?></td>
    <td width="24%"><?= HtmlPurifier::process(($model->active === 1) ? 'Active Institution' :'Inactive Institution') ?></td>
    <td width="22%" class="text-right"> 
    <?= Html::a('Roles', ['rbac/roles', 'id' => $model->id], ['class' => 'btn btn-warning btn-sm manage', 'title' => Yii::t('yii', 'Roles')]) ?> 
    <?= Html::a('Privileges', ['rbac/privileges', 'id' => $model->id], ['class' => 'btn btn-primary btn-sm manage', 'title' => Yii::t('yii', 'Privileges')]) ?>
    <?= Html::a('Edit', ['institution/edit', 'id' => $model->id], [
    'class' => 'btn btn-success btn-sm manage', 'title' => Yii::t('yii', 'Edit') ]) ?>
    <?= Html::button(($model->active == 1) ? 'Deactivate' : 'Activate' ,[
            'class' => ($model->active == 1 ) ? 'btn btn-danger btn-sm activate w80' : 'btn btn-info btn-sm activate w80',
            'id' => ($model->active == 1) ? 'btn-institution-deactivate' : 'btn-institution-activate', 
            'title' => ($model->active ==1 ) ? Yii::t('yii', 'Deactivate') : Yii::t('yii', 'Activate'),
            'data-institution-id' => $model->id
        ]
    );
    ?>
    
    </td>
</tr>