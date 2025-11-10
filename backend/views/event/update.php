<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\extendedmodels\ExtendedEvent */

$this->title = 'Event Registration';
?>
<div class="extended-event-update">
	<div class="col-md-12 col-sm-12 pageheader Mtop15">Event Registration</div>
    <?= $this->render('_form', [
        'model' => $model,
        'members' => $members,
        'familyUnits' => $familyUnits,
        'batches' => $batches
    ]) ?>

</div>
