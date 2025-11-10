<?php
/**
 * @author Remya
 * This component handle the push notification services for ios devices
 */
namespace common\components;

use yii;

class APNSClient
{
    private $cert;
    private $passphrase;
    private $ServiceURL;
    private $gateway;

    public function __construct()
    {
        $this->cert =  \Yii::$app->params['apns.path.certificate'];
        $this->passphrase = \Yii::$app->params['passphrase'];
        $this->gateway = \Yii::$app->params['apns.path'];

    }

    public function send($to, $data)
    {
        $result ='';
        // yii::info('Initializing streaming','APnspushNotifications');
        $ctx = stream_context_create();
        stream_context_set_option($ctx, 'ssl', 'local_cert', $this->cert);
        stream_context_set_option($ctx, 'ssl', 'passphrase', $this->passphrase);
        // Open a connection to the APNS server
        $fp = stream_socket_client($this->gateway, $err, $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
        $payload = json_encode($data);
        // Build the binary notification
        $msg = chr(0) . pack('n', 32) . pack('H*', $to) . pack('n', strlen($payload)) . $payload;
        // Send it to the server
        $result = fwrite($fp, $msg, strlen($msg));
        //yii::error("Apns -Response : ".var_export($result,true));
        fclose($fp);
        return $result;
    }

    protected function fwrite_stream($fp, $string)
    {
        for ($written = 0; $written < strlen($string); $written += $fwrite) {
            $fwrite = fwrite($fp, substr($string, $written),strlen($string));
            if ($fwrite === false) {
                return $written;
            }
        }
        return $written;
    }
}
