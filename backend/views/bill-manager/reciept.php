<?php
function getIndianCurrency($number)
{
	$decimal = round($number - ($no = floor($number)), 2) * 100;
	$hundred = null;
	$digits_length = strlen($no);
	$i = 0;
	$str = array();
	$words = array(0 => '', 1 => 'one', 2 => 'two',
			3 => 'three', 4 => 'four', 5 => 'five', 6 => 'six',
			7 => 'seven', 8 => 'eight', 9 => 'nine',
			10 => 'ten', 11 => 'eleven', 12 => 'twelve',
			13 => 'thirteen', 14 => 'fourteen', 15 => 'fifteen',
			16 => 'sixteen', 17 => 'seventeen', 18 => 'eighteen',
			19 => 'nineteen', 20 => 'twenty', 30 => 'thirty',
			40 => 'forty', 50 => 'fifty', 60 => 'sixty',
			70 => 'seventy', 80 => 'eighty', 90 => 'ninety');
	$digits = array('', 'hundred','thousand','lakh', 'crore');
	while( $i < $digits_length ) {
		$divider = ($i == 2) ? 10 : 100;
		$number = floor($no % $divider);
		$no = floor($no / $divider);
		$i += $divider == 10 ? 1 : 2;
		if ($number) {
			$plural = (($counter = count($str)) && $number > 9) ? 's' : null;
			$hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
			$str [] = ($number < 21) ? $words[$number].' '. $digits[$counter]. $plural.' '.$hundred:$words[floor($number / 10) * 10].' '.$words[$number % 10]. ' '.$digits[$counter].$plural.' '.$hundred;
		} else $str[] = null;
	}
	$Rupees = implode('', array_reverse($str));
	$paise = '';
	if($decimal){
		$paise .= " and ";
		if($decimal >=20){
			$paise .= $words[((int)($decimal / 10))*10 ] . " " . $words[$decimal % 10] . ' paise';
		} else {
			$paise .= $words[$decimal] . ' paise';
		}
	}
	return ($Rupees ? $Rupees . 'rupees ' : '') . ($paise ? $paise : '') . (($Rupees || $paise) ? ' only' : '') ;
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Receipt</title>
</head>

<body style="padding:0px; margin:0px; background:#FFF; font-family:Arial, Helvetica, sans-serif; font-size:13px;">

<!-- wrapper -->
<div style="width:100%; display:block; float:left; padding:20px; box-sizing:border-box; border:2px solid #333; border-radius:12px; -webkit-border-radius:12px; -moz-border-radius:12px;">

<!-- Header -->
<table cellpadding="3" cellspacing="0" style="width:100%;">
	<tr>
       <td style="font-size:22px; font-weight:bold; text-align:center;"><?php echo $institution->name; ?></td>
    </tr>
    <tr>
    	<td style="font-size:12px; font-weight:bold; text-align:center;">
    	<?php 
    	echo !empty($institution->address1) ? $institution->address1 : '' ;
      	echo !empty($institution->address2) ? ', ' . $institution->address2 : '';
        echo !empty($institution->address3) ? ', ' . $institution->address3 : ''; 
        echo !empty($institution->district) ? ', ' . $institution->district : '';
        echo !empty($institution->state) ? ', ' . $institution->state : ''; 
        echo !empty($institution->pin) ? ' - ' . $institution->pin : '';
        ?>
    	</td>
    </tr>
    <tr>
    	<td style="font-size:12px; font-weight:bold; text-align:center;">Phone: <?php echo $institution->phone1_countrycode; ?>&nbsp;<?php echo $institution->phone1_areacode; ?>&nbsp;<?php echo $institution->phone1; ?></td>
    </tr>
</table>
<!-- /.Header -->
<br></br>  
<!-- form 1 -->
<table cellpadding="0" cellspacing="0" style="width:100%;">
	<tr>
       <td width="8%" style="padding:3px; font-size:16px;"><strong>No.</strong></td>
       <td width="60%"><?php echo $receiptNo ?></td>
       <td width="12%" style="padding:3px; font-size:16px;"><strong>Date</strong></td>
       <td width="20%" style="padding:3px; border-bottom:2px dotted #333;"><?php echo date(yii::$app->params['dateFormat']['viewDateFormat'], strtotimeNew($bill->transactiondate));?></td>
    </tr>
<br></br>     
</table>
<!-- /.form 1 -->

<!-- form 1-1 -->
<table cellpadding="0" cellspacing="0" style="width:100%;">
	<tr>
       <td width="20%" style="padding:10px 3px 0px 3px; vertical-align:bottom; font-size:16px; font-style:italic;"><strong>Received from</strong></td>
       <td width="80%" colspan="3" style="padding:10px 3px 1px 3px; border-bottom:2px dotted #333;  vertical-align:bottom;">
       <?php echo ucwords($member->firstName . ' ' . $member->middleName . ' ' . $member->lastName . ' ( ' . $member->memberno . ' )');?></td>       
    </tr>
    <tr>
       <td colspan="4" style="padding:15px 3px 1px 3px; vertical-align:bottom; border-bottom:2px dotted #333;">&nbsp;</td>          
    </tr>
    <tr>
       <td width="20%" style="padding:15px 3px 0px 3px; vertical-align:bottom; font-size:16px; font-style:italic;"><strong>Rupees</strong></td>
       <td width="80%" colspan="3" style="padding:20px 3px 1px; border-bottom:2px dotted #333;  vertical-align:bottom;">
       	<?php 
       	echo ucfirst(getIndianCurrency($bill->credit) ?? '');
       	?> 
       </td>       
    </tr> 
    <tr>
       <td colspan="4" style="padding:15px 3px 1px 3px; vertical-align:bottom; border-bottom:2px dotted #333;">&nbsp;</td>          
    </tr>
    <?php if(isset($cheque->paymentType)) { ?> 
    <tr>
       <td width="20%" style="padding:15px 3px 0px 3px; vertical-align:bottom; font-size:16px; font-style:italic;"><strong>By 
       <?php if($cheque->paymentType != 'cash' ){?><del><?php }?>
       Cash
       <?php if($cheque->paymentType != 'cash' ){?></del><?php }?>
       <br/>/
       
       <?php if($cheque->paymentType != 'card' ){?><del><?php }?>
       Card
       <?php if($cheque->paymentType != 'card'){?></del><?php }?>
       <br/>/
       
       <?php if($cheque->paymentType != 'cheque'){?><del><?php }?>
       Cheque No.
       <?php if($cheque->paymentType !='cheque'){?></del><?php }?>
       <br/>/
       
       <?php if($cheque->paymentType !='neft'){?><del><?php }?>
       NEFT No.
       <?php if($cheque->paymentType !='neft'){?></del><?php }?>
       <br/>/
       
       <?php if($cheque->paymentType != 'upi' ){?><del><?php }?>
       UPI
       <?php if($cheque->paymentType != 'upi'){?></del><?php }?>
       
        </strong></td><td width="80%" colspan="3" style="padding:20px 3px 1px; border-bottom:2px dotted #333;  vertical-align:bottom;"><?php if($cheque->paymentType == 'cheque'){ echo $cheque->ChequeNo; }?><?php if($cheque->paymentType == 'neft'){ echo $cheque->NeftNo; }?>
        <?php if($cheque->paymentType == 'upi'){ echo $cheque->ChequeNo; }?>
      </td>  
    </tr>
    <tr>
       <td width="20%" style="padding:15px 3px 0px 3px; vertical-align:bottom; font-size:16px; font-style:italic;"><strong>Drawn on</strong></td>
       <td width="80%" colspan="3" style="padding:20px 3px 1px; border-bottom:2px dotted #333;  vertical-align:bottom;"><?php if($cheque){ echo $cheque->Bank ?? '' . ', ' . $cheque->Branch?? ''; }?></td>       
    </tr> 
    <tr>
       <td width="20%" style="padding:15px 3px 0px 3px; vertical-align:bottom; font-size:16px; font-style:italic;"><strong>Date</strong></td>
       <td width="40%" style="padding:15px 3px 1px; border-bottom:2px dotted #333;  vertical-align:bottom;"><?php echo !empty($cheque->Date) ? date(yii::$app->params['dateFormat']['viewDateFormat'], strtotimeNew($cheque->Date)) : '';?></td>       
       <td width="40%" style="padding:15px 3px; vertical-align:bottom;">&nbsp;</td>    
    </tr> 
     <?php } ?>
    <tr>
       <td width="20%" style="padding:15px 3px 0px 3px; vertical-align:bottom; font-size:16px; font-style:italic;"><strong>For</strong></td>
       <td width="40%" style="padding:15px 3px 1px; border-bottom:2px dotted #333;  vertical-align:bottom;"><?php echo $bill->description?></td> 
       <td width="40%" style="padding:15px 3px 1px; vertical-align:bottom;">&nbsp;</td>    
    </tr> 
        
</table>
<!-- /.form 1-1 -->


<!-- footer -->
<table cellpadding="0" cellspacing="0" style="width:100%;">
   <tr>
      <td width="20%" style="padding:15px 3px 0px 3px; vertical-align:bottom; font-size:16px; font-style:italic;"><strong>Rs.</strong></td>
      <td width="40%" style="padding:15px 3px 1px; border-bottom:2px dotted #333;  vertical-align:bottom;"><?php echo yii::$app->MoneyFormat->decimalWithComma($bill->credit);?>/-</td> 
      <td width="40%" style="padding:15px 3px 0px 3px; vertical-align:bottom; text-align:right; font-size:16px;"><strong>Accountant</strong></td> 
   </tr>
</table>
<!-- /.footer -->




</div><!-- /.wrapper -->

</body>
</html>
</body>