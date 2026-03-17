<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\assets\AppAsset;
use backend\components\widgets\FlashResult;

$assetName = AppAsset::register($this);
$this->registerJsFile(
    $assetName->baseUrl . '/theme/js/Remember.vicardirectory.ui.js',
    [
        'depends' => [
            AppAsset::className()
        ]
    ]
);
?>

<div class="col-md-12 col-sm-12 pageheader Mtop15">Vicar Positions</div>
<div class="col-md-12 col-sm-12 contentbg">
    <div class="col-md-12 col-sm-12 Mtopbot20">

        <?= FlashResult::widget(); ?>

        <?php
        $path = (isset($update) && $update) ? 'vicardirectory/update-position/' . $model->id : 'vicardirectory/create-position';
        $button = (isset($update) && $update) ? 'Update' : 'Add Position';
        ?>

        <div class="inlinerow Mtop20">
            <div class="col-md-12 col-sm-12"><strong>Manage Vicar Positions</strong></div>

            <?php $form = ActiveForm::begin(['action' => [$path], 'options' => ['method' => 'post']]) ?>

            <div class="inlinerow Mtop10">
                <div class="col-md-3 col-sm-3">
                    <?= $form->field($model, 'position_name')->textInput(['maxlength' => true])->label('Position Name') ?>
                </div>

                <div class="col-md-3 col-sm-3">
                    <?= $form->field($model, 'position_description')->textInput(['maxlength' => true])->label('Description (Optional)') ?>
                </div>

                <div class="col-md-2 col-sm-2">
                    <?= $form->field($model, 'is_main_vicar')->checkbox()->label('Is Main Vicar?') ?>
                </div>

                <div class="col-md-2 col-sm-2">
                    <?= $form->field($model, 'display_order')->textInput(['type' => 'number'])->label('Display Order') ?>
                </div>

                <div class="col-md-2 col-sm-2 Mtop25">
                    <div style="display: inline-block; vertical-align: top;">
                        <?= Html::submitButton('<span class="glyphicon glyphicon-plus-sign"></span>&nbsp;' . $button, ['class' => 'btn btn-primary', 'title' => $button]) ?>
                    </div>
                    <div style="display: inline-block; vertical-align: top; margin-left: 5px; margin-top: 3px;">
                        <?= Html::a('Clear', ['positions'], ['class' => 'btn btn-primary', 'title' => 'Clear']) ?>
                    </div>
                </div>
            </div>

            <?php ActiveForm::end(); ?>
        </div>

        <div class="col-md-12 col-sm-12 Mtop20">
            <h4>Existing Positions</h4>
            
            <!-- Search Box -->
            <div class="row Mtop10">
                <div class="col-md-4 col-sm-4">
                    <input type="text" id="searchPositions" class="form-control" placeholder="Search positions...">
                </div>
            </div>

            <?php if (!empty($positions)): ?>
                <table class="table table-bordered Mtop10" id="positionsTable">
                    <thead>
                        <tr>
                            <th>Position Name</th>
                            <th>Description</th>
                            <th>Type</th>
                            <th>Display Order</th>
                            <th>Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($positions as $position): ?>
                            <tr class="<?= $position->active ? '' : 'text-muted' ?>">
                                <td><?= Html::encode($position->position_name) ?></td>
                                <td><?= Html::encode($position->position_description) ?></td>
                                <td>
                                    <?php if ($position->is_main_vicar): ?>
                                        <span class="label label-primary">Main Vicar</span>
                                    <?php else: ?>
                                        <span class="label label-info">Assistant</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center"><?= $position->display_order ?></td>
                                <td>
                                    <?php if ($position->active): ?>
                                        <span class="label label-success">Active</span>
                                    <?php else: ?>
                                        <span class="label label-default">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?= Html::a('Edit', ['update-position', 'id' => $position->id], ['class' => 'btn btn-primary btn-sm']) ?>
                                    <?php if ($position->active): ?>
                                        <?= Html::button('Deactivate', [
                                            'class' => 'btn btn-danger btn-sm btn-deactivate-position',
                                            'data-id' => $position->id,
                                        ]) ?>
                                    <?php else: ?>
                                        <?= Html::button('Activate', [
                                            'class' => 'btn btn-success btn-sm btn-activate-position',
                                            'data-id' => $position->id,
                                        ]) ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-muted">No positions found. Add your first position above.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
