<?php

use yii\helpers\Html;
use backend\assets\AppAsset;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;
use common\models\extendedmodels\ExtendedRsvpdetails;
use yii\grid\GridView;
/* @var $this yii\web\View */
/* @var $model common\models\extendedmodels\ExtendedEvent */

$this->title = 'RSVP Listing';
$assetName = AppAsset::register($this);
$this->registerJsFile(
    $assetName->baseUrl . '/theme/js/Remember.Event.ui.js',
    [
        'depends' => [
                AppAsset::className()
        ]
    ]
);
?>
<div>      
    <div class="container">
        <div class="row">
            <!-- Header -->
            <div class="col-md-12 col-sm-12 pageheader Mtop15">RSVP Listing</div>
            <!-- Content -->
            <div class="col-md-12 col-sm-12 contentbg">
                <div class="col-md-12 col-sm-12 Mtopbot20">

                    <!-- Section head -->  
                    <!-- /. Blockrow closed -->
                    <!-- header -->
                    <div class="row">
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <!-- Album info -->
                            <div class="albuminfo">

                                <div class="inlinerow">Event Title :</div>
                                <div class="eventsinfo-head"><?php echo $model->notehead ?></div>
                                <div class="inlinerow">Event date &amp; time :</div>
                                <div class="eventsdate"><?php echo date(yii::$app->params['dateFormat']['viewDateFormatDetailView'],strtotimeNew($model->activitydate)) ?></div>
                            </div>
                            <!-- /.Album info -->
                        </div>
                        <div class="col-md-6 col-sm-6 col-xs-12 text-right">
                     <div class="inlinerow Mtop15">
                                <!-- Controls -->
                                <a href='/event/index' class="btn btn-default backbutton"><span class="glyphicon glyphicon-chevron-left"></span>&nbsp; Back</a>
                                <div class="col-md-3 col-sm-3 pull-right">
                                    <?php $form = ActiveForm::begin(
                                        [   
                                            'action' => ['rsvp-listing','id'=> $model->id],
                                            'method' => 'get',
                                            'id' => 'rsvp-form'
                                        ]
                                    ); ?>
                                    <?= $form->field($searchModel, 'searchParam') ->dropDownList(
                                            [
                                                0 => 'Yes', 
                                                1 => 'No',
                                            ],
                                            [
                                                'prompt' => 'All',
                                                'id' => 'rsvpcountdropdown',
                                                'class' => 'form-control'
                                            ]
                                            )
                                        ->label(false);
                                        ?>      
                                     <?php ActiveForm::end(); ?>
                                </div>
                                <!-- /.Controls -->
                            </div>
                        </div>
                    </div>
                    <!-- /.Header -->
                    <!-- Events Listing Table -->
                    <div class="blockrow Mtop20">   
                    <?= GridView::widget([
         'dataProvider' => $dataProvider,
         'layout' => '{items}</tbody></table><div class="table-btn text-right">{pager}</div>',
         'tableOptions' => ['class' => 'table rsvptab'],
         'showFooter' => true,
         'footerRowOptions'=>[
                'style'=>'font-weight:bold;background-color: #e4f1ce;'
            ],
         'columns' => [
            [
                'attribute' => 'memberno',
                'label' => 'Member No',
                'footer' => "Total",
            ],
            [
                'attribute' => 'membername',
                'label' => 'Name'
            ],
            [
                'attribute' => 'phone',
                'label' => 'Mobile'
            ],
            [
                'attribute' => 'membercount',
                'label' => 'Member Count',
                'footer' => ExtendedRsvpdetails::getTotal($dataProvider->models, 'membercount'),
            ],
            [
                'attribute' => 'childrencount',
                'label' => 'Children Count',
                'footer' => ExtendedRsvpdetails::getTotal($dataProvider->models, 'childrencount'),
            ],
            [
                'attribute' => 'guestcount',
                'label' => 'Guest Count',
                'footer' => ExtendedRsvpdetails::getTotal($dataProvider->models, 'guestcount'),
            ],
            [
                'attribute' => 'acksentdatetime',
                'label' => 'Ack.Sent ON',
                'value' => function($model) {
                     return ($model['acksentdatetime']) ?
                    date('d-M-Y H:i',strtotimeNew($model['acksentdatetime'])) : '--';
                }
            ],
            
            [
              'contentOptions' => ['class' =>'text-center'],
              'class' => 'yii\grid\ActionColumn',
              'template' => '{acknowledge}',
              'buttons' => [
                'acknowledge' => function ($url, $model) {
                            if (!$model['acksentdatetime']) {
                                 return  Html::button(
                                'Acknowledge',
                                ['class' => 'btn btn-success btn-sm manage',
                                'id' => 'acknowledgeid',
                                'title' => Yii::t('yii', 'Acknowledge'),
                                'data-id' => $model['id'],
                                'data-eventId' => $model['rsvpid'],
                                'data-memberId' => $model['memberid'],
                                'data-userId' => $model['userid'],
                                'ackvalue' => 'disabled',
                                'ackvalue' => ''
                                
                                ]
                            );
                            } else {
                                     return  Html::button(
                                'Acknowledge',
                                [
                                'class' => 'btn btn-success btn-sm manage disabled',
                                'id' =>'acknowledgeid', 
                                'title' => Yii::t('yii', 'Acknowledge'),
                                'data-id' => $model['id'],
                                'data-eventId' => $model['rsvpid'],
                                'data-memberId' => $model['memberid'],
                                'data-userId' => $model['userid'],
                                'ackvalue' => 'disabled'
                                ]
                            );
                            }
                            
                    },   
              ],
  
            ],
        ],
    ]); ?>
                    </div>
                    <!-- /. Events Listing -->
                </div>
            </div>
        </div>
    </div>
<!-- Modal --> 
<div id="alertModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header-rsvp">
                <button class="close" data-dismiss="modal">&times;</button>     
            </div>
            <input type='hidden' id='rsvpid' value=''>
            <input type='hidden' id='memberid' value=''>
            <input type='hidden' id='userid' value=''>
            <input type='hidden' id='eventid' value=''>
            <div class="modal-body">
                                <div class="inlinerow">Event Title :</div>
                                <div class="eventsinfo-head"><?php echo $model->notehead ?></div>
                                <div class="inlinerow">Event date &amp; time :</div>
                                <div class="eventsdate"><?php echo date(yii::$app->params['dateFormat']['viewDateFormatDetailView'],strtotimeNew($model->activitydate)) ?></div>
                                 <div class="col-md-12 col-sm-12">
                   <br/>
                   <div hidden="hidden" class="alert alert-danger text-center" id="ErrorMessageLabel" role="alert"><strong>Message Body Cannot be blank</strong></div>
                  <textarea class="input-description" cols="20" id="modaltxtnotebody" maxlength="8000" name="NoteBody" rows="50" ></textarea>         
            </div>
            </div>
            <div class="modal-footer-rsvp">
                <div>
                <button class="btn btn-success" id="modalsendid" url="event/acknowledgemail/" reload-path ='event/rsvp-listing/<?php echo $model->id?>' data-id ='<?php echo $model->id?>'>
                    Send</button>
                <button class="btn btn-default"  id="closeid" data-dismiss="modal">
                    Close</button>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
<!-- Modal closed -->
</div>