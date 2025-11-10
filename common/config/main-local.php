<?php

return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=' . env('DB_HOST') . ';port=' . env('DB_PORT', '3306') . ';dbname=' . env('DB_DATABASE'),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD'),
            'charset' => 'utf8',
            
        ],
        'mailer' => [
            'class' => \yii\symfonymailer\Mailer::class,
            'viewPath' => '@common/mail',
            'transport' => [
                'scheme' => env('MAIL_DRIVER'), // Use 'smtps' only if the server requires implicit TLS
                'host' => env('MAIL_HOST'), // Replace with your SMTP host
                'username' => env('MAIL_USERNAME'),// Replace with your username
                'password' => env('MAIL_PASSWORD'), // Replace with your password
                'port' => (int)env('MAIL_PORT'), // Use 587 for STARTTLS; 465 for implicit TLS
                'encryption' => env('MAIL_ENCRYPTION'), // STARTTLS encryption (use 'ssl' for implicit TLS on port 465)
            ],
            'useFileTransport' => false, // Set to false to send real emails
        ],
    ],
];
