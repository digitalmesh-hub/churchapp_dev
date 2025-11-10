<?php
namespace common\components;

use Yii;
use yii\base\Component;
use common\models\extendedmodels\ExtendedInstitutionPaymentGateways;

class ManageTransaction extends Component
{
    /* *
	* payment status enquiry currently only enabled in Cochin Yacht Club
	*/

    public function transactionStatusCheck(array $data)
    {
        $response = ExtendedInstitutionPaymentGateways::getPaymentGatewayData($data['institutionId'],$data['type']);
        $result = [
            'error' =>true
        ];
        if(!empty($response)) {
            $credentials = json_decode($response['credentials'], true);
            $data['credential'] = $credentials[ $data['type'] ? 'live' : 'test'];
            $result = self::httpCall($response['paymentEnquiryUrl'],$data);
        }
        return $result;
    }
    protected static function httpCall($url='', array $bodyPayload=[], $method = 'post') 
    {
        try {
            $curlObj = curl_init();

            if (!empty($bodyPayload)) {
                $payload = http_build_query($bodyPayload);
            }
            
            $curlOptions = array(
                CURLOPT_URL => $url,
                CURLOPT_POST => $method === 'post' ? true : false,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_POSTFIELDS => !empty($payload) ? $payload : null,
            );
            curl_setopt_array($curlObj, $curlOptions);
                // logging request
            yii::info(
                'Send Request :'.
                var_export($curlOptions,true)
            );

            $curlResponse = curl_exec($curlObj);
            // logging response
            yii::info(
                'Response :'.
                var_export($curlResponse,true)
            );
            
            $errno = curl_errno($curlObj);
            if ($errno !== 0) {
                // logging error
                yii::error(
                    'Curl Error :'.var_export(curl_error($curlObj),true)
                );
                return false;
            }
            $curlResponse = json_decode($curlResponse,true);
            return $curlResponse;
        } catch (\Exception $e) {
            yii::error($e->getMessage());
			return false;
        }
        
    }

}