<?php

namespace api\modules\v3\models\responses;

use yii;

class ApiResponse implements \JsonSerializable
{
    public $statusCode;
    public $data;
    public $message = "";

    public function __construct($status = null, $data = null, $message = "")
    {
        $this->statusCode = $status;
        $this->message = $message;
        $this->data = $data;
    }
    public static function __set_state($data)
    {
        $obj = new ApiResponse();
        $obj->data = $data["data"];
        $obj->message = $data["message"];
        $obj->statusCode = $data["statusCode"];
        return $obj;
    }

    /**
     * This is the abstract method which returns json encoded data.
     */
    public function jsonSerialize(): mixed
    {
        if (is_null($this->data)) {
            $this->data = new \stdClass();
        }

        return [
            'statusCode' => $this->statusCode,
            'message' => $this->message,
            'data' => $this->data,
        ];
    }
}
