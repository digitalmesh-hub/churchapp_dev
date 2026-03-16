<?php

use yii\helpers\Html;

$this->title = 'Create Sunday Service';
$this->params['breadcrumbs'][] = ['label' => 'Sunday Services', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sunday-service-create">
    <div class="col-md-12 col-sm-12 pageheader Mtop15"><?= Html::encode($this->title) ?></div>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
