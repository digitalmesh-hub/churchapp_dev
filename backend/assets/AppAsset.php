<?php

namespace backend\assets;

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
        'theme/css/bootstrap-dialog.min.css',
        'theme/css/sweetalert.css',
        'theme/dist/summernote.css',
        'theme/css/custom.css',
        'theme/css/select2.css',
        'theme/dist_tags/bootstrap-tagsinput.css',
    ];
    public $js = [
        '//cdnjs.cloudflare.com/ajax/libs/lodash.js/3.10.1/lodash.min.js',
    	'theme/js/ie10-viewport-bug-workaround.js',
    	'theme/js/jquery-1.11.2.min.js',
    	'theme/js/bootstrap.min.js',
    	'theme/js/bootstrap-dialog.min.js',
    	'theme/js/bootbox.min.js',
    	'theme/js/jsFramework.lib.core.js',
    	'theme/js/Remember.common.ui.js',
        'theme/js/sweetalert.js',
        'theme/dist/summernote.js',
        'theme/js/select2.min.js',
        'theme/dist_tags/bootstrap-tagsinput.js',
    ];
    
    public $depends = [
    	'backend\assets\IEAsset',
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
    
}
