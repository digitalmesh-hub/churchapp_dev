<?php

use yii\helpers\Html;
use backend\assets\AppAsset;

$assetName = AppAsset::register($this);
$this->registerJsFile ( $assetName->baseUrl . '/theme/js/Remember.menuManagement.ui.js',[
        'depends' => [
            AppAsset::className ()
    ]
]);
?>

<div class="inlinerow">
    <div class="col-md-12 col-sm-12 Mtop10">
        <?= Html::button('Add New Category', ['class' => 'btn btn-primary pull-right', 'title' => Yii::t('yii', 'Add New Category'), 'id' => 'add-category'])?>
    </div>
    <div class="inlinerow Mtop10">
        <div class="panel panel-default">
            <div class="panel-heading category-title" role="tab" id="Div1">
                <h4 class="panel-title">
                    <div class="category-link">Categories </div> 
                </h4>
            </div>
            <div id="Div2" class="panel-collapse" role="tabpanel"  aria-labelledby="headingThree">
                <div class="panel-body">
                    <table class="table table-hover food-table" cellspacing="0" cellpadding="0">
                        <tbody>
                            <?php
                            if($categoryList){ 
                                foreach($categoryList as $model){ ?>
                                    <tr>
                                        <td><?= Html::encode($model['category'])?></td>
                                        <td width="20%">
                                            <?php if($model['active'] == 1){ ?>
                                            <?= Html::button('Unavailable',['class' => 'btn btn-warning btn-sm manage catavail', 'title' => Yii::t('yii', 'UnAvailable'),'data-propertyCategoryId' => Html::encode($model['propertycategoryid']),'data-isActive' =>$model['active'], 'style'=>'width: 60%;']); 
                                                ?>
                                            <?php }
                                            else{ ?>
                                            <?= Html::button('Available',['class' => 'btn btn-success btn-sm manage catavail', 'title' => Yii::t('yii', 'Available'),'data-propertyCategoryId' => Html::encode($model['propertycategoryid']),'data-isActive' =>$model['active'],'style'=>'width: 60%;']); 
                                                ?>
                                            <?php } ?> 
                                            <?= Html::button('Edit',
                                                ['class' => 'btn btn-primary btn-sm manage pull-right edit-category', 'title' => Yii::t('yii', 'Edit'),'data-propertyCategoryId' => $model['propertycategoryid'],
                                                    'data-category'=>$model['category']]); 
                                            ?>
                                        </td>
                                    </tr> 
                                <?php }
                            }
                            else{?>
                                <tr>
                                    <td colspan="2" class="text-center">No records</td>
                                </tr>                      
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal" id="AddCategoryPopup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="H2">Category</h4>
                <div class="alert alert-danger error-msg" 
                    style="display:none">
                    <strong>Error!</strong> Some error occured while processing.
                </div>
            </div>
            <div class="modal-body">
                <form class="form-horizontal">
                    <fieldset>
                        <!-- Text input-->
                        <div class="form-group">
                            <label class="col-md-3 control-label text-left">Category Name <span style="color: red;">*</span></label>
                            <div class="col-md-9">
                                <input type="text" maxlength="50" id="foodcategory" placeholder="" class="form-control input-md" value="">
                            </div>
                            <label class="col-md-3 control-label text-left"></label>
                            <div class="col-md-9 nodisplay" id="ErrorDiv">
                                <div class="alert alert-danger text-center" id="ErrorMessageLabel" role="alert"><strong>Category Name Cannot be blank</strong></div>
                            </div>
                        </div>
                    </fieldset>
                </form>
                <!-- Validation errors -->
                
                <!-- /.Validation errors -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal" title="Close">Close</button>
                <button id="save-category" type="button" class="btn btn-primary" title="Save">Save</button>
            </div>
        </div>
    </div>
</div>