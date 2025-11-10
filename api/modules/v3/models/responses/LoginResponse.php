<?php

namespace api\modules\v3\models\responses;

class LoginResponse implements \JsonSerializable
{
    public $token;

    public function __construct($token = '', $screenName = '')
    {
        $this->token = $token;
    }
    /**
     * This method is invoked while encoding into json
    */
    function jsonSerialize()
    {
        $jsonObj	=	new \stdClass();
        if ($this->token) {
            $jsonObj->token = $this->token;
        }
        return $jsonObj;
    }
}