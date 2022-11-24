<?php

return [
	'version' => '1.0.0',

	'settings' => [
		'routes' => true,
		'error_handler' => true,
		'translate_response' => true,
	],

	// Emails text
	'email' => [
		'subject' => [
			'password' => '🙂 Your new password',
			'register' => '👋 Account activation'
		],
		'message' => [
			'welcome' => 'Welcome',
			'activation' => 'This activation e-mail is sent to the e-mail address that you registered on our site. To activate your account, please click on the link below.',
			'reset_password' => 'This is your new password.',
			'regards' => 'Have a nice day!'
		]
	],

	'event' => [
		'log_created' => true,
		'log_logged' => true,
	],
];
