<?php 

use yii\web\view;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ListView;
use backend\assets\AppAsset;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;

$assetName = AppAsset::register($this);
$this->title = 'Conversations';

$assetName = AppAsset::register ( $this );
$this->registerJsFile ( $assetName->baseUrl . '/theme/js/Remember.conversations.ui.js',[
        'depends' => [
            AppAsset::className ()
    ]
] );
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
             <?php $form = ActiveForm::begin(['options' => ['id' => 'form1'],'method' => 'get']); ?>
                <?php echo @Html::hiddenInput('homeUrl', Url::home(), array('id'=>'homeUrl'));?> 
                <legend style="font-size: 20px;">Search Conversations</legend>
                <div class="inlinerow">
                    <div class="col-md-3 col-sm-3">
                        <div class="labelbox"><strong>Search by Title</strong></div>
                        <div class="inlinerow Mtop10"> 
                        <?= $form->field($model, 'search_title')
                            ->dropDownList(
                                $subjectTitleModel,
                                 [
                                 'id' => 'search_title_id',
                                 'prompt' => 'All',
                                 'options'=> [
                                    isset($dynamic['search_title']) ? $dynamic['search_title'] : null => array('selected' => true) 
                                 ]
                               ]
                            )->label(false) ?>
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-3">
                        <div class="labelbox"><strong>Search by Word</strong></div>
                        <div class="inlinerow Mtop10"> 
                        <?= $form->field($model, 'search_word')
                             ->textInput(
                                 [
                                 'id' => 'search_word_id', 
                               ]
                             )->label(false) ?>
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-3">
                        <div class="labelbox"><strong>Start Date</strong></div>
                        <div class="inlinerow Mtop10">
                         <?= $form->field($model, 'start_date')->widget(DatePicker::classname(), [
                                    'name' => 'start_date',
                                    'id' => 'startdate',
                                    'options' => [
                                         'placeholder' => 'Enter start date',
                                    ],
                                    'pluginOptions' => [
                                        'autoclose'=>true,
                                        'format' => 'dd MM yyyy', 
                                    ]
                        ])->label(false); ?> 
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-3">
                        <div class="labelbox"><strong>End Date</strong></div>
                        <div class="inlinerow Mtop10">
                            <?= $form->field($model, 'end_date')->widget(DatePicker::classname(), [
                                   'name' => 'end_date',
                                    'id' => 'enddate',
                                    'options' => [
                                         'placeholder' => 'Select End Date',
                                    ],
                                    'pluginOptions' => [
                                        'autoclose'=>true,
                                        'format' => 'dd MM yyyy',
                                        'endDate' =>'0d'
                                    ]
                        ])->label(false); ?> 
                        </div>
                    </div>
                </div>
                <div class="inlinerow">

                    <div class="col-md-3 col-sm-3">
                        <div class="labelbox"><strong>Member Name</strong></div>
                        <div class="inlinerow Mtop10"> 
                            <?= $form->field($model, 'member_name')
                             ->dropDownList($memberNameModel,
                                 [
                                 'id' => 'member_id',
                                 'prompt' => 'All',
                                 'options'=> [
                                    isset($dynamic['member_name']) ? $dynamic['member_name'] : null => array('selected' => true) 
                                 ]
                               ]
                             )->label(false) ?>
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-3">
                        <div class="labelbox">&nbsp;</div>
                        <div class="inlinerow Mtop10">
                            <?= Html::submitButton('Search',['class' => 'btn btn-primary','title' => Yii::t('yii', 'Search')]) ?>
                        </div>
                    </div>

                </div>
                <?php ActiveForm::end(); ?>

                <!-- /. Section 2 -->
            </fieldset>
            <?php
            if(!empty($conversations->getModels())) { ?>
                <?= ListView::widget(
                    [
                        'dataProvider' => $conversations,
                        'itemView' => '_view',
                        'layout' => '{items}</tbody></table><div class="blockrow Mtop25 text-right">{pager}</div>',
                    ]
                ); 
            }?>
        </div>
    </div>                   
</div>
  
