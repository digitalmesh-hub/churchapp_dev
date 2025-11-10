<?php

use yii\helpers\Html;
use backend\assets\AppAsset;

$assetName = AppAsset::register($this);
?>
<div class="inlinerow Mtop10">
    <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true"> 
    <?php if($categoryList){
        foreach($categoryList as $model){ ?>
            <div class="panel panel-default">
                <div class="panel-heading  category-title" role="tab" id="headingOne">
                    <h4 class="panel-title"><a role="button" data-toggle="collapse" data-parent="#accordion" href="#<?= Html::encode($model['propertycategoryid'])?>" aria-expanded="true" aria-controls="collapseOne">
                        <div class="category-link"><?= Html::encode($model['category'])?><i class="glyphicon glyphicon-menu-down pull-right"></i></div>
                    </a></h4>
                </div>

                <div id="<?= Html::encode($model['propertycategoryid'])?>" class="panel-collapsed collapse" role="tabpanel" aria-labelledby="headingOne">
                    <div class="panel-body">
                        <table cellpadding="0" cellspacing="0" class="table table-hover food-table">

                        <?php if($propertyList){
                            $count = 0 ;
                            foreach($propertyList as $property){
                                if($property['propertycategoryid'] == $model['propertycategoryid']){ 
                                    $count = $count + 1;?>
                                    <tr>
                                        <td width="35%" class= "break-text"><?= Html::encode($property['property'])?></td>
                                        <td width="30%"><?= '₹ '.yii::$app->MoneyFormat->decimalWithComma($property['price'])?></td>
                                        <td width="15%">
                                            <?php if($property['active'] == 1){ ?>
                                                <?= Html::button('UnAvailable',['class' => 'btn btn-warning btn-sm manage avail', 'title' => Yii::t('yii', 'UnAvailable'),'data-propertyId' => Html::encode($property['propertyid']),'data-isActive' =>$property['active'], 'style'=>'width: 60%;']); 
                                                ?>
                                            <?php }
                                            else{ ?>
                                            <?= Html::button('Available',['class' => 'btn btn-success btn-sm manage avail', 'title' => Yii::t('yii', 'Available'),'data-propertyid' => Html::encode($property['propertyid']),'data-isActive' =>$property['active'],'style'=>'width: 60%;']); 
                                                ?>
                                            <?php } ?> 
                                            <?= Html::a('Edit',['update','id'=> Html::encode($property['propertyid'])],
                                                ['class' => 'btn btn-primary btn-sm manage pull-right', 'title' => Yii::t('yii', 'Edit'),'data-propertyid' => Html::encode($property['propertyid'])]); 
                                            ?>
                                        </td>
                                    </tr>
                                <?php }
                            } 
                            if($count == 0){ ?>
                                <tr><td colspan="2" class="text-center">No records</td></tr>
                            <?php }
                        }
                        else{?>
                            <tr><td colspan="2" class="text-center">No records</td></tr>
                        <?php } ?>
                        </table>
                    </div>
                </div>
            </div>
        <?php }
    } 
    else{ ?>
        <div class="text-center">No records</div>
    <?php } ?>
    </div>
</div>