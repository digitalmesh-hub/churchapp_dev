<?php 
 
use yii\helpers\Html;
use backend\assets\AppAsset;
use backend\components\widgets\FlashResult;

$this->title = 'Institution Privileges';
$assetName = AppAsset::register($this);

?>

<!-- Contents -->
    <div class="container">
        <div class="row">

            <!-- Header -->
            <div class="col-md-12 col-sm-12 pageheader Mtop15">Institution Privileges</div>

            <!-- Content -->
            <div class="col-md-12 col-sm-12 contentbg">
                <!-- Copy -->
                <div class = "Mtop10">
                    <?= FlashResult::widget(); ?>
                </div>
                <div class="col-md-12 col-sm-12 ">
                    <div class="blockrow">
                        <div class="blockrow ">
                        <?= Html::beginForm(['rbac/privileges','id' =>$institutionModel->id], 'post', []) ?>
                            <div class="privilage-holder">
                                <div class="col-md-12 Mtop10">
                                    <table id="institutionprivilege" cellpadding="0" cellspacing="0" class="table table-bordered">
                                        <tbody>
                                            <tr>
                                                <th colspan="2"><?= Html::encode($institutionModel->name) ?></th>
                                            </tr>
                                            <?php foreach ($provider as $key => $value): ?>
                                                <tr class="priv">
                                                <td><?= Html::encode($value) ?> </td>
                                                <td>
                                                <?= Html::checkbox($key, in_array($key, $institutionPrivileges) ? true :false, ['label' => false]) ?>
                                                </td>
                                                </tr> 
                                            <?php endforeach ?>
                                        </tbody>
                                    </table>
                                </div>
                                <?= Html::hiddenInput('institutionId', $institutionModel->id) ?>
                                <?= Html::submitButton('Save', ['class' => 'submit btn btn-success btn-sm manage','title' => Yii::t('app', 'Save')]) ?>
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
