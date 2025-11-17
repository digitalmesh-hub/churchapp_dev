<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\extendedmodels\ExtendedZone */

$this->title = 'Create Zone';
$this->params['breadcrumbs'][] = ['label' => 'Zones', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="extended-zone-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
