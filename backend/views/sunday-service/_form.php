<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use backend\assets\AppAsset;

$assetName = AppAsset::register($this);
$this->registerJsFile($assetName->baseUrl . '/theme/js/Remember.Event.ui.js', [
    'depends' => [
        AppAsset::className()
    ]
]);

// Register custom script to remove picture button after Summernote loads
$this->registerJs("
$(window).on('load', function() {
    setTimeout(function() {
        // Remove picture upload button
        $('.note-icon-picture').closest('button').parent().remove();
        
        // Increase editor height
        $('#txtcontent').summernote('destroy');
        $('#txtcontent').summernote({
            height: 200,
            dialogsInBody: true,
            toolbar: [
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['font', ['strikethrough', 'superscript', 'subscript', 'fontname']],
                ['fontsize', ['fontsize']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph', 'table', 'hr']],
                ['height', ['height']],
                ['insert', ['link']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ]
        });
    }, 500);
});
", \yii\web\View::POS_END);
?>
<div class="sunday-service-form">
    <div class="col-md-12 col-sm-12 contentbg">
        <div class="col-md-12 col-sm-12 Mtopbot20">
            <div class="blockrow Mtop20">
                <div class="inlinerow Mtop10">
                    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

                    <div class="col-md-12 col-sm-12 Mtop20">
                        <div class="inlinerow Mtop10">
                            <div class="col-md-2 col-sm-2 L32">
                                Service Date <span style="color: red;">*</span>
                            </div>
                            <div class="col-md-6 col-sm-6">
                                <?= $form->field($model, 'service_date')->widget(DatePicker::classname(), [
                                    'options' => [
                                        'placeholder' => 'Choose date',
                                        'id' => 'sunday-service-date',
                                    ],
                                    'pluginOptions' => [
                                        'autoclose' => true,
                                        'format' => 'dd MM yyyy',
                                        'startDate' => '0d', // Prevent past dates
                                    ]
                                ])->label(false); ?>
                            </div>
                        </div>

                        <div class="inlinerow Mtop20">
                            <div class="col-md-2 col-sm-2 L32">
                                Service Content <span style="color: red;">*</span>
                            </div>
                            <div class="col-md-10 col-sm-10 z-index-11">
                                <?= $form->field($model, 'content')->textarea([
                                    'rows' => '10',
                                    'class' => 'form-control input-description',
                                    'id' => 'txtcontent'
                                ])->label(false) ?>
                            </div>
                        </div>

                        <div class="inlinerow Mtop20">
                            <div class="col-md-2 col-sm-2 L32">
                                Active Status
                            </div>
                            <div class="col-md-6 col-sm-6">
                                <?= $form->field($model, 'active')->dropDownList(
                                    [1 => 'Active', 0 => 'Inactive'],
                                    ['prompt' => 'Select Status']
                                )->label(false) ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 col-sm-12 Mtop20 Mbottom20">
                        <div class="form-group">
                            <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                            <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-default']) ?>
                        </div>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.z-index-11 {
    z-index: 11 !important;
}
.L32 {
    line-height: 32px;
}
/* Fix modal z-index issues */
.note-modal-backdrop {
    z-index: 1050 !important;
}
.note-modal {
    z-index: 1060 !important;
}
</style>
