<?php

namespace api\components;

use yii\base\Component;
use yii;

/**
 *ApiRequestHandler is used to handle the
 * header and form data information  of the api request
 */
class ApiRequestHandler extends Component
{
    /**
    * @property object $header to store the Header information
    */
    public $header;

    /**
     * 
    * @var array $headerKey stores the header keys
    */
    private $headerKey = array(
        "appVersion",
        "deviceType",
        "osVersion",
        "timeZone",
       // "deviceKey",
         //"demo"
        );
    /**
    * This method checks post and  calls corresponding functions
    * @return string $config
    */
    public function setRequestData()
    {
        $this->setHeaderInformation();
    }

    /**
    * sets the  header information to the property. 
    * if the $_SERVER array contain the key correspond to $headerKey array values.
    * then set that $_SERVER key and value to header property
    */
    private function setHeaderInformation()
    {
        $this->header = new \stdClass();
        $request 	= \Yii::$app->request;
        $requestHeaders = $request->getHeaders();
        foreach ($this->headerKey as $key => $value) {
            if ($requestHeaders->offsetExists($value)) {
                $this->header->$value = $requestHeaders->get($value);
                $this->header->status =200;
            } else {
                $this->header->status =500;
                yii::error('Header '.$value." is missing", 'api_request');
                break;
            }
        }
    }
}