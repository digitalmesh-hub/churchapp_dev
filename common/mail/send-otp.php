<?php
use yii\helpers\Html;
?>
<?php $this->beginPage() ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Remember Email</title>
<meta content="text/html; charset=iso-8859-1" http-equiv="Content-Type" />
<?php $this->head() ?>
</head>
<body marginheight="0" topmargin="0" marginwidth="0" leftmargin="0"	style="background-color: #fff;">
<?php $this->beginBody() ?>
<table cellspacing="0" border="0"
		style="background-color: #fff; font-family: Tahoma, Geneva, sans-serif; font-size: 13px"
		cellpadding="0" width="100%">
		<tr>
			<td valign="top" style="margin: 0 auto">
				<table cellspacing="0" border="0" align="center" style="background: #fff; border: 1px solid #ccc; margin: 0 auto" cellpadding="0" width="600">
					<tr>
						<td style="text-align: center;"><img style="cursor: pointer; max-width: 150px; height: auto; min-height: auto; min-width: auto;" src="<?= !empty($logo) ? $logo : $message->embed(Yii::getAlias('@backend'.Yii::$app->params['email']['logoPath']));?>" /></td>
					</tr>
					<tr>
						<td>
							<table cellspacing="0" border="0" cellpadding="0" width="100%" align="center">
								<tr>
									<td valign="top" style="padding: 0 10px; font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #000; line-height: 25px" width="600" colspan="2">	
										<?php echo $content;?><br>
                  						<br/>
									</td>
								</tr>
								<tr>
									<td colspan="2">&nbsp;</td>
								</tr>
								<tr>
									<td colspan="2">&nbsp;</td>
								</tr>
								<tr>
									<td style="padding: 0 10px; font-family: Tahoma, Geneva, sans-serif; font-size: 11px; color: #808080; line-height: 15px;" colspan="2">
										The contents of this email and any
										files transmitted with it are confidential and intended only
										for the individuals or entities to which they are addressed.
										It may not be disclosed to, or used by, anyone other than the
										addressee, nor may it be copied in any way. If you have
										received this email in error, please notify the sender by
										return email and then delete the email and any files
										transmitted with it from your system. Please note that while
										reasonable effort has been made to ensure this message is free
										of viruses, opening and using this message is at the risk of
										the recipient and Re-member does not accept responsibility for
										any loss arising from unauthorised access to, or interference
										with, any internet communications by any third party, or from
										the transmission of any viruses.<br /> <br />
									</td>
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
 <?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
