<?php
// swift mailer include
require_once 'vendor/autoload.php';
error_reporting(E_ALL);

// API Endpoint
$url = "https://cdn-api.co-vin.in/api/v2/appointment/sessions/calendarByDistrict?";
// current date
$date = date('d-m-Y');
// district id
$districtID = '307';
// params

$params = array(
    'district_id' => $districtID,
    'date' => $date
);
// Adding Parameters
$url =  $url.http_build_query($params);

// CURL
$retry = 3;
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_ENCODING, '');
curl_setopt($ch, CURLOPT_CUSTOMREQUEST,'GET');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
$curl_response = curl_exec($ch);
if ($curl_response === false) {
    $info = curl_getinfo($ch);
}
while($curl_response === false && $retry>0){
    $curl_response = curl_exec($ch);
    $retry--;
}
// Close CURL
curl_close($ch);
print_r($curl_response);
// Decode Result
$centersList = json_decode($curl_response, true);
echo "vaccination Center List";
print_r($centersList);
$availList = array();

// Loop through each Centers
if(!empty($centersList['centers'])) {
    foreach ($centersList['centers'] as $eachCenter) {
        $centerID = $eachCenter['center_id'];
        // Hospital Name
        $name = $eachCenter['name'];
        foreach ($eachCenter['sessions'] as $eachSessions) {
            $date = $eachSessions['date'];
            $ageGroup  = $eachSessions['min_age_limit'];
            $capacity = $eachSessions['available_capacity'];
            // Check if slots available
           // if($capacity>0) {
                $availList[$name][$date][$ageGroup] = $eachSessions['available_capacity'];
           // }
        }
    }
}
// To check if slots available
if(!empty($availList)) {
    // Send Email to group
    // Create the Transport
    $transport = (new Swift_SmtpTransport())
        ->setHost('smtp-mail.outlook.com')
        ->setUsername('anandhugopi@hotmail.com')
        ->setPassword('@Nandhu8615!..')
        ->setPort(587)->setEncryption('tls');

    // Create the Mailer using your created Transport
    $mailer = new Swift_Mailer($transport);
    $message = (new Swift_Message('Vaccine Alert'));
    $html = '<html>
                <head>
                    <title>Available Vaccine Centers</title>
                    <meta content="text/html; charset=iso-8859-1" http-equiv="Content-Type" />
                </head>
                <body marginheight="0" topmargin="0" marginwidth="0" leftmargin="0" style="background-color: #F7F7F7;">
                <table cellspacing="" border="0" style="background-color: #F7F7F7;font-family:Tahoma, Geneva, sans-serif; font-size:13px; margin-top:20px;" cellpadding="0" width="100%">
                <tr>
                    <td valign="top" style="margin:0 auto"><table cellspacing="10" border="0" align="center" style="background: #fff; border: 1px solid #ccc; margin:0 auto;box-shadow: 0px 0px 30px -7px rgb(0 0 0 / 29%);" cellpadding="0" width="600">
                        <tr>
                        <td style="text-align:center;" colspan="2"><img style="width:300px; height:100px; margin-top:10px;" 
                                    src="'.$message->embed(Swift_Image::fromPath('https://www.digitalmesh.com/images/digitalmesh-logo.png')).'"/></td>
                </tr>';
    foreach($availList as $name=>$eachItem) {
        foreach($eachItem as $date => $avail) {
            $avail18 = !empty($avail[18]) ? $avail[18] : 0;
            $avail45 = !empty($avail[45]) ? $avail[45] : 0;
            $html.= '<tr>
            <td colspan="2"><table cellspacing="1" border="0" cellpadding="0" width="100%" align="center">
                <tr>
                  <td  valign="top" style="font-family:Tahoma, Geneva, sans-serif; font-size: 13px;  color:#000; line-height:25px" width="600" colspan="2">
                    <p style="text-align:center; font-size:12px; width: 100%;float: left;color: #fff;background: #29166f;padding: 1px 0px;box-sizing: border-box;text-transform: uppercase;">
                        <strong>'.$name.'</strong>
                    </p>
                  </td>
                </tr>
                <tr>
                  <td  style="padding: 5px;background: #f7f7f7;font-size: 14px;"><strong>Date</strong></td>
                  <td  style="padding: 5px;background: #f7f7f7;font-size: 14px;">'.$date.'</td>
                </tr>
                <tr>
                  <td   style="padding: 5px;background: #f7f7f7;font-size: 14px;"><strong>No of Vaccines (18-44)</strong></td>
                  <td  style="padding: 5px;background: #f7f7f7;font-size: 14px;">'.$avail18.'</td>
                </tr>
                <tr>
                  <td   style="padding: 5px;background: #f7f7f7;font-size: 14px;"><strong>No of Vaccines(45+)</strong></td>
                  <td  style="padding: 5px;background: #f7f7f7;font-size: 14px;">'.$avail45.'</td>
                </tr>
              </table></td>
          </tr>';
        }
    }
    $html.= '</table></td></tr></table></body></html>';
    // Create a message

    $message->setFrom(['anandhugopi@hotmail.com' => 'Anandhu Gopi'])
        ->setTo(['anandhugopi@hotmail.com'])
        ->setBody($html, 'text/html');
    try {
        // Send the message
        if (!$mailer->send($message, $failures)) {
            echo "Failures:";
            echo "<pre>";
            print_r($failures);
        } else {
            echo 'email sent successfully';
        }
    } catch (Exception $e) {
        print_r($e);
    }

} else {
    echo "No Vaccination centers Available";
}

?>

