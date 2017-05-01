<?php
ini_set("display_errors", 1);
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

define('DEBUG', true);

return new \Phalcon\Config(array(
    'database' => array(
        'adapter'     => 'Mysql',
        'host'        => 'localhost',
        'username'    => 'thebestsong',
        'password'    => 'JNoj7U3xbb',
        'dbname'      => 'thebestsong',
    ),
    'application' => array(
        'controllersDir' => __DIR__ . '/../../app/controllers/',
        'modelsDir'      => __DIR__ . '/../../app/models/',
        'viewsDir'       => __DIR__ . '/../../app/views/',
        'formsDir' 		 => __DIR__ . '/../../app/forms/',
        'pluginsDir'     => __DIR__ . '/../../app/plugins/',
        'libraryDir'     => __DIR__ . '/../../app/library/',
        'logDir'     	 => __DIR__ . '/../../app/logs/',
        'templatesDir'   => __DIR__ . '/../../app/templates/',
        'templatesCompiledDir'   => __DIR__ . '/../../app/templates_c/',
        'cacheDir'       => __DIR__ . '/../../app/cache/',
        'baseUri'        => '/thebestsong/',
        'publicUrl' => 'http://dev.res-populi.com/thebestsong',
        'cryptSalt' => 'eEAfR|_&G&f,+vU]:jFr!!A&+71w1Ms9~8_4L!<@[N@DyaIP_2My|:+.u>/6m,$D',
        'client_session_key' => 'Maradona'        
    ),
    'mail' => array(
        'fromName' => 'The best Song',
        'fromEmail' => 'tbs@smartcrew.eu',
        'smtp' => array(
            'server' => 'localhost',
            'port' => 25,
            'security' => 'tls',
            'username' => '',
            'password' => ''
        )
    ),
    'facebook' => array(
		'appId' => '749556845096687',
		'appSecret' => '59c82eef46adac991782546a39e57717'
    ),    
    'log' => false
));
