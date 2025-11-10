<?php 
if(!empty($selectedRoleCategroy)) {
foreach ($selectedRoleCategroy as $key => $value) { ?>
 <li>
 <input type="text" class="form-control category" name="update_field_name[<?= $value['RoleCategoryID'] ?>]" value="<?= $value['Description'] ?>" id="categorytextbox" categoryid="0" isdeleted="0" maxlength="100">
</li>  
<?php } } else {?>
 <li>
 <input type="text" class="form-control category" name="field_name[]" value="" id="categorytextbox" categoryid="0" isdeleted="0" maxlength="100">
</li>  
<?php }?>
 
