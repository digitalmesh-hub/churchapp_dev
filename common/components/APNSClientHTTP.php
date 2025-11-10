<?php
/**
 * @author Remya
 * This component handle the push notification services for ios devices
 */
namespace common\components;

use yii;
use Pushok\AuthProvider;
use Pushok\Client;
use Pushok\Notification;
use Pushok\Payload;
use Pushok\Payload\Alert;

class APNSClientHTTP
{
    private $options = [];

    public function __construct()
    {
        $this->options =  [
            'key_id' => \Yii::$app->params['apns.key_id'], // The Key ID obtained from Apple developer account
            'team_id' => \Yii::$app->params['apns.team_id'], // The Team ID obtained from Apple developer account
            'app_bundle_id' => \Yii::$app->params['apns.app_bundle_id'], // The bundle ID for app obtained from Apple developer account
            'private_key_path' => \Yii::$app->params['apns.path.private_key_path'], // Path to private key
            'private_key_secret' => \Yii::$app->params['apns.private_key_secret'] // Private key secret
        ];

    }
    public function send(array $deviceTokens, $data)
    {
        try {
            // Yii::error($data, 'iOS_request data');
            $title=(isset($data['contentTitle'])) ? $data['contentTitle'] : '';
            $authProvider = AuthProvider\Token::create($this->options);
            $alert = Alert::create()->setTitle($title);
            $alert = $alert->setBody($data['aps']['alert']['body']);
            $payload = Payload::create()->setAlert($alert);
    
            //set notification sound
            $payload->setSound($data['aps']['sound']);
            if(!empty($data['aps']['contentAvailable'])){
                $payload->setContentAvailability(true);
            }
    
            //add custom value to your notification, needs to be customized
            if(!empty($data['type'])) {
                $payload->setCustomValue('type', $data['type']);
            }
            if(!empty($data['item-id'])) {
                $payload->setCustomValue('item-id', $data['item-id']);
            }
            if(!empty($data['institution'])) {
                $payload->setCustomValue('institution', $data['institution']);
            }
            if(!empty($data['institution-id'])) {
                $payload->setCustomValue('institution-id', $data['institution-id']);
            }
            if(!empty($data['member-id'])) {
                $payload->setCustomValue('member-id', $data['member-id']);
            }
            // $payload->setCustomValue('key', 'value');
    
    
            $notifications = [];
            foreach ($deviceTokens as $deviceToken) {
                $notifications[] = new Notification($payload,$deviceToken);
            }
            // Yii::error($payload, 'iOS_request payload');
            // If you have issues with ssl-verification, you can temporarily disable it. Please see attached note.
            // Disable ssl verification
            $client = new Client($authProvider, $production = \Yii::$app->params['apns.is_production'], [CURLOPT_SSL_VERIFYPEER=>false]);
            $client->addNotifications($notifications);
    
            $responses = $client->push(); // returns an array of ApnsResponseInterface (one Response per Notification)
            // print_r($responses);
            $status = true;
            foreach ($responses as $response) {
                /* $response->getApnsId();
                $response->getStatusCode();
                $response->getReasonPhrase();
                $response->getErrorReason();
                $response->getErrorDescription(); */
                if($response->getStatusCode() != 200)
                {
                    $status = false;
                    break;
                }

                //Yii::info($response, 'iOS_request response');
            }
            return $status;
								
		} catch (\Exception $e) {
            Yii::error($e, 'iOS_request error');
			return false;
		}
    }

    
}
