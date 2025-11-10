<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\extendedmodels\ExtendedAlbum */

$this->title = 'Update Extended Album: {nameAttribute}';
$this->params['breadcrumbs'][] = ['label' => 'Extended Albums', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->albumid, 'url' => ['view', 'id' => $model->albumid]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="extended-album-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
