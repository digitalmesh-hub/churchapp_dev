<?php

use yii\web\view;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ListView;
use backend\assets\AppAsset;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use common\models\extendedmodels\ExtendedTitle;
use yii\helpers\ArrayHelper;


$this->title = 'Qurbana Requests';

$assetName = AppAsset::register($this);
$this->registerJsFile($assetName->baseUrl . '/theme/js/Remember.conversations.ui.js', [
    'depends' => [
        AppAsset::className()
    ]
]);

$titleModel      = new ExtendedTitle();
$institutionId = yii::$app->user->identity->institution->id;
$titles = $titleModel->getActiveTitles($institutionId);
$titlesArray = [];
if (!empty($titles)) {
    $titlesArray =    ArrayHelper::map($titles, 'TitleId', 'Description');
}
?>

<!-- Header -->
<div class="col-md-12 col-sm-12 col-xs-12 pageheader Mtop15">
    <?= $this->title ?>
</div>
<div class="extended-userprofile-index">

    <!-- Content -->
    <div class="col-md-12 col-sm-12 contentbg">
        <div class="col-md-12 col-sm-12 Mtopbot20">
            <!-- Section head -->
            <fieldset>
                <?php $form = ActiveForm::begin(['options' => ['id' => 'form1'], 'method' => 'get']); ?>
                <legend style="font-size: 20px;">Search Qurbana</legend>
                <div class="inlinerow">
                    <div class="col-md-3 col-sm-3">
                        <div class="labelbox"><strong>Search by Qurbana Type</strong></div>
                        <div class="inlinerow Mtop10">
                            <?= $form->field($searchModel, 'qurbana_type_id')->dropDownList(
                                $qurbanaTypes,
                                [
                                    'prompt' => 'All',
                                    'id' => 'type-search'
                                ]
                            )->label(false); ?>
                        </div>
                    </div>



                    <div class="col-md-3 col-sm-3">
                        <div class="labelbox"><strong>Qurbana Date</strong></div>
                        <div class="inlinerow Mtop10">
                            <?= $form->field($searchModel, 'qurbana_date')->widget(DatePicker::classname(), [
                                'name' => 'qurbana_date',
                                'id' => 'qurbana_date',
                                'options' => [
                                    'placeholder' => 'Enter Qurbana Date',
                                ],
                                'pluginOptions' => [
                                    'autoclose' => true,
                                    'format' => 'yyyy-mm-dd',
                                ]
                            ])->label(false); ?>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-3">
                        <div class="labelbox">&nbsp;</div>
                        <div class="inlinerow Mtop10">
                            <div class="col-md-4">
                                <?= Html::submitButton('Search', ['class' => 'btn btn-primary', 'title' => Yii::t('yii', 'Search')]) ?>
                            </div>
                            <?php if (!empty($dataProvider->models)) { ?>
                            <div class="col-md-4">
                             <?= Html::a('Export', 
                                        array_merge(['/qurbana/export-qurbana-list'], Yii::$app->request->queryParams), 
                                        ['class' => 'btn btn-primary', 'title' => Yii::t('yii', 'Export')])
                            ?>
                            </div>
                            <?php } ?>
                            


                        </div>
                    </div>
                </div>
                <?php ActiveForm::end(); ?>

                <!-- /. Section 2 -->
            </fieldset>
            <div class="inlinerow Mtop20" id="FeedbackListDiv">
                <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                    <?php if (!empty($dataProvider->models)) { ?>
                        <?= ListView::widget([
                            'dataProvider' => $dataProvider,
                            'itemView' => function ($model) {
                                $qurbanaType = $model->qurbanatype ? $model->qurbanatype->type : 'N/A';
                                $memberData = $model->member;
                                return '
                <tr>
                    <td>' .  $model->member->FullNameWithTitle. '</td>
                    <td>' . ($memberData['member_mobile1'] ?? 'N/A') . '</td>
                    <td>' . $qurbanaType . '</td>
                    <td>' . (!empty($model->name) ? $model->name : 'Self') . '</td>
                    <td>' . date_format(date_create($model->qurbana_date), Yii::$app->params['dateFormat']['formDateFormat']) . '</td>
                </tr>';
                            },
                            'layout' => '
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Requested By</th>
                        <th>Contact</th>
                        <th>Qurbana Type</th>
                        <th>Name to be remembered</th>
                        <th>Qurbana Date</th>
                    </tr>
                </thead>
                <tbody>
                    {items}
                </tbody>
            </table>
            <div class="blockrow Mtop25 text-right">{pager}</div>',
                        ]); ?>
                    <?php } else { ?>
                        <div>
                            <label style="vertical-align: middle; padding-left: 40%;">No Record found</label>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>