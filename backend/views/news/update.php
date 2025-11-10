<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\extendedmodels\ExtendedEvent */

$this->title = 'Update Event: {nameAttribute}';

?>
<div class="extended-event-update">
    <?= $this->render('_form', [
        'model' => $model,
        'members' => $members,
        'familyUnits' => $familyUnits,
        'batches' => $batches,
    ]) ?>

</div>
