<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\extendedmodels\ExtendedAffiliatedinstitution */

$this->title = 'Update Affiliated Institutions';
$this->params['breadcrumbs'][] = ['label' => 'Extended Affiliatedinstitutions', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->affiliatedinstitutionid]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="container">
    <div class="row">
        <div class="col-md-12 col-sm-12 pageheader Mtop15">Institution</div>
        <!-- Content -->
        <?php echo Html::hiddenInput('isRotary', $isRotary,
            [
                'id' => 'isRotary'
            ]
        ); ?>
        <div class="col-md-12 col-sm-12 contentbg">
            <div class="col-md-12 col-sm-12 Mtopbot20">
                <!-- Section head -->
                <fieldset>
                    <legend style="font-size: 20px;">Affiliated Institutions
                    </legend>
                    <?= $this->render('_form', [
        				'model' => $model, 'countryList' => $countryList,
                       'isRotary' => $isRotary,  
                        'meetingDays' => $meetingDays
    				]) ?>
    			</fieldset>
    		</div>
    	</div>
    </div>
</div>