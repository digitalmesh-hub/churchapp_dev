<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\extendedmodels\ExtendedEvent */

$this->title = 'Event Registration';

?>

<div class="extended-event-create">
	<div class="col-md-12 col-sm-12 pageheader Mtop15">Event Registration</div>
    <?= $this->render('_form', [
        'model' => $model,
    	'familyUnits' => $familyUnits,
    	'members' => $members,
		'memberModel' => $memberModel,
		'batches' => $batches
    ]) ?>

</div>
