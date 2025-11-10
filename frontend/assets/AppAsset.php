<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class AppAsset extends AssetBundle
{
	public $sourcePath = '@app/assets/';
    public $baseUrl = '@web';
    
    public $css = [
        'theme/css/core.css',
        //'theme/css/bootstrap.css',
    ];
    public $js = [
       // 'theme/js/jquery.js',
        // 'theme/js/analytics.js',
        'theme/js/bootstrap.js',
        'theme/js/core.js',
    ];
    
    public $depends = [
    	'backend\assets\IEAsset',
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
    
}
