<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\assets\AppAsset;

/* @var $this yii\web\View */
/* @var $model common\models\extendedmodels\ExtendedInstitutionWhatsappConfig */
/* @var $isNew bool */

$assetName = AppAsset::register($this);
$this->registerJsFile(
    $assetName->baseUrl . '/theme/js/Remember.whatsappSettings.ui.js',
    ['depends' => [AppAsset::className()]]
);

echo Html::hiddenInput('base-url', $assetName->baseUrl, ['id' => 'base-url']);
echo Html::hiddenInput('whatsapp-send-test-Url', \Yii::$app->params['ajaxUrl']['whatsapp-send-test-Url'], ['id' => 'whatsapp-send-test-Url']);
echo Html::hiddenInput('whatsapp-send-now-Url', \Yii::$app->params['ajaxUrl']['whatsapp-send-now-Url'], ['id' => 'whatsapp-send-now-Url']);

$this->title = 'WhatsApp Settings';
?>
<div class="col-md-12 col-sm-12 contentbg Mtop15">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php if (!$isNew) { ?>
    <div class="col-md-12 col-sm-12 Mtop10">
        <div class="alert <?= $model->is_active ? 'alert-success' : 'alert-warning' ?>">
            WhatsApp greetings are currently
            <strong><?= $model->is_active ? 'ENABLED' : 'DISABLED' ?></strong> for your institution.
        </div>
    </div>
    <?php } ?>

    <?php $form = ActiveForm::begin(['id' => 'whatsapp-settings-form']); ?>

    <div class="col-md-6 col-sm-6 Mtop20">
        <fieldset>
            <legend>WhatsApp Business Account</legend>

            <?= $form->field($model, 'waba_id')->textInput(['maxlength' => 64])
                ->hint('From Meta Business Manager > WhatsApp Business Accounts.') ?>

            <?= $form->field($model, 'phone_number_id')->textInput(['maxlength' => 64])
                ->hint('The Phone Number ID (not the phone number itself) used as the sender.') ?>

            <?= $form->field($model, 'display_phone')->textInput(['maxlength' => 20])
                ->hint('Optional — the human-readable sender number, for display only.') ?>

            <?= $form->field($model, 'access_token')->passwordInput(['maxlength' => 2000, 'autocomplete' => 'new-password'])
                ->hint($isNew ? 'The permanent or long-lived access token for this WABA.' : 'Leave blank to keep the currently saved token unchanged.') ?>

            <?= $form->field($model, 'api_version')->textInput(['maxlength' => 10]) ?>
        </fieldset>
    </div>

    <div class="col-md-6 col-sm-6 Mtop20">
        <fieldset>
            <legend>Sending</legend>

            <?= $form->field($model, 'send_mode')->dropDownList([
                'manual' => 'Manual — admin clicks Send Now',
                'auto' => 'Automatic — daily at the send time below',
            ]) ?>

            <?= $form->field($model, 'send_time')->textInput(['type' => 'time'])
                ->hint('Local time for this institution (auto mode only).') ?>

            <div class="inlinerow Mtop10">
                <?= $form->field($model, 'is_active')->checkbox(['label' => 'Enable WhatsApp greetings for this institution']) ?>
            </div>
        </fieldset>
    </div>

    <div class="col-md-12 col-sm-12 Mtop20">
        <?= Html::submitButton('Save Settings', ['class' => 'btn btn-primary btn-lg']) ?>
        <?= Html::a('Templates', ['whatsapp/templates'], ['class' => 'btn btn-default btn-lg']) ?>
        <?= Html::a('Message Log', ['whatsapp/message-log'], ['class' => 'btn btn-default btn-lg']) ?>
    </div>

    <?php ActiveForm::end(); ?>

    <?php if (!$isNew) { ?>
    <div class="col-md-12 col-sm-12 Mtop20">
        <div class="segment">&nbsp;</div>
        <fieldset>
            <legend>Test &amp; Manual Send</legend>
            <div class="col-md-6 col-sm-6">
                <div class="labelbox"><strong>Send a test message</strong></div>
                <div class="inlinerow Mtop10">
                    <input type="text" id="whatsapp-test-number" class="form-control" placeholder="Recipient number with country code, digits only">
                </div>
                <div class="inlinerow Mtop10">
                    <select id="whatsapp-test-event-type" class="form-control">
                        <option value="birthday">Birthday template</option>
                        <option value="anniversary">Anniversary template</option>
                    </select>
                </div>
                <div class="inlinerow Mtop10">
                    <?= Html::button('Send Test Message', ['class' => 'btn btn-default', 'id' => 'btn-whatsapp-send-test']) ?>
                </div>
            </div>
            <div class="col-md-6 col-sm-6">
                <div class="labelbox"><strong>Send today's greetings now</strong></div>
                <p>Sends birthday/anniversary greetings for anyone matching today's date who hasn't already been sent one, regardless of send mode.</p>
                <?= Html::button('Send Now', ['class' => 'btn btn-success', 'id' => 'btn-whatsapp-send-now']) ?>
            </div>
        </fieldset>
    </div>
    <?php } ?>

</div>
