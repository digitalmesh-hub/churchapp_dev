<?php

namespace api\components;

use yii;
use yii\base\Component;

/**
 *ApiResponseFormatter class is used for common response messages and codes.
 *
 */
class ApiResponseCode extends Component
{
    const ERR_OK = 200;
    const ERR_AUTH = 401;
    const ERR_METHOD_NOT_FOUND = 404;
    const ERR_INTERNAL_SERVER_ERROR = 500;
    const ERR_VALIDATION_FAILED = 422;
    const ERR_METHOD_NOT_ALLOWED = 405;
    const ERR_HEADER_FAILED = 417;
    
    /**
     * This function will return required messages for code.
     * @param  [type] $code [description]
     * @return [type]       [description]
     */
    public static function responseMessagesFromCode($code)
    {
        $messages = [
        self::ERR_OK => 'ok',
        self::ERR_AUTH => 'Authentication failed.',
        self::ERR_METHOD_NOT_FOUND => 'Requested resource not found',
        self::ERR_INTERNAL_SERVER_ERROR => 'Service is temporarily unavailable.If this error continues to occur, Please contact us.',
        self::ERR_VALIDATION_FAILED => 'Data validation failed (in response to a POST request)',
        self::ERR_METHOD_NOT_ALLOWED => 'Method not allowed. Please check the Allow header for the allowed HTTP methods',
        self::ERR_HEADER_FAILED => 'The server cannot meet the requirements of the Expect request-header field',
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        118 => 'Connection timed out',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        208 => 'Already Reported',
        210 => 'Content Different',
        226 => 'IM Used',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Reserved',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',
        310 => 'Too many Redirect',
        400 => 'Bad Request',
        402 => 'Payment Required',
        403 => 'Forbidden',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Time-out',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested range unsatisfiable',
        418 => 'I\'m a teapot',
        421 => 'Misdirected Request',
        423 => 'Locked',
        424 => 'Method failure',
        425 => 'Unordered Collection',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        449 => 'Retry With',
        450 => 'Blocked by Windows Parental Controls',
        451 => 'Unavailable For Legal Reasons',
        501 => 'Not Implemented',
        502 => 'Bad Gateway or Proxy Error',
        503 => 'Service Unavailable',
        504 => 'Gateway Time-out',
        505 => 'HTTP Version not supported',
        507 => 'Insufficient storage',
        508 => 'Loop Detected',
        509 => 'Bandwidth Limit Exceeded',
        510 => 'Not Extended',
        511 => 'Network Authentication Required',
        602 => 'You are not authorized to perform this action.'
        ];
        return (isset($messages[$code]))? $messages[$code]: $messages[self::ERR_INTERNAL_SERVER_ERROR];
    }
}
