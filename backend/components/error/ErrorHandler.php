<?php

namespace backend\components\error;
use Yii;

/*
*Author : amal@digitalmesh.com
*date : 08-06-2018
*This is a class called from main file when an error/exception trigger. 
*/
 
class ErrorHandler extends \yii\web\ErrorHandler
{
    
    public $errorView = '@backend/views/site/error.php';
    public $exceptionView = '@backend/views/site/error.php';
    public function register()
    {
        parent::register();
    }
    public function handleError($code, $message, $file, $line)
    {   
        // Only call parent if this is not a warning about array offset on null
        if (!($code === E_WARNING && strpos($message, 'Trying to access array offset on null') !== false)) {
            parent::handleError($code,$message,$file,$line);
        }
    }

    public function handleException($exception)
    {   
        parent::handleException($exception);
    }

    public function handleFatalError()
    {   
        parent::handleFatalError();
    }
    public static function isFatalError($error)
    {
        return isset($error['type']) && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_CORE_WARNING, E_COMPILE_ERROR, E_COMPILE_WARNING]);
    }
    protected function renderException($exception)
    {   
        
        if (Yii::$app->params['enableErrorMailService'] && $exception->statusCode != 404) { //send mail only when this bit is true.
            $this->emailError($exception);
        }
        parent::renderException($exception);
    }
    
    /*
    *This action will send mail to given email ids
    *The exception object will be converted to string.
    */
    public function emailError($exception)
    {   
    
        try {
            if (!YII_DEBUG && YII_ENV == 'prod') {
            $from = Yii::$app->params['errorFromMail'];
            $to  = Yii::$app->params['errorToMail'];
            $cc =  Yii::$app->params['errorCcEmail'];   
            $logo = null;
            $subject = '['.env('ENVIRONMENT') .'] Re-member Error Report';
            $message = 'Time : '.date('Y-m-d H:i:s');
            $message .= '</br></br>';
            $message .= parent::convertExceptionToVerboseString($exception);
            
            $mailContent['content'] = $message;
            $mailContent['template'] = "error-mail";
            //Sending mail.
            $emailStatus = yii::$app->EmailHandler->sendEmail(
                    $from,
                    $to,
                    $cc,
                    $subject,
                    $mailContent,
                    null,
                    $logo
                    );
            } 
        } catch(\Exception $e) {
        }
    }
}
