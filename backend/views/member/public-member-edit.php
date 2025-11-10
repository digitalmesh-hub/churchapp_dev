<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\assets\AppAsset;
use kartik\date\DatePicker;
use common\models\extendedmodels\ExtendedInstitution;
use backend\components\widgets\FlashResult;

$assetName = AppAsset::register($this);

$this->title = 'Edit Member-Details';

$this->registerJsFile(
    $assetName->baseUrl . '/theme/js/Remember.memberEdit.ui.js',
    [
        'depends' => [
            AppAsset::className()
        ]
    ]
);

echo Html::hiddenInput(
    'base-url',
    $assetName->baseUrl,
    [
        'id' => 'base-url'
    ]
);
$style= <<< CSS
input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}
input[type=number] {
    -moz-appearance: textfield;
    /* Firefox */
}
@media only screen and (min-width: 769px) {
    .form-control.mobileNumber {
        width: 20%;
    }
    .form-control.otp {
        width: 15%;
    }
}
.container .blockrow.Mtop20 button[type=submit] {
    margin-left: 15px;
    margin-right: auto;
}
.member-edit ul li {
    font-size: 15px;
    margin-bottom: 15px;
}
/* .member-edit ul {
    list-style: none;
}
.member-edit ul li::marker {
  content: "📌 ";
} */
@media (max-width: 768px) {
    .container {
        width: 268px !important;
    }
    .container .blockrow.Mtop20 input {
        width: 175px !important;
    }
}
CSS;
$this->registerCss($style);
?>
<!-- Contents -->
<div class="container">
    <div class="row">
        <!-- Content -->
        <div class="col-md-12 col-sm-12 contentbg">
            <div class="col-md-12 col-sm-12 Mtopbot20">
                <fieldset>
                    <form method="POST">
                        <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />
                        <div class="blockrow Mtop20">
                            <div class="col-md-12 Mtop10">
                                <div>
                                    <?php if($showOTPBox): ?>
                                        <label>OTP</label>
                                        <div class="field_wrapper">
                                            <input type="hidden" class="form-control" name="memberMobileNumber" id="memberMobileNumber" value="<?=$memberPhoneNumber?>">
                                            <input type="number" class="form-control otp" name="otp" id="otp" oninput="javascript: if (this.value.length > 4) this.value = this.value.slice(0, 4);" value="<?=$otp?>" required>
                                        </div>
                                    <?php else : ?>
                                        <label>Mobile Number</label>
                                        <div class="field_wrapper">
                                            <input type="number" class="form-control mobileNumber" name="memberMobileNumber" id="memberMobileNumber" oninput="javascript: if (this.value.length > 10) this.value = this.value.slice(0, 10);" value="<?=$memberPhoneNumber?>" required>
                                        </div>
                                    <?php endif; ?>
                                    <?php if(!empty($errorMessage)): ?>
                                        <div style="margin: 5px 0;">
                                            <span style="color:red;"> <?=$errorMessage?></span>
                                        </div>
                                    <?php elseif(!empty($successMessage)) : ?>
                                        <div style="margin: 5px 0;">
                                            <span style="color:green;"> <?=$successMessage?></span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success Mtop20" title="Submit">Submit</button>
                            <?php if($showOTPBox): ?>
                            <button type="button" class="btn btn-warning Mtop20" title="Cancel" onclick="location.reload();">Cancel</button>
                            <?php endif; ?>
                        </div>
                    </form>
                </fieldset>
                <div class="member-edit">
                    <p><h4> Instructions to Members: </h4></p>
                    <ul>
                        <li>Please enter your 10 digit Mobile number that has been registered with Lotus Club.</li>
                        <li>If the number is registered with Lotus Club you will receive a 4 digit OTP. If your mobile number is not registered with Lotus Club, please contact the club office to get your number registered.</li>
                        <li>Enter the OTP and on successful validation, you will be taken to the Member Edit page.</li>
                        <li>You will be able to correct any details <strong><u>except your Mobile Number.</u></strong> To correct the mobile number please contact the club office.</li>
                        <li>Click on the Save Button.</li>
                    </ul>
                </div>
                <!-- contents -->
            </div>
        </div>
    </div>
</div>
