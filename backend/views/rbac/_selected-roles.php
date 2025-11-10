<?php 

if(!empty($selectedRoles)) { 
foreach ($selectedRoles as $key => $value) { ?>
 <li>
 <input type="text" class="form-control roles" name="update_field_name[<?=$value['roleid']?>]" value="<?= $value['roledescription'] ?>" id="rolestextbox" rolesid = true isdeleted="0" maxlength="100">
</li>  
<?php } } else { ?>
<li>
 <input type="text" class="form-control roles" roleid="0" isdeleted="0" name="field_name[roledescription][]" value="" id="rolestextbox" maxlength="100">
</li> 
<?php } ?>
 