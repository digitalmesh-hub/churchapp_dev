<?php

namespace backend\assets;

use yii\web\AssetBundle;

class MemberAsset extends AssetBundle
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
        'theme/js/bootstrap.min.js'
    ];

    public $depends = [
        'backend\assets\IEAsset',
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
