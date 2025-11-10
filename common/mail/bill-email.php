<?php
use yii\helpers\Html;
?>
<html>
<head>
<title>Re-member</title>
<meta content="text/html; charset=iso-8859-1" http-equiv="Content-Type" />
</head>
<body marginheight="0" topmargin="0" marginwidth="0" leftmargin="0" style="background-color: #fff;">
<table cellspacing="0" border="0" style="background-color: #fff;font-family:Tahoma, Geneva, sans-serif; font-size:13px" cellpadding="0" width="100%">
  <tr>
    <td valign="top" style="margin:0 auto"><table cellspacing="0" border="0" align="center" style="background: #fff; border: 1px solid #ccc; margin:0 auto" cellpadding="0" width="600">
        <tr>
				<td width="135" style="text-align:left;"><img style="width:120px; height:120px;" src="<?= !empty($logo) ? $logo : $message->embed(Yii::getAlias('@backend'.'/assets/theme/images/main_logo_mdpi.png'));?>"/></td>
                <td width="281" style="text-align:center;"><strong>Bills</strong></td>
               <td width="129" style="text-align:right;"><img style="width:120px; height:120px;" src="<?= $institutionLogo ?>"/>&nbsp;&nbsp;&nbsp;</td>
        </tr>
        <tr>
          <td colspan="3"><table cellspacing="0" border="0" cellpadding="0" width="100%" align="center">
              <tr>
                <td  valign="top" style="padding: 0 10px; font-family:Tahoma, Geneva, sans-serif; font-size: 13px;  color:#000; line-height:25px" width="600" colspan="2"><br>
                  Dear <?= Html::encode($name);?>,<br>
                 <br>
                 
                  Your bill details are given below for your reference. <br/>
                  <br/>
                  </td>
              </tr>
              <tr>
                <td colspan="2" style="padding:10px;"><!-- data table -->
                  
                  <table style="width:100%;border:1px solid #ddd; margin:0 auto;font-family:Arial, Helvetica, sans-serif; vertical-align:top;">
                    <tbody>
                      <tr>
                        <th colspan="3"  style="border-bottom:1px solid #ddd;padding:5px; font-size:14px; background: #755f88; color: #FFF;"><?= Html::encode($month);?>&nbsp;&nbsp;<?= Html::encode($year);?></th>
                      </tr>
                      <tr style="border-bottom:3px solid #ddd;padding:5px; font-size:14px;font-weight:bold;width:100%; " >
                        <td style="border-bottom:1px solid #ddd;padding:5px; font-size:14px; color: #656565;">Description </td>
                        <td style="border-bottom:1px solid #ddd;padding:5px; font-size:14px; color: #656565;text-align:right;"> Dr.</td>
                        <td style="border-bottom:1px solid #ddd;padding:5px; font-size:14px; color: #656565;text-align:right;"> Cr.</td>
                      </tr>
                       <tr style="padding:5px; font-size:14px;background:#f7f7f7;  ">
                        <td style="padding:5px; color:#757575;border-left: 2px solid #f9f9f9; font-weight:bold;">Current Opening Balance </td>
                        <td style="padding:5px; color:#000;text-align:right; font-weight:bold;"><?= $openingdebit;?></td>
                        <td style="padding:5px; color:#000;text-align:right; font-weight:bold;"><?= $openingcredit;?></td>
                      </tr>
                      <?php if(!empty($transdata)){
                      foreach ($transdata as $key => $value){?>
                      <tr>
                        <td colspan="3" style="padding:5px; font-size:14px; color:#4689bb;font-weight:bold;"><?= Html::encode(date('d-m-Y',strtotimeNew($value['transactiondate'])));?></td>
                      </tr>
                      <?php foreach ($value['billlist'] as $key => $data){?>
                      <tr style="padding:5px; font-size:14px; ">
                        <td style="padding:5px; color:#757575;border-left: 2px solid #f9f9f9;"><?= Html::encode($data['description']);?></td>
                        <?php if ($data['debit']) { ?> 
                          <td style="padding:5px; color:#000;text-align:right;"><?= $data['debit'];?></td>
                        <?php }?>
                        <?php if ($data['credit']) { ?> 
                          <td colspan="5" style="padding:5px;  color:#000;text-align:right;"><?= $data['credit'];?></td>
                        <?php }?>
                      </tr> 
                      <?php }
                      }
						          }?>
                      <tr style="padding:15px 5px; font-size:14px; background:#e5f9d4; ">
                        <td style="padding:10px 5px; color:#389002; border-left: 2px solid #f9f9f9;font-weight:bold;">Grand Total </td>
                        <td style="padding:10px 5px; color:#389002;text-align:right;font-weight:bold;"><?=  yii::$app->MoneyFormat->decimalWithComma($grandtotaldebit);?></td>
                        <td style="padding:10px 5px; color:#389002;text-align:right;font-weight:bold;"><?= yii::$app->MoneyFormat->decimalWithComma($grandtotalcredit);?></td>
                      </tr>
                      <tr style="padding:15px 5px; font-size:14px;background:#d4e5f9; ">
                        <td style="padding:10px 5px; color:#075299; border-left: 2px solid #f9f9f9;font-weight:bold;">Balance </td>
                        <td style="padding:10px 5px; color:#075299;text-align:right;font-weight:bold;"><?= ($balancedebit > 0) ? yii::$app->MoneyFormat->decimalWithComma($balancedebit) : '';?></td>
                        <td style="padding:10px 5px; color:#075299;text-align:right;font-weight:bold;"><?= ($balancecredit > 0) ? yii::$app->MoneyFormat->decimalWithComma($balancecredit) : '';?></td>
                      </tr>
                    </tbody>
                  </table>
                  
                  <!-- /.data table --></td>
              </tr>
              <tr>
                <td colspan="2" style="padding:10px; font-size:12px; line-height:25px;"><span style="color:#0165AF;">Kind regards</span><br/>
                  <strong style="color:#0165AF;">ADMIN</strong><br/>
                  <span style="color:#666;"><?= Html::encode($institutionname);?></span><br/></td>
              </tr>
              <tr>
                <td colspan="2">&nbsp;</td>
              </tr>
              <tr>
                <td colspan="2">&nbsp;</td>
              </tr>
              <tr>
                <td style="padding: 0 10px; font-family:Tahoma, Geneva, sans-serif; font-size: 11px;  color:#808080; line-height:13px" width="600" colspan="2" > The contents of this email and any files transmitted with it are confidential and intended only for the individuals or entities to which they are addressed.  It may not be disclosed to, or used by, anyone other than the addressee, nor may it be copied in any way.  If you have received this email in error, please notify the sender by return email and then delete the email and any files transmitted with it from your system.  Please note that while reasonable effort has been made to ensure this message is free of viruses, opening and using this message is at the risk of the recipient and Re-Member does not accept responsibility for any loss arising from unauthorised access to, or interference with, any internet communications by any third party, or from the transmission of any viruses.<br/>
                  <br/></td>
              </tr>
              <tr>
                <td colspan="2">&nbsp;</td>
              </tr>
              <tr>
                <td colspan="2">&nbsp;</td>
              </tr>
            </table></td>
        </tr>
      </table></td>
  </tr>
</table>
</body>
</html>