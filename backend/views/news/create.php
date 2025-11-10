<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\extendedmodels\ExtendedEvent */

$this->title = 'News';
?>
<div class="extended-event-create">
    <?= $this->render('_form', [
        'model' => $model,
    	'members' => $members,
        'familyUnits' => $familyUnits,
        'batches' => $batches,
    ]) ?>

</div>
