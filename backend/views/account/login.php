<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\BaseUrl;
use yii\bootstrap\ActiveForm;
use backend\assets\LoginAsset;

$assetName = LoginAsset::register($this);
$this->title = 'Login';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="container">
<?php $form = ActiveForm::begin(
    [
		'enableClientValidation' => false,
		'options' => [
        'class' => 'form-signin Mtop50'
    ],
    'fieldConfig' => [
        'template' => "{beginWrapper}\n{input}\n{hint}\n{endWrapper}",
        'horizontalCssClasses' => [
            'label' => 'col-sm-4',
            'offset' => 'col-sm-offset-4',
            'wrapper' => 'col-sm-8',
            'error' => '',
            'hint' => '',
        ],
    ]
    ]
	);
?>
<?php
$session = Yii::$app->session;
if ($session->has('institutionInactive')): ?>
    <div class="alert alert-danger">
        <?= $session->get('institutionInactive') ?>
    </div>
<?php 
    $session->remove('institutionInactive'); // Clear message after displaying
endif; ?>
	<div class="filter-rows text-center Mbot40">
		<img src="<?php echo $assetName->baseUrl; ?>/theme/images/login-logo.png" />
	</div>
	<div class="inlinerow">
		<div class="required">
            <?= $form->field($loginModel, 'username')->textInput(['placeholder' => 'Email address'])->label(false) ?>
		</div>
	</div>
	<div class="inlinerow ">
		<div class="required">
             <?= $form->field($loginModel, 'password')->passwordInput(
            [
             'placeholder' => 'Password'
            ]
            )->label(false) ?>
		</div>
	</div>
    <div class="inlinerow">
        <?= $form->field($loginModel, 'rememberMe')->checkbox() ?>     
    </div>
    <div class="inlinerow loginvalueerror">
		<?php if(isset($_POST['LoginForm'])){
			echo $form->errorSummary($loginModel,['header' => ""]);
		} ?>
    </div>
    <?= Html::submitButton('Sign in', ['class' => 'btn btn-lg btn-success btn-block', 'name' => 'login-button']) ?>
      
<?php ActiveForm::end(); ?>    
</div>
