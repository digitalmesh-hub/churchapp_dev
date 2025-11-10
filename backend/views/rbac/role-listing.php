<?php 

use yii\helpers\Html;
use yii\helpers\Url;
use backend\assets\AppAsset;
use yii\widgets\ActiveForm;
use backend\components\widgets\FlashResult;

$this->title = 'Role Listing';
$assetName = AppAsset::register($this);

?>
<!-- Contents -->
<div class="container">
    <div class="row">
        <div class="col-md-12 col-sm-12 pageheader Mtop15">Roles</div>
            <!-- Content -->
            <div class="col-md-12 col-sm-12 contentbg">
                <div class = "Mtop10">
                        <?= FlashResult::widget(); ?>
                </div>
                <div class="col-md-12 col-sm-12 Mtopbot20">
                    <fieldset>
                        <legend style="font-size: 20px;">
                        <?= Html::encode($institutionModel->name) ?>
                        <?= Html::a('Add/Edit Category', ['manage-category', 'id' => $institutionModel->id], ['class' => 'btn btn-success btn-sm manage rightalign Mtop-5',
                            'title' => yii::t('app', 'Add or Edit Category')
                         ]) ?>    
                        </legend>
                         <div class="blockrow Mtop20">
                            <div class="blockrow Mtop20">
                            <?php if (!empty($dataArray)) { ?>
                                <?php foreach ($dataArray as $key => $value):?>
                                    <div class="groupbox Mtop20">
                                    <div class="rolesgroup">
                                        <h3><?= Html::encode($key)?></h3>
                                    </div>
                                    <div class="inlinerow Mtop10">
                                  <?php foreach ($value as $key => $value):?>
                                        <div class="col-md-6 col-sm-12 col-xs-12">
                                            <div class="role-holder">
                                                <div class="list-head">
                                                    <h3><?= Html::encode($value['roleCategoryName']) ?></h3>
                                            <?= Html::a(Html::img( $assetName->baseUrl.'/theme/images/edit-icon.png', ['alt' => 'My logo']), ['rbac/add-edit-role','institutionId' => $value['InstitutionID'],'roleCategoryId' => $value['RoleCategoryId'],
                                               'roleGroupId' => $value['RoleGroupId']], ['class' => 'rightalign'])
                                            ?>
                                                </div>
                                            <?php foreach ($allotedRoles as $akey => $avalue): ?>

                                               <?php  if (($avalue['RoleGroupId'] == $value['RoleGroupId']
                                                        && $avalue['RoleCategoryId'] == $value['RoleCategoryId']) && !in_array($avalue['roleid'], $currentUserRole)) { ?>
                                                        <ul class="list-group">
                                                                <li><?= Html::encode($avalue['role']) ?>
                                                                <?= Html::a('Privileges',['rbac/add-role-privileges', 'roleId' => $avalue['roleid'],
                                                                    'institutionId' => $value['InstitutionID'], 
                                                                    'roleGroupId' => $value['RoleGroupId'] ], 
                                                                    ['class' => 'btn btn-primary btn-sm manage'])
                                                                ?>
                                                                </li>
                                                        </ul>
                                                <?php } ?>
                                            <?php endforeach ?>
                                            </div>
                                        </div>
                                  <?php endforeach ?>
                                    </div>
                                </div>
                                <?php endforeach ?>
                                        
                            <?php } else { ?>
                                <p>No record found</p>
                            <?php  } ?>
                                       
                            </div>
                        </div>
                    </fieldset>
                 <!-- contents -->
                </div>
            </div>
    </div>
</div>



    