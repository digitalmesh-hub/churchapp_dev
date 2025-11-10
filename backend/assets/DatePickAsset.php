<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace backend\assets;

use yii\web\AssetBundle;

class DatePickAsset extends AssetBundle
{
	public $sourcePath = '@app/assets/';
	public $baseUrl = '@web';
	
    public $css = [
	    'theme/css/datepick.css',
        'theme/css/core.css',
        'theme/css/bootstrap-dialog.min.css',
        'theme/css/sweetalert.css',
        'theme/dist/summernote.css',
        'theme/css/custom.css',
        'theme/dist_tags/bootstrap-tagsinput.css',
    ];

    public $js = [
    	'theme/js/datepick.js',
        'theme/js/jquery-1.11.2.min.js',
        //'theme/js/bootstrap.min.js'
    ];

    public $depends = [
        'backend\assets\AppAsset',
    ];
}
