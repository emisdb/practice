<?php
date_default_timezone_set('America/Chicago');
return array(
	'components'=>array(
		'db'=>array(
			'connectionString' => 'mysql:host=db:3306;dbname=repairq-test',
			'emulatePrepare' => true,
			'username' => 'root',
			'password' => 'password',
			'charset' => 'utf8',
			'tablePrefix' => '',
			'schemaCachingDuration' => 3600,
			'enableProfiling' => true,
			'enableParamLogging' => true,
		),
		'mail' => array(
			'class' => 'ext.mail.Mail', //set to the path of the extension
			'transportType' => 'php',
			'viewPath' => 'application.views.mail',
			'debug' => false
		),
		'log' => array(
			'class' => 'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error, warning, info',
					'logFile' => 'application.log'
				),
				// array(
				// 	'class'=>'ext.yii-debug-toolbar.YiiDebugToolbarRoute',
				// 	//'ipFilters'=>array('127.0.0.1','::1', '172.*'),
				// ),
			),
		)
	),
	'params'=>array(
		'instanceTimezone' => 'America/Los_Angeles',
	)
);
