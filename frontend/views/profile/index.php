<?php
/* @var $this yii\web\View */
$this->title = $member['name'];
?>
<style type="text/css">
.card {
    box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);
  transition: 0.3s;
  border-radius: 5px; /* 5px rounded corners */
  padding: 40px;
  background-color: white;
  margin-left: auto;
  margin-right: auto;
}
/* On mouse-over, add a deeper shadow */
.card:hover {
  box-shadow: 0 8px 16px 0 rgba(0,0,0,0.2);
}
/* Add some padding inside the card container */
.container {
  padding: 2px 16px;
}
.center {
  margin: auto;
  width: 50%;
  border: 3px solid green;
  padding: 10px;
}
</style>
<div class="site-index">
    <div class="">
        <div class="w3-row " style="padding: 10px;">
            <div class="card mb-3" style="max-width: 700px;">
                <div class="row no-gutters" style="max-width: 600px;">
                    <div class="col-md-4">
                        <img class="card-img-top"  width="" height="184"  src="<?=Yii::$app->params['imagePath'].$member['imageOrginal']?>">
                    <hr>
                    </div>
                    <div class="col-md-8">
                        <div class="card-body">
                            <h2 style='font-family: sans-serif;'><?=$member['name']?></h2> 
                            <?php
                            if($member['residence_address1']){ echo "<h4 style='font-family: sans-serif;'>".$member['residence_address1']."</h4>";}
                            if($member['residence_address2']){ echo "<h4 style='font-family: sans-serif;'>".$member['residence_address2']."</h4>";}
                            if($member['residence_pincode']){ echo "<h4 style='font-family: sans-serif;'>Pin - ".$member['residence_pincode']."</h4>";}
                            if($member['residence_district']){ echo "<h4 style='font-family: sans-serif;'>".$member['residence_district']."</h4>";}
                            if($member['residence_state']){ echo "<h4 style='font-family: sans-serif;'>".$member['residence_state']."</h4>";}
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="body-content">        

    </div>
</div>
