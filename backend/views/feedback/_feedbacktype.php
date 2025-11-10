
 <?php
 use yii\helpers\Html;
 use yii\helpers\Url;
 use common\models\extendedmodels\ExtendedFeedbacktype;
 ?>
<?php 
    $feedbackdescription =  ExtendedFeedbacktype::getFeedbackDescription($model->feedbacktypeid);
    $data = ExtendedFeedbacktype::getFeedbackOrder($model->institutionid);
 ?>  
<tbody id="tablebody">
                <tr>
                    <td>
                       <?= $feedbackdescription['description'] ?>     
                    </td>
                    <td class="text-center">
                <?php if($model->order == 0){ ?>
                             <div class="sortbox">
                             </div>
                             <div class="sortbox">
                            <input nextorder="<?= $model->order + 1 ?>" url="feedback/sort-feedback-type/"  id="<?= $model->institutionfeedbacktypeid ?>"  class="sortarrow-down" order="<?= $model->order ?>" type="button"></div>

                <?php } if ($model->order == $data['ordervalue']) {
                    
                ?>      <div class="sortbox">
                            <input previousorder="<?= $model->order - 1 ?>" url="feedback/sort-feedback-type/"  id="<?= $model->institutionfeedbacktypeid ?>" class="sortarrow-up" order="<?= $model->order ?>" type="button"></div>
                            <div class="sortbox">
                             </div>
                <?php } elseif($model->order != $data['ordervalue'] && $model->order != 0)  { ?>
                         <div class="sortbox">
                            <input previousorder="<?= $model->order - 1 ?>" url="feedback/sort-feedback-type/"  id="<?= $model->institutionfeedbacktypeid ?>" class="sortarrow-up" order="<?= $model->order ?>" type="button"></div>
                        <div class="sortbox">
                            <input nextorder="<?= $model->order + 1 ?>" url="feedback/sort-feedback-type/"  id="<?= $model->institutionfeedbacktypeid ?>"  class="sortarrow-down" order="<?= $model->order ?>" type="button"></div>
                        <?php } ?>
                       </td>
                    <td class="text-center">
                    <?php if($model->feedbacktypeid != 1) {
                             if($model->active == '1')  {?> 
                        <button class="btn btn-danger btn-sm w70p activate" url="feedback/deactivate/"  id="btn-deactivate" feedbacktypeid="<?= $model->feedbacktypeid ?>">Deactivate</button>  
                        <?php }else {?>
                         <button class="btn btn-success btn-sm w70p activate" url="feedback/activate/" id="btn-activate" feedbacktypeid="<?= $model->feedbacktypeid ?>">Activate</button>
                    <?php } } ?>

                    </td>
                </tr>
                <tr>    
            </tbody>



