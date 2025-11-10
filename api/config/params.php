<?php
return [
    'adminEmail' => env('CLIENT_MAIL'),
    'runningAppVersions' => [
        'ios' => [
            'latestAppVersion' => env('IOS_LATEST_VERSION'),
            'minimumAppVersion' => env('IOS_MINIMUM_VERSION'),
            'description' => 'Minor bug fixes',
            'forceUpdate' => []
        ],
        'android' => [
            'latestAppVersion' => env('ANDROID_LATEST_VERSION'),
            'minimumAppVersion' => env('ANDROID_MINIMUM_VERSION'),
            'description' => 'Login using Fingerprint & Minor bug fixes',
            'forceUpdate' => []
        ]
    ],
    'defaultCountry' => [ 
    	'INDIA' => 94,
		'COUNTRY_CODE' => +91,
        'INDIA_COUNTRY_CODE' => 91
    ],
    'mobileAccessPrivileges' =>[
        'ca4ac940-ec4a-11e6-b48e-000c2990e707', 
        'd4b64d8a-ec48-11e6-b48e-000c2990e707',
        '81423355-ec4a-11e6-b48e-000c2990e707', 
        '0f74458a-ec49-11e6-b48e-000c2990e707', 
        '74af2974-ec46-11e6-b48e-000c2990e707', 
        'fcb852d5-0005-11e7-b48e-000c2990e707',
        '0c26fee6-3df8-4cd6-83d9-45a556a75b64'
    ],
];