<!DOCTYPE html>
<html>
    <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="/img/favicon.ico" type="image/x-icon" />
        <title>Re-member error trail mail</title>
        <style type="text/css">
            html, body{
                padding: 0px;
                margin: 0px;
                text-align: center;
                font-family: Open Sans, Arial, sans-serif;
                background-color: #ffffff;
                color: #ffffff;
                -webkit-font-smoothing: antialiased;
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
        </style>
    </head>
   <body>
      <div class="message"> 
      <h1>Attention the following error has occured.</h1>    
      <p><?= $content ?></p>
      </div>
   </body>
</html>
   
