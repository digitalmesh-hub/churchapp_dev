<?php
return [
    'memberProfileUrl' => env('MEMBER_PROFILE_URL'),
    'adminEmail' => env('ADMIN_MAIL'),
	'bccEmail' => env('BCC_BILL_MAIL'),
	'tempEmail' => env('TEMP_MAIL'),
    'supportEmail' => env('SUPPORT_MAIL'),
    'user.passwordResetTokenExpire' => 3600,
    // Enable/disable automatic removal of UserMember connections when member status is set to inactive
    'removeUserMemberOnInactive' => env('REMOVE_USERMEMBER_ON_INACTIVE', false),
    'dateFormat' => [
        'sqlDateFormat' => 'Y-m-d',
        'sqlDandTFormat' => 'Y-m-d H:i:s',
        'viewDateFormat' => 'd F Y',
        'viewDandTFormat12Hr' => 'd-m-Y h:i a',
        'viewDandTFormat' => 'd-m-Y h:i:s',
        'shortMonthFormat' => 'd-M-Y',
        'shortMandTFormat12Hr' => 'd M Y g:i A',
        'formDateFormat' => 'd-M-Y',
        'time12Hr' => 'h:i a',
        'time24Hr' =>'H:i:s',
        'dateOfBrithFormat' => 'd/m/Y',
        'viewDateFormatandT24hr'=>'d F Y H:i:s',
        'shortMandTFormat24Hr' => 'd M Y H:i:s',
        'viewDateFormatDetailView' => 'd F Y h:i:s a',
        'shortMonthDateFormat' => 'd-m-Y',
        'serviceDateFormat' => 'd F Y h:i a',
    	'updatedDateFormat' => 'Y F d H:i:s',
        'notificationDateFormat' =>  'd/m/Y h:i:s a',
        'sqlDateFromatWithDotSep' => 'd.m.Y'
    ],
    'imagePath' => env('IMAGE_URL'),
	'image'=> [
		'member'=>[
			'main' =>'Member',
			'memberImage'    => 'member',
			'memberthumbnailImage'    => 'thumbnail_member',
            'spouseImage'    => 'spouse',
		    'spousethumbnailImage'    => 'thumbnail_spouse',
            'dependantImage'  => 'dependant',
            'dependantSpouse' => 'dependant_spouse',
			'thumbnailDependantSpouse' => 'thumbnail_dependant_spouse',
            'tempMemberImage' => 'temp_member',
            'tempSpouseImage' => 'temp_spouse',
            'tempDependantImage' => 'temp_dependant',
			'tempDependantThumbnailImage' => 'thumbnail_temp_dependant',
            'tempDependantSpouseImage' => 'temp_dependant_spouse',
			'tempDependantThumbnailSpouseImage' => 'temp_dependant_spouse',
			'thumbnailTempMember' =>'thumbnail_temp_member',
			'thumbnailTempSpuse' =>'thumbnail_temp_spouse',
			'thumbnailDepentant' =>'thumbnail_dependant',	
			'thumbnail' => [
				'width' => 120,
			    'height' => 139
			]	
		],
        'products' =>[
            'propertyImage' => 'propertyImage'
        ],
        'institution' => [
            'institutionImage' => 'institutionImage',
            'thumbnailImage' => 'thumbnailImage',
            'thumbnail' => [
                'width' => 120,
                'height' => 139
            ],
        ],
		'album' => [
				'main' => 'albums',
				'albumimage' => 'original_album',
				'albumevent' => 'album_',
				'albumthumbnailImage' => 'thumbnail_album',
				'thumbnail' => [
					'width' => 120,
					'height' => 139
				]
		],
		'feedback' => [
			'main' => 'feedback',
			'feedbackImage' => 'feedbackImage',
			'thumbnailFeedback' => 'feedbackImage_thumbnail'
		],
	],
        'proxyEnabled' => false, //for proxy enabled servers, set value to true
        'proxy.host' => '192.168.10.1', //Proxy host address
        'proxy.port' => 8080,//port number
        'dev_proxy' => '192.168.10.1:8080',
        'sms' => [
            'user' => env('SMS_USER'),//username for API provider
            'hash' => env('SMS_HASH'), //hash value
            'sender' => urlencode(env('SMS_SENDER'),), //from address shown when recieving SMS
            'apiurl' => env('SMS_APIURL'), //Change only if the API provider changes
        ],
    'clubAppPrivileges' => [
        'manageFoodOrders' => 'fcb852d5-0005-11e7-b48e-000c2990e707'
    ],
    'propertyGroup' => [
        'restaurant' => 1
    ],
    'orderStatus' => [
        'placed' => 0,
        'confirmed' => 1,
        'ready' => 2,
        'handover' => 3,
        'rejected' => 4,
        'cancelled' => 5,
        'removeFromMyOrder' => 6
    ],
    'surveyURL' => 'http://survey.dev/index.php?uid=',	
    're-memberEmail' => env('REMEMBER_MAIL'),
    'clientEmail' => env('CLIENT_MAIL'),
    'openBalance' => [ 
        'description' => 'opening balance',
        'type' => 1 
    ],
    'otherTransactions' => [
        'type' => 0
    ],
    'emailAdminOnBillChange' => true, // Flag to send email on bill modification
    'isBillChangeCc' => true, // Flag to add cc on bill modification email
    'billChangeCcEmail' => env('BCC__BILL_MAIL'),
    'defaultReceiptNo' => 1000,
    'message' => 'Are you sure you want to leave?',
    'subscriptionFee' => [
        'minAmount' => 1,
        'defaultAmount' => 1000,
        'defaultDescription' => 'Subscription fee for the month of ',
        'status' =>[
            0 => 'Pending',
            1 => 'Processing',
            2 => 'Processed'
        ]
    ],
    'instituteLogoFrom' =>  env('INSTITUTION_LOGO_FORM'),
    'email' => [
        'attachmentPath' => '/runtime/files/',
        'logoPath' => '/assets/theme/images/login-logo.png',
        'emailFailed' => Yii::t ( 'app', 'Unable to send mail' ),
        'emailFailedNetwork' => Yii::t ( 'app', 'Unable to send mail! due to network issue' ) 
    ] ,
    'excel' => [
        'excelOpeningError' => Yii::t('app', 'Failed to Open Excel.!'),
        'excelImportTableError' => Yii::t('app', 'Failed to Save Data from Excel'),
        'excelMismatchError' => Yii::t('app', 'Excel not Matching. Misspelled columns'),
        'excelRequiredFieldsError' => Yii::t('app', 'Either Required fields are missing or misspelled')
    ],
    'csv' => [
        'csvOpeningError' => Yii::t('app', 'Failed to Open Csv.!'),
        'csvImportTableError' => Yii::t('app', 'Failed to Save Data from Csv'),
        'csvMismatchError' => Yii::t('app', 'Csv not Matching. Misspelled columns'),
        'csvRequiredFieldsError' => Yii::t('app', 'Either Required fields are missing or misspelled')
    ],
    'billExcelFields'=> [
        'Doc. Date' => 'Doc. Date',
        'Doc. Ref.' => 'Doc. Ref.',
        'Particulars/Narration' => 'Particulars/Narration',
        'Debit' => 'Debit',
        'Credit' => 'Credit',
        'Balance' => 'Balance',
        'Dr Cr' => 'Dr Cr'     
    ],
    'billExcelRequiredFields' => [
        'Doc. Date' => 'Doc. Date',
        'Doc. Ref.' => 'Doc. Ref.',
        'Particulars/Narration' => 'Particulars/Narration',
        'Debit' => 'Debit',
        'Credit' => 'Credit',
        'Balance' => 'Balance',
        'Dr Cr' => 'Dr Cr'      
    ],
    'cochinClubExcelRequiredFields' =>[
        'Date' => 'Date',
        'Particulars' => 'Particulars',
        'Debit' => 'Debit',
        'Credit' => 'Credit',
        'Balance' => 'Balance'
        
    ],
    'cochinClubExcelFields' =>[
        'Date' => 'Date',
        'Particulars' => 'Particulars',
        'Debit' => 'Debit',
        'Credit' => 'Credit',
        'Balance' => 'Balance'
    ],
    'billCsvFields' =>[
        "mem_alias" => "mem_alias",
        "mem_order" => "mem_order",
        "mem_name" => "mem_name", 
        "mem_type" => "mem_type",
        "bill_date" => "bill_date", 
        "item_no" => "item_no",
        "item_desc" => "item_desc",
        "trans_date" => "trans_date",
        "trans_desc" => "trans_desc",
        "db_amt" => "db_amt",
        "cr_amt" => "cr_amt"
    ],
    'billCsvRequiredFields' => [
        "mem_alias" => "mem_alias",
        "mem_order" => "mem_order",
        "mem_name" => "mem_name", 
        "mem_type" => "mem_type",
        "bill_date" => "bill_date", 
        "item_no" => "item_no",
        "item_desc" => "item_desc",
        "trans_date" => "trans_date",
        "trans_desc" => "trans_desc",
        "db_amt" => "db_amt",
        "cr_amt" => "cr_amt"
    ],
    'contacts' => [
        'contactPath' => 'contacts'
    ],
    'notificationType' => [
        'event' => 'E',
        'announcement' => 'A',
        'member_approval' => 'M'
    ],
    'apns.path.certificate' => (true) // set this to false for development
        ? Yii::getAlias('@common/certs/cert_prod.pem') : Yii::getAlias('@common/certs/cert_dev.pem'),
    "passphrase" => "Pass@123",
    'apns.path' => (true) // set this to false for development
        ? 'ssl://gateway.push.apple.com:2195' : 'ssl://gateway.sandbox.push.apple.com:2195',

    "apns.is_production" => true, // set this to false for development
    "apns.key_id" => "T7676NNRV6",
    "apns.team_id" => "8X2B6BXMCN",
    "apns.app_bundle_id" => "com.digitalmesh.Remember",
    'apns.path.private_key_path' => (true) // set this to false for development
    ? Yii::getAlias('@common/certs/AuthKey_39742C25WN.p8') : Yii::getAlias('@common/certs/AuthKey_39742C25WN.p8'),
    "apns.private_key_secret" => null,
    "android.service.account.path" => Yii::getAlias('@common/certs/firebase-adminsdk.json'),
    "android.project.id" => "clubapp-project",
    "meetingDays" => [
        "Sunday" => "Sunday",
        "Monday" => "Monday",
        "Tuesday" => "Tuesday",
        "Wednesday" => "Wednesday",
        "Thursday" => "Thursday",
        "Friday" => "Friday",
        "Saturday" => "Saturday"
    ],
    'disableOTP' => false,
];
