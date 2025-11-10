  <!-- Contents -->
    <div class="container">
         <div class="row">
              <!-- Header -->
              
              <?php if(isset($type) && $type == 'error'){?>
<div class="col-md-12 col-sm-12 col-xs-12 pageheader Mtop15">
    Member details can not be displayed</div>
<?php }else{?>
<div class="col-md-12 col-sm-12 col-xs-12 pageheader Mtop15">
    Details stored successfully</div>
<?php }?>
<!-- Content -->
<div class="col-md-12 col-sm-12 col-xs-12 contentbg">
    <fieldset>
        <?php if(!empty($institutionName)) : ?>
            <legend style="text-align: center;"><?=$institutionName?></legend>
        <?php endif; ?>
<?php if(isset($type) && $type == 'error'){?>
    <div class="site-error">
        <br>    
        <div class="alert alert-danger text-center">
            Member details not available</div>
        <br>
        <p></p>
        <p></p>

    </div>
<?php }else{?>
    <div class="site-error">
        <br>    
        <div class="alert alert-success text-center">
            Details stored successfully</div>
        <br>
        <p></p>
        <p></p>

    </div>
    <?php }?>
    </fieldset>
</div> 
         </div>
    </div>