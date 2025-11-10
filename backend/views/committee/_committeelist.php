<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use backend\assets\AppAsset;

$assetName = AppAsset::register($this);
 echo Html::hiddenInput(
            'admin-delete-committee-member-Url',
            \Yii::$app->params['ajaxUrl']
            ['admin-delete-committee-member-Url'],
            [
                'id'=>'admin-delete-committee-member-Url'
            ]
        ); 

?>

<div role="tabpanel" class="tab-pane fade" 
id="committee">
    <?php $form = ActiveForm::begin() ?>
        <div class="inlinerow Mtop20">
            <div class="col-md-12 col-sm-12">
                <strong>Committee members for the year
                </strong>

            </div>
            <div class="inlinerow Mtop10">
                <div class="col-md-3 col-sm-3">
                    <div class="labelbox">
                        <strong>Committee<span style="color: red;">*</span></strong>
                    </div>
                    <div class="inlinerow Mtop10 committeetype">
                        <?= $form->field($memberModel, 'committeeType'
                        )
                        ->dropDownList(
                            $committeTypeList,
                            [
                             'id' => 'committeeType',
                             'prompt' => 'Please Select'
                           ]
                        )->label(false) ?>
                    </div>
                </div>

                <div class="col-md-4 col-sm-4">
                    <div class="labelbox">
                        <strong>Committee Period <span style="color: red;">*</span></strong>
                    </div>
                    <div class="inlinerow Mtop10 committeeperiod">
                         <?= $form->field($memberModel, 'committeePeriod'
                        )
                        ->dropDownList(
                            [],
                            [
                             'id' => 'committeePeriod',
                             'prompt' => 'Please Select'
                           ]
                        )->label(false) ?>
                    </div>
                </div>

                <div class="col-md-2 col-sm-2">
                    <div class="labelbox">&nbsp;</div>
                    <div class="inlinerow Mtop10">
                        <?= Html::button('<span class="glyphicon glyphicon-plus-sign"></span>&nbsp;Search', ['class' => 'btn btn-primary' ,'id' =>'search-committee-member', 'title' => Yii::t('yii', 'Search')]) ?>
                    </div>
                </div>
            </div>

            <div class="inlinerow Mtop10 ">
                <div class="col-md-6 col-sm-6" id="CommitteeMemberListErrorDiv" style="display:none">
                    <div class="alert alert-danger text-center" id="CommitteeMemberListErrorMessageLabel" role="alert">
                        <strong>Error! Please check again</strong>
                    </div>
                </div>
            </div>
        </div>
    <?php ActiveForm::end() ?>
    <div class="committeeMembers"></div> 
</div>