<?php

use yii\helpers\Html;
use yii\widgets\ListView;
use yii\widgets\Pjax;
use yii\helpers\Url;
use backend\assets\AppAsset;
use yii\widgets\ActiveForm;
use backend\components\widgets\FlashResult;

$this->title = 'List Institutions';
$assetName = AppAsset::register($this);
$this->registerJsFile(
    $assetName->baseUrl . '/theme/js/Remember.institution.ui.js',
    [
        'depends' => [
                AppAsset::className()
        ]
    ]
);
echo Html::hiddenInput(
    'institution-deactivate-Url',
    \Yii::$app->params['ajaxUrl']['institution-deactivate-Url'],
    [
        'id'=>'institution-deactivate-Url'
    ]
);
echo Html::hiddenInput(
    'institution-activate-Url',
    \Yii::$app->params['ajaxUrl']['institution-activate-Url'],
    [
        'id'=>'institution-activate-Url'
    ]
);
?>
<div class="col-md-12 col-sm-12 pageheader Mtop15">Institution List</div>
 <!-- Content -->
    <div class="col-md-12 col-sm-12 contentbg">
      <div class="col-md-12 col-sm-12 Mtopbot20"> 
        <?= FlashResult::widget(); ?>
        
        <!-- Section head --> 
        <!--<div class="pagesubhead">Employee Listing</div>-->
        
        <div class="blockrow Mtop25">
          <div class="col-md-12">
           <?php $form = ActiveForm::begin(
                [
                'action' => ['list-institution'],
                'method' => 'get',
                ]
                ); ?>
            <div class="col-md-5 col-md-offset-3 col-sm-4 col-sm-offset-3">
                <?= $form->field($model, 'name')->textInput(['placeholder' => 'Search Institution by name'])->label(false); ?>      
            </div>
            <div class="col-md-2 col-sm-2">
              <?= Html::submitButton('Search', ['class' => 'btn btn-primary','title' => Yii::t('yii', 'Search')]) ?>
            </div>
            <?php ActiveForm::end(); ?>
          </div>
        </div>
        <!-- /. Blockrow closed --> 
        
        <!-- Employee Listing Table -->
        <div class="blockrow Mtop20">
          <table cellspacing="0" cellpadding="0" id="tableAnnouncement" data-pagination="true" class="table employeelist">
            <?php if(!empty($dataProvider->getModels())) { ?>
                <thead>
                <tr>
                    <th class="text-center">Image</th>
                    <th class="text-center">Institution Name</th>
                    <th class="text-center">Status</th>
                    <th>&nbsp</th>
                </tr>
                </thead>
                <?php } ?> 
            <tbody id="eventBody">
            <?php Pjax::begin(); ?>
            <?= ListView::widget(
                [
                'dataProvider' => $dataProvider,
                'itemView' => '_view',
                'layout' => '{items}</tbody></table><div class="blockrow Mtop25 text-right">{pager}</div>'
            ]
            ); ?>
            <?php Pjax::end(); ?>
            </tbody>
          </table>
        </div>
        <!-- /. Employee Listing --> 
      </div>
    </div>
  </div>
</div>
<!-- Contents closed --> 