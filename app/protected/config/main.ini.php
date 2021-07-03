<?php
date_default_timezone_set('America/Sao_Paulo');
setlocale(LC_MONETARY, 'en_US');
return array(
  // 'language' => 'pt_BR',
	'components' => array(
		'db' => array(
			'connectionString' => 'mysql:host=db;dbname=repairq-test',
			'username' => 'root',
			'password' => 'password',
			'tablePrefix' => '',
			'schemaCachingDuration' => 0,
			'enableProfiling' => true,
			'enableParamLogging' => true,
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
		),
	),
	'params' => array(
		'instanceTimezone' => 'America/Los_Angeles',
	),
);
