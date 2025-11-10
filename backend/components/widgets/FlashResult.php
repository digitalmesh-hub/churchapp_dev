<?php

namespace backend\components\widgets;

use yii\base\Widget;

/**
 * Created by amal
 * Date: 15/03/18
 * Time: 10:52 PM
 */

class FlashResult extends Widget
{
    public function init()
    {
        parent::init();
    }
    public function run()
    {
        return $this->render('flash_result');
    }
}