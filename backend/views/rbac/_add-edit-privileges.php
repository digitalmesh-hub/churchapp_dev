<?php 
 
use yii\helpers\Html;
use backend\assets\AppAsset;
use backend\components\widgets\FlashResult;

$this->title = 'Role Privileges';
$assetName = AppAsset::register($this);

?>

<!-- Contents -->
    <div class="container">
        <div class="row">

            <!-- Header -->
            <div class="col-md-12 col-sm-12 pageheader Mtop15">Role Privileges</div>

            <!-- Content -->
            <div class="col-md-12 col-sm-12 contentbg">
                <!-- Copy -->
                <div class = "Mtop10">
                    <?= FlashResult::widget(); ?>
                </div>
                <div class="col-md-12 col-sm-12 ">
                    <div class="blockrow">
                    <legend style="font-size: 20px;"><?= Html::encode($institutionModel->name)?></legend>
                        <div class="blockrow ">
                        <?= Html::beginForm(['rbac/add-role-privileges',
                        'institutionId' =>$institutionModel->id, 
                        'roleId' => $roleId , 
                        'roleGroupId' => $roleGroupId ], 'post', []) ?>
                            <div class="privilage-holder">
                                <div class="col-md-12 Mtop10">
                                    
                                            <?php if(!empty($provider)) { ?>
                                            <table id="institutionprivilege" cellpadding="0" cellspacing="0" class="table table-bordered">
                                             <tbody>
                                                <tr>
                                                <th colspan="2"><?= Html::encode($roleName) ?></th>
                                                </tr>
                                                  <?php  foreach ($provider as $key => $value) { ?>
                                                <tr class="priv">
                                                <td><?= Html::encode($value['privilege']) ?> </td>
                                                <td>
                                                <?= Html::checkbox($value['privilegeid'], (
                                                    in_array(
                                                $value['privilegeid'], $assignedRoles)) ? true : false , ['label' => false]) ?>
                                                </td>
                                                </tr> 
                                             <?php } ?>    
                                        </tbody>
                                        </table> 
                                        <?php } else {  ?>
                                            <div class="alert-div">
                                            <span>It seems like institution has no privileges.Please set it first.</span>  
                                            </div>    
                                        <?php  } ?>
                                </div>
                                <?= Html::hiddenInput('institutionId', $institutionModel->id) ?>
                                 <?= Html::hiddenInput('role', $roleId) ?>
                                 <?php if(!empty($provider)) { ?>
                                <?= Html::submitButton('Save', ['class' => 'submit btn btn-success btn-sm manage','title' => Yii::t('app', 'Save')]) ?>
                                <?php } ?>
                            </div>
                        </div>
                        <?= Html::endForm() ?>
                    </div>
                </div>
                <!-- /.Copy -->

            </div>
        </div>
    </div>
    <!-- Contents closed -->

