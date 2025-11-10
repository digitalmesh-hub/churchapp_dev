<?php 

use yii\helpers\Html;
use yii\jui\AutoComplete;
use yii\web\JsExpression;

echo Html::hiddenInput('member-id', 0,
    [
        'id'=>'member-id'
    ]
); 
echo Html::hiddenInput(
            'admin-get-committee-member-details-Url',
            \Yii::$app->params['ajaxUrl']['admin-get-committee-member-details-Url'],
            [
                'id'=>'admin-get-committee-member-details-Url'
            ]
        ); 
echo Html::hiddenInput(
            'admin-get-member-for-search-Url',
            \Yii::$app->params['ajaxUrl']['admin-get-member-for-search-Url'],
            [
                'id'=>'admin-get-member-for-search-Url'
            ]
); 
?>
<div role="tabpanel" class="tab-pane fade active in" id="members">
        <div class="inlinerow">
            <div class="col-md-4 col-sm-4">
                <div class="labelbox">
                    <strong>Search by Name/Member Number</strong>
                </div>
                <div class="inlinerow Mtop5">
                   <?= AutoComplete::widget([
                        'name' => 'member_name',
                        'options' => [
                            'class' => 'form-control member-name'
                        ],
                        'clientOptions' => [
                        'source' => $committeMemberList,
                        'autoFill' => true,
                        'minLength' => 2,
                        'select' => new JsExpression("function( event, ui ) {
                                $('#member-id').val(ui.item.id);
                        }")
                    ],
                    ]);?>
                </div>
                <div class="inlinerow Mtop15">
                    <div class="checkbox">
                        <label>
                            <?= Html::checkbox('isSpouse', false, ['label' => 'Spouse', 'class' => 'spouse-check','labelOptions' => ['style' => 'padding:5px;']]) ?>
                        </label>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-4">
                <div class="labelbox">&nbsp;</div>
                <div class="inlinerow Mtop5">
                <?= Html::button('Search', ['class' => 'btn btn-primary add-member', 'title' => Yii::t('yii', 'Search'),"id"=>"button-add"]) ?>
                </div>
            </div>
        </div>
    <div id="CommitteeMemberDetailsDiv"></div>
</div>