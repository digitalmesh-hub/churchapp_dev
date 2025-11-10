<?php

use backend\assets\AppAsset;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use backend\components\widgets\FlashResult;
use yii\web\View;
use yii\widgets\Pjax;

$assetName = AppAsset::register($this);

$this->title = 'Bills';
$this->registerJs(<<<JS
$('#form1').on('beforeValidate', function (event, messages) {
              $('.overlay').show()
              $(this).find('.submit').attr('disabled', true)
})

$('#form1').on('afterValidate', function (event, messages) {
    var form = $(this)
    if (form.find('.has-error').length) {
        $('.overlay').hide()
        $(this).find('.submit').attr('disabled', false)
    }
})   
JS
,View::POS_READY);
?>
<!-- Header -->
<div class="col-md-12 col-sm-12 col-xs-12 pageheader Mtop15">
    <?= $this->title ?>
</div>
   <!-- Content -->
        <div class="col-md-12 col-sm-12 contentbg">
                <div class="col-md-12 col-sm-12 Mtopbot20">
                     <?= FlashResult::widget(); ?>
                    <fieldset>
                        <legend style="font-size: 20px;">Import Bills</legend>
                        <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data','id' =>"form"]]); ?>
                        <div class="blockrow Mtop15">
                            <div class="col-md-3 col-sm-3">
                                <div id="div1" class="filter-rows text-center">Year</div>
                                <div class="filter-rows">   
                                <?= $form->field($formModel, 'year')->dropdownList(array_combine(range(1993, 2025), range(1993, 2025)), [
                                    'prompt' => '',
                                    'options' => [ isset($queryBack['year']) ? $queryBack['year'] : date('Y') => ['Selected'=>'selected']]
                                    ])->label(false) ?>
                                </div>
                            </div>

                            <div class="col-md-3 col-sm-3">
                                <div class="filter-rows text-center">Month</div>
                                <div class="filter-rows">
                                <?= $form->field($formModel, 'month')->
                                dropdownList(['01' => 'January', '02' => 'February', 
                                            '03' => 'March', '04' => 'April','05' => 'May', 
                                            '06' => 'June', '07' => 'July', '08' => 'August', 
                                            '09' => 'September','10' => 'October', 
                                            '11' => 'November', '12' => 'December'
                                            ], 
                                        [
                                            'prompt' => '',
                                            'options' => [ isset($queryBack['year']) ? $queryBack['month'] :date('m') => ['Selected'=>'selected']]
                                        ]
                                )->label(false) ?>
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-4">
                                <div class="filter-rows text-center">Please Select File</div>
                                <div class="filter-rows">
                                <?= $form->field($formModel, 'invoice')->fileInput(['class' => 'form-control'])->label(false) ?>
                                </div>
                            </div>
                            <div class="col-md-1  col-sm-1">
                                <div class="filter-rows"></div>
                                <div class="filter-rows">
                                    <br>
                                    <?= Html::submitButton('Upload', ['class' => 'btn btn-success submit','title' => Yii::t('yii', 'Upload')]) ?>
                                </div>
                            </div>
                        </div>
                        <?php ActiveForm::end(); ?>
                        <!-- Filter action -->
                        <div class="inlinerow Mtop30">
                            <fieldset>
                                <legend style="font-size: 20px;">Preview Bills</legend>
                            </fieldset> 
                        </div>
                        <!-- Filter section 2 -->
                        <?php $searchForm = ActiveForm::begin(['options' => ['id' => 'form1'],'method' => 'get']); ?>
                        <div class="blockrow Mtop15">
                            <div class="col-md-3 col-sm-3">
                                <div id="divCategory" class="filter-rows text-center">Year</div>
                                <div class="filter-rows">
                                 <?= $searchForm->field($searchModel, 'year')->dropdownList(array_combine(range(1993, 2025), range(1993, 2025)), [
                                    'prompt' => '',
                                    'options' => [ ($searchModel->year) ? ($searchModel->year) :  date('Y') => ['Selected'=>'selected']]
                                    ])->label(false) ?>
                                </div>
                            </div>

                            <div class="col-md-3 col-sm-3">
                                <div class="filter-rows text-center">Month</div>
                                <div class="filter-rows">
                                    <?= $searchForm->field($searchModel, 'month')->
                                dropdownList(['01' => 'January', '02' => 'February', 
                                            '03' => 'March', '04' => 'April','05' => 'May', 
                                            '06' => 'June', '07' => 'July', '08' => 'August', 
                                            '09' => 'September','10' => 'October', 
                                            '11' => 'November', '12' => 'December'
                                            ], 
                                        [
                                            'prompt' => '',
                                            'options' => [ ($searchModel->month) ? $searchModel->month : date('m') => ['Selected'=>'selected']]
                                        ]
                                )->label(false) ?>
                                    
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-4">
                                <div class="filter-rows text-center">Search MemberNo </div>
                                <div class="filter-rows">
                                    <?= $searchForm->field($searchModel,'memberNo')->textInput()->label(false) ?>
                                </div>
                            </div>

                            <div class="col-md-1  col-sm-1">
                                <div class="filter-rows"></div>
                                <div class="filter-rows">
                                    <br>
                                    <?= Html::submitButton('Preview', ['class' => 'btn btn-success submit','title' => Yii::t('yii', 'Preview')]) ?>
                                </div>
                            </div>

                        </div>
                        <?php ActiveForm::end(); ?>
                        <div class="blockrow Mtop20 divhide">
                            <?php Pjax::begin(); ?>
                            <?= $htmlData ?>
                            <?php Pjax::end(); ?>
                        </div>
                    </fieldset>
                </div>
            </div>
            <!-- /. Blockrow closed -->
