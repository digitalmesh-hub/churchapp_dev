<?php 
include 'lib/swift_required.php';

$to = 'info@digitalmesh.com';
$name = '';
$email = '';
$phone = '';
$subject = 'Remember Support - Enquiry';
$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST'){
	// encode html entities
	$name = htmlEntities($_POST['name']);
	$email = htmlEntities($_POST['email']);
	$phone = htmlEntities($_POST['phone']);
	$message = htmlEntities($_POST['message']);
	
	send($to, $email,$name, $phone, $subject,$message);
	
}
function createmailtemplate($name,$email,$phone,$message,$image){ 
	$template = '<html>
<head>
	
	<title>Re-member</title>
	
	<meta content="text/html; charset=iso-8859-1" http-equiv="Content-Type" />
	
</head>
<body marginheight="0" topmargin="0" marginwidth="0" leftmargin="0" style="background-color: #F7F7F7;">

<table cellspacing="0" border="0" style="background-color: #F7F7F7;font-family:Tahoma, Geneva, sans-serif; font-size:13px; margin-top:20px;" cellpadding="0" width="100%">	
	<tr>
<td valign="top" style="margin:0 auto">
			<table cellspacing="0" border="0" align="center" style="background: #fff; border: 1px solid #ccc; margin:0 auto" cellpadding="0" width="600">
				<tr>
					<td style="text-align:center;" colspan="2"><img style="width:60px; height:60px; margin-top:10px;" 
					src="'.$image.'"/></td>
                   
				</tr>
				<tr>
					<td colspan="2">
						<table cellspacing="0" border="0" cellpadding="0" width="100%" align="center">
							<tr>
								<td  valign="top" style="padding: 0 10px; font-family:Tahoma, Geneva, sans-serif; font-size: 13px;  color:#000; line-height:25px" width="600" colspan="2">
                                
                                <p style="text-align:center; font-size:16px;"><strong>Re-member</strong></p><br>
			Name   : '.$name.'<br>
			Email  : '.$email.'<br>
			Phone  : '.$phone.'<br>
			

'.$message.'

								</td>
							</tr>
                            
                            <tr>
                               <td colspan="2" width="100%">&nbsp;</td>
                            </tr>
                            <tr >
                               <td colspan="2" width="100%">&nbsp;</td>
                            </tr>
							                 
						 
						</table>
					</td>
				</tr>
				
			</table>
		</td>
	</tr>
</table>

</body>
</html>';
	
	return $template;
}
function send($to,$email,$name,$phone,$subject,$mailContent){
	 // Create the Transport
	 $dir = dirname ( __FILE__ ).DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR;
	if(($email) && ($name)  &&($mailContent)){
		
		
		$transport = Swift_SmtpTransport::newInstance('mail.digitalmesh.com', 25)
		->setUsername('dbizadmin@digitalmesh.com')
		->setPassword('adm1n2db1z');
		// Create the Mailer using your created Transport
		$mailer = Swift_Mailer::newInstance($transport);
		// Create a message
		$message = Swift_Message::newInstance($subject);
		
		$image = $message->embed(Swift_Image::fromPath($dir.'appicon.png')) ;
		$mailContent = createmailtemplate($name, $email, $phone, $mailContent,$image);
	
		$message->setBody($mailContent,'text/html')
		->setFrom(array($email=>$name))
		->setTo(array($to));
		
		// Send the message
		$result = $mailer->send($message);
		if($result){
			header( "Location: success.html" );
		}else{
			
			header( "Location: error.html" );
		}
	}else{
		
		header( "Location: error.html" );
	}
	
}