<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\widgets\LinkPager;
use backend\assets\AppAsset;
use kartik\date\DatePicker;

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

<div class="col-md-12 col-sm-12 pageheader Mtop15">Vicar Directory Management</div>
<div class="col-md-12 col-sm-12 contentbg">
    <div class="col-md-12 col-sm-12 Mtopbot20">
        
        <?php
        $path = (isset($update) && $update) ? 'vicardirectory/update-vicar/' . $model->id : 'vicardirectory/create-vicar';
        $button = (isset($update) && $update) ? 'Update' : 'Assign Vicar';
        ?>
        
        <div class="inlinerow Mtop20">
            <div class="col-md-12 col-sm-12"><strong>Assign Members to Vicar Positions</strong></div>

            <?php $form = ActiveForm::begin(['action' => [$path], 'options' => ['method' => 'post']]) ?>

            <div class="inlinerow Mtop10">
                <div class="col-md-3 col-sm-3">
                    <?= $form->field($model, 'vicar_position_id')->dropDownList(
                        ArrayHelper::map($positions, 'id', 'position_name'),
                        ['prompt' => 'Select Position', 'id' => 'position-select']
                    )->label('Position') ?>
                </div>
                
                <div class="col-md-3 col-sm-3">
                    <?= $form->field($model, 'member_id')->dropDownList(
                        ArrayHelper::map($members, 'memberid', function($member) {
                            $nameParts = array_filter([
                                $member->membertitle0 ? $member->membertitle0->Description : '',
                                $member->firstName,
                                $member->middleName,
                                $member->lastName
                            ]);
                            $fullName = implode(' ', $nameParts);
                            return $fullName . ' (' . $member->memberno . ')';
                        }),
                        ['prompt' => 'Select Member', 'id' => 'member-select']
                    )->label('Member') ?>
                </div>
                
                <div class="col-md-3 col-sm-3">
                    <?php
                    // Convert date format for display in edit mode to match Yii::$app->formatter->asDate()
                    if (!empty($model->start_date) && $model->start_date != '0000-00-00') {
                        $model->start_date = date('d F Y', strtotime($model->start_date));
                    }
                    ?>
                    <?= $form->field($model, 'start_date')->widget(DatePicker::classname(), [
                        'options' => [
                            'placeholder' => 'Choose date',
                        ],
                        'pluginOptions' => [
                            'autoclose' => true,
                            'format' => 'dd MM yyyy',
                        ]
                    ])->label('Start Date') ?>
                </div>
                
                <div class="col-md-3 col-sm-3 Mtop25">
                    <div style="display: inline-block; vertical-align: top;">
                        <?= Html::submitButton('<span class="glyphicon glyphicon-plus-sign"></span>&nbsp;' . $button, ['class' => 'btn btn-primary', 'title' => $button]) ?>
                    </div>
                    <div style="display: inline-block; vertical-align: top; margin-left: 5px; margin-top: 3px;">
                        <?= Html::a('Clear', ['index'], ['class' => 'btn btn-primary', 'title' => 'Clear']) ?>
                    </div>
                </div>
            </div>
            
            <div class="inlinerow">
                <div class="col-md-6 col-sm-6">
                    <?= $form->field($model, 'remarks')->textarea(['rows' => 2])->label('Remarks') ?>
                </div>
                <div class="col-md-2 col-sm-2">
                    <?= $form->field($model, 'display_order')->textInput(['type' => 'number'])->label('Display Order') ?>
                </div>
            </div>

            <?php ActiveForm::end(); ?>
        </div>

        <div class="col-md-12 col-sm-12 Mtop20">
            <h4>Current Vicar Assignments</h4>
            
            <!-- Search and Filter -->
            <form method="get" action="<?= Yii::$app->urlManager->createUrl(['vicardirectory/index']) ?>" id="filterForm">
                <div class="row Mtop10">
                    <div class="col-md-4 col-sm-4">
                        <input type="text" name="search" class="form-control" placeholder="Search by name, position, or membership no..." value="<?= Html::encode($filters['search'] ?? '') ?>">
                    </div>
                    <div class="col-md-3 col-sm-3">
                        <select name="status" class="form-control" onchange="this.form.submit()">
                            <option value="">All Status</option>
                            <option value="active" <?= isset($filters['status']) && $filters['status'] === 'active' ? 'selected' : '' ?>>Active Only</option>
                            <option value="inactive" <?= isset($filters['status']) && $filters['status'] === 'inactive' ? 'selected' : '' ?>>Inactive Only</option>
                        </select>
                    </div>
                    <div class="col-md-3 col-sm-3">
                        <select name="position" class="form-control" onchange="this.form.submit()">
                            <option value="">All Positions</option>
                            <?php foreach ($positions as $position): ?>
                                <option value="<?= Html::encode($position->position_name) ?>" <?= isset($filters['position']) && $filters['position'] === $position->position_name ? 'selected' : '' ?>>
                                    <?= Html::encode($position->position_name) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2 col-sm-2">
                        <button type="submit" class="btn btn-primary">Search</button>
                        <?php if (!empty($filters)): ?>
                            <a href="<?= Yii::$app->urlManager->createUrl(['vicardirectory/index']) ?>" class="btn btn-default">Clear</a>
                        <?php endif; ?>
                    </div>
                </div>
            </form>
            
            <?php if (!empty($vicars)): ?>
                <table class="table table-bordered Mtop10" id="vicarsTable">
                    <thead>
                        <tr>
                            <th>Position</th>
                            <th>Member</th>
                            <th>Membership No</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Status</th>
                            <th>Display Order</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($vicars as $vicar): ?>
                            <tr class="<?= $vicar['is_active'] ? '' : 'text-muted' ?>">
                                <td>
                                    <?= Html::encode($vicar['position_name']) ?>
                                    <?php if ($vicar['is_main_vicar']): ?>
                                        <span class="label label-primary">Main Vicar</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                    $nameParts = array_filter([
                                        $vicar['memberTitle'] ?? '',
                                        $vicar['firstName'] ?? '',
                                        $vicar['middleName'] ?? '',
                                        $vicar['lastName'] ?? ''
                                    ]);
                                    echo Html::encode(implode(' ', $nameParts));
                                    ?>
                                </td>
                                <td><?= Html::encode($vicar['memberno']) ?></td>
                                <td><?= Yii::$app->formatter->asDate($vicar['start_date'], 'php:d F Y') ?></td>
                                <td><?= $vicar['end_date'] ? Yii::$app->formatter->asDate($vicar['end_date'], 'php:d F Y') : '-' ?></td>
                                <td>
                                    <?php if ($vicar['is_active']): ?>
                                        <span class="label label-success">Active</span>
                                    <?php else: ?>
                                        <span class="label label-default">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center"><?= $vicar['display_order'] ?></td>
                                <td class="text-center">
                                    <?= Html::a('Edit', ['update-vicar', 'id' => $vicar['id']], ['class' => 'btn btn-primary btn-sm']) ?>
                                    <?php if ($vicar['is_active']): ?>
                                        <?= Html::button('Deactivate', [
                                            'class' => 'btn btn-danger btn-sm btn-deactivate-vicar',
                                            'data-id' => $vicar['id'],
                                        ]) ?>
                                    <?php else: ?>
                                        <?= Html::button('Activate', [
                                            'class' => 'btn btn-success btn-sm btn-activate-vicar',
                                            'data-id' => $vicar['id'],
                                        ]) ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <!-- Pagination -->
                <div class="row Mtop10">
                    <div class="col-md-12">
                        <?= LinkPager::widget([
                            'pagination' => $pagination,
                            'options' => ['class' => 'pagination'],
                            'linkOptions' => ['class' => 'page-link'],
                        ]) ?>
                    </div>
                </div>
            <?php else: ?>
                <p class="text-muted">No vicar assignments found.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
