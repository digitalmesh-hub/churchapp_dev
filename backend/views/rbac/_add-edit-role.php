<?php 

use yii\helpers\Html;
use backend\assets\AppAsset;
use backend\components\widgets\FlashResult;

$this->title = 'AddOrEditCategory';
$assetName = AppAsset::register($this);

$this->registerJsFile(
    $assetName->baseUrl . '/theme/js/Remember.roles.ui.js',
    [
        'depends' => [
                AppAsset::className()
        ]
    ]
);

echo Html::hiddenInput(
    'getSelectedRoles',
    \Yii::$app->params['ajaxUrl']['getSelectedRoles-url'],
    [
        'id'=>'getSelectedRoles-url'
    ]
);
 
?>
<!-- Contents -->
<div class="container">
    <div class="row">
            <!-- Content -->
            <div class="col-md-12 col-sm-12 contentbg">
            <div class = "Mtop10">
                        <?= FlashResult::widget(); ?>
                </div>
                <div class="col-md-12 col-sm-12 Mtopbot20">
                    <fieldset>
                        <legend style="font-size: 20px;">Edit Roles
                         <?= Html::a('Back to Listing', ['rbac/roles', 'id' => $institutionModel->id], ['class' => 'btn btn-primary btn-sm manage rightalign Mtop-5']) ?>
                        </legend>
                      <?= Html::beginForm(['rbac/add-edit-role', 'institutionId' => $institutionModel->id, 'roleCategoryId' => $roleCategoryId,'roleGroupId' => $roleGroupId], 'post', ['id' => 'role-form']) ?>
<div class="blockrow Mtop20">
    <div class="privilageedit-holder">
        <div class="col-md-12 Mtop10">
            <div class="form-group category-holder">
                <label class="col-md-4 col-sm-4 control-label text-left" for="request-description">Category Name</label>
                <div class="col-md-6 col-sm-8">
                    <?= Html::dropDownList(
                        'role_category',
                        ($roleCategoryId) ? $roleCategoryId : null ,
                        $dropDownData,
                        [
                            'class' => "form-control",
                            'id' => 'role_category',
                        ]) ?>  
                </div>
            </div>
            <div id="result">
                <div id="rolecategorylist">
                    <ul class="list-group">
                        <li>
                            <?= Html::button('Add New Role', [
                            'class' => 'btn btn-primary btn-sm manage pull-left',
                            'id' => 'add-new-Role',
                            'title' => yii::t('app', 'Add New Role')
                            ]
                        ) ?>
                        </li>
                        <div class="field_wrapper">
                            <div>
                            <li>
                            <input type="text" class="form-control roles" name="field_name[roledescription][]" value="" id="rolestextbox" roleid="0" isdeleted="0" maxlength="100">
                            </li>
                            </div>
                        </div>

                    </ul>
                </div>

            </div>

        </div>
        <?= Html::hiddenInput('institutionId', $institutionModel->id,['id' => 'institutionId']) ?>
        <?= Html::submitButton('Save', ['class' =>  'btn btn-success btn-sm manage Mtop20' , 'id' => 'btn-role-save', 'title' => 'Save']) ?>
    </div>
</div>
    <?= Html::endForm() ?>
                    </fieldset>
                 <!-- contents -->
                </div>
            </div>
    </div>
</div>
