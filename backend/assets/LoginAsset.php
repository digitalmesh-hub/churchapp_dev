<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class LoginAsset extends AssetBundle
{
    public $sourcePath = '@app/assets/';
    public $baseUrl = '@web';
    
    public $css = [
        'theme/css/core.css'
    ];
    public $js = [
            
    ];
    
    public $depends = [
        'backend\assets\IEAsset',
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
    
}