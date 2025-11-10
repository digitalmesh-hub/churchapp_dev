<?php
use yii\helpers\Html;
?>
<html><head><title>RSVP Email</title>
<meta content='text/html; charset=iso-8859-1' http-equiv='Content-Type'></head>
<body marginheight='0' topmargin='0' marginwidth='0' leftmargin='0' style='background-color: #fff;'>
<table cellspacing='0' border='0' style='background-color: #fff;font-family:Tahoma, Geneva, sans-serif; font-size:13px' cellpadding='0' width='100%'>
<tbody><tr>
<td valign='top' style='margin:0 auto'>
<table cellspacing='0' border='0' align='center' style='background: #fff; border: 1px solid #ccc; margin:0 auto' cellpadding='0' width='600'>
<tbody><tr>
 <td width="129" style="text-align:center;"><img style="width:190px" src="<?= $institutionLogo ?>"/>&nbsp;&nbsp;&nbsp;</td>
</tr><tr>
<td colspan='2'>
<table cellspacing='0' border='0' cellpadding='0' width='100%' align='center'>
<tbody><tr>
<td valign='top' style='padding: 0 10px; font-family:Tahoma, Geneva, sans-serif; font-size: 13px;  color:#000; line-height:25px' width='600' colspan='2'>
<br>Dear Sir/Madam,<br>
I will be attending the <strong> <?= Html::encode($notehead);?></strong> on <?= Html::encode($transactiondate);?>  with the number of attendees listed below.<br><br>
<table cellpadding='4' cellspacing='0' style='padding:5px; border:1px solid #a0a0a0; width:100%; margin-bottom:30px; font-size:13px;'>
<tbody><tr><th colspan='2' style='border-bottom:1px solid #CCC;'>Attendees</th></tr>
<tr>
<td width='465' style='padding:10px;'>Member</td>
                        <td width='95' style='text-align:center; padding:10px;'><?= $member;?></td>
</tr>
<tr>
<td style='padding:10px;'>Guest</td>
 <td style='text-align:center; padding:10px;'><?= $guest;?></td>
   </tr>
<tr>
<td style='padding:10px;'>Children</td>
<td style='text-align:center; padding:10px;'><?= $children;?></td>
</tr>
<tr>
<td style='border-top:1px solid #CCC; padding:10px;'><strong>Total</strong></td>
<td style='text-align:center; padding:10px; border-top:1px solid #CCC;'><strong><?= $total;?></strong></td>
</tr>
</tbody></table>
<span style='color:#0165AF;'>Regards</span><br>
<strong style='color:#0165AF;'> <?= Html::encode($name);?></strong><br>
</td></tr>
<tr><td colspan='2'>&nbsp;</td></tr>
<tr> <td colspan='2'>&nbsp;</td> </tr>
<tr><td style='padding: 0 10px; font-family:Tahoma, Geneva, sans-serif; font-size: 11px;  color:#808080; line-height:13px' width='600' colspan='2'>
The contents of this email and any files transmitted with it are confidential and intended only for the individuals or entities to which they are addressed.
</td></tr>
<tr><td colspan='2'>&nbsp;</td></tr>
<tr><td colspan='2'>&nbsp;</td></tr>
</tbody></table></td></tr>
</tbody></table></td></tr></tbody></table>
</body></html>
