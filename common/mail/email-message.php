<html>
   <head>
      <title>Remember Profile Updation</title>
      <meta content="text/html; charset=iso-8859-1" http-equiv="Content-Type" />
   </head>
   <body marginheight="0" topmargin="0" marginwidth="0" leftmargin="0" style="background-color: #fff;">
      <table cellspacing="0" border="0" style="background-color: #fff;font-family:Tahoma, Geneva, sans-serif; font-size:13px" cellpadding="0" width="100%">
         <tr>
            <td valign="top" style="margin:0 auto">
               <table cellspacing="0" border="0" align="center" style="background: #fff; border: 1px solid #ccc; margin:0 auto" cellpadding="0" width="600">
                    <tr>
                      <td width="150" style="text-align:left;"><img width="96" height="72" src="<?= !empty($logo) ? $logo : $message->embed(Yii::getAlias('@backend'.'/assets/theme/images/main_logo_mdpi.png'));?>"/></td>
                      <td width="281" style="text-align:center;"><strong></strong></td>
                      <td width="150" style="text-align:right;"><img src="<?= $institutionLogo ?>" width="96" height="72"/>&nbsp;&nbsp;&nbsp;</td>
                     </tr>
                  <tr>
                     <td colspan="2">
                        <table cellspacing="0" border="0" cellpadding="0" width="100%" align="center">
                           <tr>
                              <td  valign="top" style="padding: 0 10px; font-family:Tahoma, Geneva, sans-serif; font-size: 13px;  color:#000; line-height:25px" width="600" colspan="2">
                                 <br>
                                 Dear <?= isset($toname)?$toname.',':'Sir/Madam,'?><br>
                                 <?= $content?><br>
                                 <br>
                                 <br>
                                 <span style="color:#0165AF;">Regards</span><br>
                                 <strong style="color:#0165AF;"><?= $name ?></strong><br>
                                 <span style="color:#666;">Member No : <?= $memberno?></span><br/></td>
                              </td>
                           </tr>
                           <tr>
                              <td colspan="2">&nbsp;</td>
                           </tr>
                           <tr>
                              <td colspan="2">&nbsp;</td>
                           </tr>
                           <tr>
                              <td style="padding: 0 10px; font-family:Tahoma, Geneva, sans-serif; font-size: 11px;  color:#808080; line-height:13px" width="600" colspan="2" >
                                 The contents of this email and any files transmitted with it are confidential and intended only for the individuals or entities to which they are addressed.  It may not be disclosed to, or used by, anyone other than the addressee, nor may it be copied in any way.  If you have received this email in error, please notify the sender by return email and then delete the email and any files transmitted with it from your system.  Please note that while reasonable effort has been made to ensure this message is free of viruses, opening and using this message is at the risk of the recipient and Re-Member does not accept responsibility for any loss arising from unauthorised access to, or interference with, any internet communications by any third party, or from the transmission of any viruses.<br/><br/> 
                              </td>
                           </tr>
                           <tr>
                              <td colspan="2">&nbsp;</td>
                           </tr>
                           <tr>
                              <td colspan="2">&nbsp;</td>
                           </tr>
                        </table>
                     </td>
                  </tr>
               </table>
            </td>
         </tr>
      </table>
   </body>
</html>