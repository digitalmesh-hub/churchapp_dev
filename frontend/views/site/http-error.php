<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use yii\helpers\Html;
$this->title = 'Error : Re-member';

$code = $exception->statusCode;
if ($exception instanceof \yii\base\UserException) {
    $message = $exception->getMessage();
} else {
    $message = 'An error occurred while processing the request. Please contact us if you think this is a server error. Thank you';
}
?>
<!DOCTYPE html>
<html>
    <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="/img/favicon.ico" type="image/x-icon" />
        <title>Re-member</title>
        <style type="text/css">
            html, body{
                padding: 0px;
                margin: 0px;
                text-align: center;
                font-family: Open Sans, Arial, sans-serif;
                background-color: #0B3369;
                color: #ffffff;
                -webkit-font-smoothing: antialiased;
            }
            .logo-wrapper{
                padding: 30px 0px;
                background-color: #ffffff;
            }
            .logo-wrapper img{
                max-width: 100%;
                height: auto;
            }
            .message{
                padding: 60px 20px;
                background-color: #ffffff;
                color: #333333;
                font-size: 18px;
                line-height: 1.5em;
            }

            .message h1{
                font-size: 36px;
                line-height: 1.16667em;
                font-family: Roboto Slab, Georgia serif;
                font-weight: bold;
                color: #254151;
                margin: 40px 0px;
                letter-spacing: -1px;
            }

            .message h1:first-child{
                margin-top: 0px;
            }

            .message p{
                margin: 1em 0px;
            }

            .message p:last-child{
                margin-bottom: 0px;
            }

            .button{
                display: inline-block;
                background: #F38731;
                background: -moz-linear-gradient(top,#F38731 0%,#e5592a 100%);
                background: -webkit-gradient(linear,left top,left bottom,color-stop(0%,#F38731),color-stop(100%,#e5592a));
                background: -webkit-linear-gradient(top,#F38731 0%,#e5592a 100%);
                background: -o-linear-gradient(top,#F38731 0%,#e5592a 100%);
                background: -ms-linear-gradient(top,#F38731 0%,#e5592a 100%);
                background: linear-gradient(to bottom,#F38731 0%,#e5592a 100%);
                color: #ffffff;
                border: 2px solid #e5592a;
                border-radius: 5px;
                padding: 15px 20px;
                font-weight: 800;
                cursor: pointer;
                text-transform: uppercase;
            }

            .button:hover,
            .button:active{
                background: #e5592a;
                color: #ffffff;
            }

            .footer{
                padding: 50px 0px;
            }
            .footer a{
                color: white;
                text-decoration: none;
            }

            .footer a:hover,
            .footer a:active{
                color: #E21E6E;
                text-decoration: none;
            }
            .footer .copyright{
                font-size: 13px;
                line-height: 19px;
                color: #92a0a8;
            }

            .footer .copyright a{
                color: #92a0a8;
            }

            .footer .copyright a:hover,
            .footer .copyright a:active{
                text-decoration: underline;
            }
            .footer .copyright p{
                margin: 0px;
            }
            .logo-wrapper,
            .message,
            .footer{
                padding-left: 20px;
                padding-right: 20px;
            }
        </style>
    </head>
    <body>
        <div class="logo-wrapper">
            <img alt="Re-member" width="150" height="55" src="/img/warning.png" />
        </div>
        <div class="message">    
            <h1><?= HTML::encode($code)?></h1> 
            <h1><?= HTML::encode($message)?></h1>        
        </div>
        <div class="footer">
            <div class="copyright">
                <p>Copyright &copy;  <?= date('Y') ?> Re-member, All rights reserved</p>
            </div>
        </div>
    </body>
</html>
   