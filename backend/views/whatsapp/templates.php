<?php

use yii\helpers\Html;
use backend\assets\AppAsset;

/* @var $this yii\web\View */
/* @var $templates common\models\extendedmodels\ExtendedWhatsappTemplate[] */

$assetName = AppAsset::register($this);
$this->registerJsFile(
    $assetName->baseUrl . '/theme/js/Remember.whatsappSettings.ui.js',
    ['depends' => [AppAsset::className()]]
);

echo Html::hiddenInput('base-url', $assetName->baseUrl, ['id' => 'base-url']);
echo Html::hiddenInput('whatsapp-delete-template-Url', \Yii::$app->params['ajaxUrl']['whatsapp-delete-template-Url'], ['id' => 'whatsapp-delete-template-Url']);

$this->title = 'WhatsApp Templates';
?>
<div class="col-md-12 col-sm-12 contentbg Mtop15">

    <h1><?= Html::encode($this->title) ?></h1>
    <p><?= Html::a('&larr; Back to Settings', ['whatsapp/settings']) ?></p>

    <div class="alert alert-info">
        Enter the exact template name and language code as approved in Meta Business Manager.
        The variable map controls which value fills each <code>{{1}}</code>, <code>{{2}}</code>, ...
        placeholder in your approved template body — available values are
        <code>first_name</code>, <code>full_name</code>, <code>church_name</code>, and (anniversary only)
        <code>spouse_name</code>. Leave the map empty to use the default of <code>{{1}}</code>=name,
        <code>{{2}}</code>=church name.
    </div>

    <div class="col-md-12 col-sm-12">
        <fieldset>
            <legend id="whatsapp-template-form-legend">Add Template</legend>
            <?= Html::beginForm(['whatsapp/save-template'], 'post', ['id' => 'whatsapp-template-form']) ?>
                <?= Html::hiddenInput('id', '', ['id' => 'tpl-id']) ?>

                <div class="col-md-3 col-sm-3">
                    <div class="labelbox">Event Type</div>
                    <?= Html::dropDownList('event_type', 'birthday', ['birthday' => 'Birthday', 'anniversary' => 'Anniversary'], ['class' => 'form-control', 'id' => 'tpl-event-type']) ?>
                </div>
                <div class="col-md-3 col-sm-3">
                    <div class="labelbox">Meta Template Name</div>
                    <?= Html::textInput('meta_template_name', '', ['class' => 'form-control', 'id' => 'tpl-meta-name', 'maxlength' => 128]) ?>
                </div>
                <div class="col-md-2 col-sm-2">
                    <div class="labelbox">Language Code</div>
                    <?= Html::textInput('language_code', 'en', ['class' => 'form-control', 'id' => 'tpl-language', 'maxlength' => 10]) ?>
                </div>
                <div class="col-md-4 col-sm-4">
                    <div class="labelbox">Body Preview (admin reference only)</div>
                    <?= Html::textInput('body_preview', '', ['class' => 'form-control', 'id' => 'tpl-body-preview', 'maxlength' => 1024]) ?>
                </div>

                <div class="col-md-12 col-sm-12 Mtop10">
                    <div class="labelbox">Variable Map</div>
                    <div class="inlinerow">
                        <div class="col-md-2">{{<span id="tpl-pos-1">1</span>}} =</div>
                        <div class="col-md-3">
                            <?= Html::hiddenInput('map_position[]', '1') ?>
                            <?= Html::dropDownList('map_context_key[]', 'first_name', [
                                'first_name' => 'first_name', 'full_name' => 'full_name',
                                'church_name' => 'church_name', 'spouse_name' => 'spouse_name',
                            ], ['class' => 'form-control', 'id' => 'tpl-map-1']) ?>
                        </div>
                        <div class="col-md-2">{{2}} =</div>
                        <div class="col-md-3">
                            <?= Html::hiddenInput('map_position[]', '2') ?>
                            <?= Html::dropDownList('map_context_key[]', 'church_name', [
                                'first_name' => 'first_name', 'full_name' => 'full_name',
                                'church_name' => 'church_name', 'spouse_name' => 'spouse_name',
                            ], ['class' => 'form-control', 'id' => 'tpl-map-2']) ?>
                        </div>
                    </div>
                </div>

                <div class="col-md-12 col-sm-12 Mtop10">
                    <?= Html::checkbox('is_active', true, ['label' => 'Active']) ?>
                </div>

                <div class="col-md-12 col-sm-12 Mtop10">
                    <?= Html::submitButton('Save Template', ['class' => 'btn btn-primary']) ?>
                </div>
            <?= Html::endForm() ?>
        </fieldset>
    </div>

    <div class="segment">&nbsp;</div>

    <div class="col-md-12 col-sm-12 Mtop20">
        <table class="table">
            <thead>
                <tr>
                    <th>Event Type</th>
                    <th>Template Name</th>
                    <th>Language</th>
                    <th>Active</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($templates)) { ?>
                    <tr><td colspan="5">No templates configured yet.</td></tr>
                <?php } else { ?>
                    <?php foreach ($templates as $template) { ?>
                        <tr>
                            <td><?= Html::encode(ucfirst($template->event_type)) ?></td>
                            <td><?= Html::encode($template->meta_template_name) ?></td>
                            <td><?= Html::encode($template->language_code) ?></td>
                            <td><?= $template->is_active ? 'Yes' : 'No' ?></td>
                            <td>
                                <button type="button" class="btn btn-xs btn-default btn-whatsapp-edit-template"
                                    data-id="<?= $template->id ?>"
                                    data-event-type="<?= Html::encode($template->event_type) ?>"
                                    data-meta-name="<?= Html::encode($template->meta_template_name) ?>"
                                    data-language="<?= Html::encode($template->language_code) ?>"
                                    data-body-preview="<?= Html::encode($template->body_preview) ?>"
                                    data-map="<?= Html::encode($template->variable_map ?: '{"1":"first_name","2":"church_name"}') ?>">Edit</button>
                                <button type="button" class="btn btn-xs btn-danger btn-whatsapp-delete-template" data-id="<?= $template->id ?>">Delete</button>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } ?>
            </tbody>
        </table>
    </div>

</div>
<script>
    $(document).on('click', '.btn-whatsapp-edit-template', function () {
        var $row = $(this);
        $('#whatsapp-template-form-legend').text('Edit Template');
        $('#tpl-id').val($row.data('id'));
        $('#tpl-event-type').val($row.data('event-type'));
        $('#tpl-meta-name').val($row.data('meta-name'));
        $('#tpl-language').val($row.data('language'));
        $('#tpl-body-preview').val($row.data('body-preview'));

        var map = $row.data('map');
        if (typeof map === 'string') {
            try { map = JSON.parse(map); } catch (e) { map = {}; }
        }
        map = map || {};
        if (map['1']) { $('#tpl-map-1').val(map['1']); }
        if (map['2']) { $('#tpl-map-2').val(map['2']); }

        $('html, body').animate({ scrollTop: 0 }, 'fast');
    });
</script>
