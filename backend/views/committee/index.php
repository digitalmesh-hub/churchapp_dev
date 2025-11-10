<?php

use yii\helpers\Url;
use yii\helpers\Html;
use backend\assets\AppAsset;

$assetName = AppAsset::register($this);
$this->registerJsFile ( $assetName->baseUrl . '/theme/js/Remember.committee.ui.js',[
        'depends' => [
            AppAsset::className ()
    ]
]);

$this->registerJsFile ( $assetName->baseUrl . '/theme/js/Remember.committeePeriod.ui.js',[
        'depends' => [
            AppAsset::className ()
    ]
]);

$this->registerJsFile ($assetName->baseUrl . '/theme/js/Remember.committeeAddMember.ui.js',[
        'depends' => [
            AppAsset::className ()
    ]
]);

$this->registerJsFile ( $assetName->baseUrl . '/theme/js/Remember.committeePeriod.ui.js',[
        'depends' => [
            AppAsset::className ()
    ]
]);

$this->registerJsFile ( $assetName->baseUrl . '/theme/js/Remember.committeeDesignation.ui.js',[
        'depends' => [
            AppAsset::className ()
    ]
]);

$this->registerJsFile ( $assetName->baseUrl . '/theme/js/Remember.committeeMemberList.ui.js',[
        'depends' => [
            AppAsset::className ()
    ]
]);

$this->registerJsFile ( $assetName->baseUrl . '/theme/js/Remember.committee.ui.js',[
        'depends' => [
            AppAsset::className ()
    ]
]);

$this->title = 'Committee'; 
?>
<?php echo Html::hiddenInput(
            'admin-get-committee-type-Url',
            \Yii::$app->params['ajaxUrl']['admin-get-committee-type-Url'],
            [
                'id'=>'admin-get-committee-type-Url'
            ]
        ); ?>
<?php echo Html::hiddenInput(
            'admin-get-committee-type-list-Url',
            \Yii::$app->params['ajaxUrl']['admin-get-committee-type-list-Url'],
            [
                'id'=>'admin-get-committee-type-list-Url'
            ]
        ); ?>
<div class="container">

    <div class="row">
        <div class="col-md-12 col-sm-12 pageheader Mtop15">Committee</div>
        <div class="col-md-12 col-sm-12 contentbg">
            <div class="col-md-12 col-sm-12 Mtopbot20">
                <div class="blockrow">
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="active" id="CommiteegroupAddli">
                            <a href="#committeegroup" aria-controls="CommitteeGroup" role="tab" data-toggle="tab" aria-expanded="false">Create Committee</a>
                        </li>
                        <li role="presentation" class="" id="CommiteedesignationAddli">
                            <a href="#committeedesignation" aria-controls="CommitteePeriod" role="tab" data-toggle="tab" aria-expanded="false">Create Designation</a>
                        </li>
                        <li role="presentation" class="" id="CommitteePeriodli">
                            <a href="#committeeperiod" aria-controls="CommitteePeriod" role="tab" data-toggle="tab" aria-expanded="false">Committee Period</a>
                        </li>
                        <li role="presentation" class="" id="CommitteeAddli">
                            <a href="#members" aria-controls="Members" role="tab" data-toggle="tab" aria-expanded="true">Add Members To Committee</a>
                        </li>
                        <li role="presentation" class="" id="Committeeli">
                            <a href="#committee" aria-controls="CommitteeMembers" role="tab" data-toggle="tab" aria-expanded="false">View Committee Members</a>
                        </li>
                    </ul>
                    <?php echo @Html::hiddenInput('homeUrl', Url::home(), array('id'=>'homeUrl'));?> 
                    <?php echo Html::hiddenInput(
                        'admin-save-committee-period-Url',
                        \Yii::$app->params['ajaxUrl']['admin-save-committee-period-Url'],
                        [
                            'id'=>'admin-save-committee-period-Url'
                        ]
                    ); ?>
                    <?php echo Html::hiddenInput(
                        'admin-get-committee-period-Url',
                        \Yii::$app->params['ajaxUrl']['admin-get-committee-period-Url'],
                        [
                            'id'=>'admin-get-committee-period-Url'
                        ]
                    ); ?>
                    <?php echo Html::hiddenInput(
                        'committee-period-id',0,
                        [
                            'id'=>'committee-period-id'
                        ]
                    ); ?>
                    <?php echo Html::hiddenInput(
                        'admin-activate-deactivate-committee-period-Url',
                        \Yii::$app->params['ajaxUrl']['admin-activate-deactivate-committee-period-Url'],
                        [
                            'id'=>'admin-activate-deactivate-committee-period-Url'
                        ]
                    ); ?>
                    <?php echo Html::hiddenInput(
                        'admin-get-period-by-type-Url',
                        \Yii::$app->params['ajaxUrl']['admin-get-period-by-type-Url'],
                        [
                            'id'=>'admin-get-period-by-type-Url'
                        ]
                    ); ?>
                    <?php echo Html::hiddenInput(
                        'admin-get-committee-members-Url',
                        \Yii::$app->params['ajaxUrl']['admin-get-committee-members-Url'],
                        [
                            'id'=>'admin-get-committee-members-Url'
                        ]
                    ); ?>

                    <div class="tab-content tabcontentborder" id="AddCommitteeType">
                        <?= $this->render('_committeetype', ['model' => $model,
                            'dataProvider' => $dataProvider, 'count' =>$count]) ?>
                    </div>
                    <div class="tab-content tabcontentborder" id="AddCommitteeDesignationDiv" style="display: none;">
                        <?= $this->render('_designation', [
                            'designationModel' => $designationModel,
                            'designationProvider' => $designationProvider, 
                            'designationCount' =>$designationCount]) ?> 
                    </div>
                    <div class="tab-content tabcontentborder" id="AddCommitteePeriodDiv" style="display: none;">
                        <?= $this->render('_period',['periodResult' => $periodResult,'periodModel' => $periodModel,
                            'committeTypeList' => $committeTypeList]); ?>
                    </div> 
                    <div class="tab-content tabcontentborder" id="AddMemberDiv" style="display: none;">
                        <?= $this->render('_members', [
                            'memberSearch' => $memberSearch,
                            'committeMemberList' => $committeMemberList]) ?>
                    </div> 
                    <div class="tab-content tabcontentborder" id="CommitteeMemberListDiv" style="display: none;">
                        <?= $this->render('_committeelist', [
                            'memberModel' => $memberModel,'committeTypeList' => $committeTypeList]) ?>
                    </div>  
                </div>
            </div>
            <div class="blockrow Mtop25 text-right">
                <nav>
                    <ul class="pagination" id="pging">
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>