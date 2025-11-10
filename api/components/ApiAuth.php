<?php

namespace api\components;

use yii\filters\auth\AuthMethod;
use yii\web\UnauthorizedHttpException;
use Exception;
use yii;

class ApiAuth extends AuthMethod
{
    public $statusCode;
    public $data;
    public $message = "";

    public function authenticate($user, $request, $response)
    {   

        $apirequestHandler = new ApiRequestHandler();
        $apirequestHandler->setRequestData();
        if ($apirequestHandler->header->status == 200) {
            $authToken = $request->getHeaders()->get('AuthToken');
            if ($authToken !== null) {
                $identity = $user->loginByAccessToken($authToken, get_class($this));
                if ($identity === null) {
                    $this->statusCode = 498;
                    $this->message = "Session expired / invalid token";
                    $this->data = new \stdClass();
                    yii::info("AuthToken is missing/Invalid", 'api_request');
                    $this->handleFailure($response);
                }
                try{
                    $identity->userToken->touch('lastactivedatetime');
                } catch (Exception $e) {
                }
                return $identity;
            }
                $this->statusCode = 498;
                $this->message = "Session expired / invalid token";
                $this->data = new \stdClass();
                return null;
        } else {
            $this->message = "Header missing";
            $this->statusCode = 500;
            throw new \yii\web\HttpException($this->statusCode);
        }
    }

    public function challenge($response)
    {

    }
    public function handleFailure($response)
    {
        throw new UnauthorizedHttpException;
    }
}