<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace backend\assets;

use yii\web\AssetBundle;


class IEAsset extends AssetBundle
{
    public $sourcePath = '@app/assets';
	public $baseUrl = '@web';
	
    public $css = [
	];

    public $jsOptions = ['condition' => 'lte IE 9'];

    public $js = [
    'theme/js/html5shiv.min.js',
    'theme/js/respond.min.js',	
    ];

    public $depends = [
    ];
}
