<?php
namespace backend\assets;
use yii\web\AssetBundle;

class TransactionAsset extends AssetBundle {
	
	public $sourcePath = '@app/assets/';
	public $baseUrl = '@web';
	
	public $css = [
			'theme/css/jquery.simple-dtpicker.css' 
	];
	
	public $js = [
			'theme/js/jquery.simple-dtpicker.js',
			'theme/js/Remember.transaction.ui.js',
	];
	
	public $depends = [ 
			'backend\assets\AppAsset',
    ];
}
