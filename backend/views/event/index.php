<?php

use yii\helpers\Html;
use yii\widgets\ListView;
use yii\widgets\Pjax;
use backend\assets\AppAsset;
use yii\widgets\ActiveForm;


$this->title = 'Events';
$this->params['breadcrumbs'][] = $this->title;

$assetName = AppAsset::register($this);
$this->registerJsFile(
    $assetName->baseUrl . '/theme/js/Remember.Event.ui.js',
    [
        'depends' => [
                AppAsset::className()
        ]
    ]
);
/* @var $this yii\web\View */
/* @var $searchModel common\models\searchmodels\EventSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

?>
<div class="extended-event-index">
    <div class="col-md-12 col-sm-12 pageheader Mtop15">Events Listing</div>
    <div class="col-md-12 col-sm-12 contentbg">
    <div class="col-md-12 col-sm-12 Mtopbot20">
    <div class="blockrow Mtop25">
                        <div class="col-md-12">
                             <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
                            <div class="col-md-4 col-md-offset-3 col-sm-4 col-sm-offset-3">
                            <?= $form->field($searchModel, 'searchKeyword')->textInput(['placeholder' => 'Search by Title,Activity Date'])->label(false) ?>
                            </div>
                            <div class="col-md-2 col-sm-2">
                                
                                <?= Html::submitButton('Search', ['class' => 'btn btn-primary','title' => 'Search']) ?>
                            </div>
                        </div>
   <?php ActiveForm::end(); ?>
                    </div>
     <!-- Events Listing Table -->
                 <div class="blockrow Mtop20">
    <table cellpadding="0" cellspacing="0" class="table table-fixed eventlist " data-pagination="true" id="tableEvents">
    <thead id="tablehead">
        <tr>
            <th style="width: 12%">Title</th>
            <th style="width: 10%">Venue</th>
            <th style="width: 10%">Activity Date</th>
            <th style="width: 10%">Activated On</th>
            <th style="width: 10%">Expiry On</th>
            <th style="width: 10%">Created On</th>
            <th style="width: 10%">Published On</th>
            <th style="width: 28%" class="text-center">Actions</th>
        </tr>
    </thead>
    <tbody id="eventBody">
     <?php if(!empty($dataProvider->getModels())) {?>
      <?php Pjax::begin(); ?>
            <?= ListView::widget(
                [
                'dataProvider' => $dataProvider,
                'itemView' => '_view',
                'emptyText' => false,
                'layout' => '{items} </tbody></table><div class="blockrow Mtop25 text-right">{pager}</div>',
            ]
            ); ?>
            <?php Pjax::end(); ?>
             <?php }else { ?>
                  <tr>
                       <td class="text-center" colspan="7">No results found.</td>
                 </tr>
            <?php } ?>
                   
</tbody>
</table>
</div>
</div>
</div>
</div>
</div>
</div> 

