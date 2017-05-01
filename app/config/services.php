<?php

use Phalcon\DI\FactoryDefault;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;
use Phalcon\Mvc\View\Engine\Volt as VoltEngine;
use Phalcon\Mvc\Model\Metadata\Memory as MetaDataAdapter;
use Phalcon\Session\Adapter\Files as SessionAdapter;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Logger\Adapter\File as Logger;

use TBS\Auth\Auth;
// use Vokuro\Acl\Acl;
use TBS\Mail\Mail;



/**
 * The FactoryDefault Dependency Injector automatically register the right services providing a full stack framework
 */
$di = new FactoryDefault();

/**
 * Register the global configuration as config
 */
$di->set('config', $config);

/**
 * Crypt service
 */
$di->set('crypt', function () use ($config) {
    $crypt = new Crypt();
    $crypt->setKey($config->application->cryptSalt);
    return $crypt;
});

/**
 * The URL component is used to generate all kind of urls in the application
 */
$di->set('url', function () use ($config) {
    $url = new UrlResolver();
    $url->setBaseUri($config->application->baseUri);

    return $url;
}, true);

/**
 * Setting up the view component
 */
$di->set('view', function () use ($config) {

    $view = new View();

    $view->setViewsDir($config->application->viewsDir);

    $view->registerEngines(array(
		".volt" => function ($view, $di) use ($config) {

            $volt = new VoltEngine($view, $di);

            $volt->setOptions(array(
                'compiledPath' => $config->application->cacheDir . 'volt/',
                'compiledSeparator' => '_'
            ));

            return $volt;
        },
        // ".tpl" => 'Phalcon\Mvc\View\Engine\Smarty',
        ".phtml" => 'Phalcon\Mvc\View\Engine\Php'
    ));

    return $view;
}, true);

/**
 * Dispatcher use a default namespace
 */
$di->set('dispatcher', function () {
    $dispatcher = new Dispatcher();
    //$dispatcher->setDefaultNamespace('TBS\Controllers');
    return $dispatcher;
});

/**
 * Custom authentication component
 */
$di->set('auth', function () {
    return new Auth();
});


/**
 * Database connection is created based in the parameters defined in the configuration file
 */
$di->setShared('db', function () use ($di, $config) {
    $connection = new \Phalcon\Db\Adapter\Pdo\Mysql(array(
        'host' => $config->database->host,
        'username' => $config->database->username,
        'password' => $config->database->password,
        'dbname' => $config->database->dbname
    ));

if (defined('DEBUG')) {
    $eventsManager = new EventsManager();
	$user_id  	= $di->get("request")->getPost("user_id") ? $di->get("request")->getPost("user_id") : $di->get("dispatcher")->getParam("user_id");
	
	if ($user_id)
		$logfile = $user_id . "_db.log";
	else
		$logfile = "db.log";
			
    $logger = new Logger($config->application->logDir . $logfile);

    //Listen all the database events
    $eventsManager->attach('db', function($event, $connection) use ($logger) {
        if ($event->getType() == 'beforeQuery') {
            $sqlVariables = $connection->getSQLVariables();
            
            if (count($sqlVariables)) {
                $logger->log($connection->getSQLStatement() . ' ' . join(', ', $sqlVariables));
            } else {
                $logger->log($connection->getSQLStatement());
            }
        }
    });
    
    //Assign the eventsManager to the db adapter instance
    $connection->setEventsManager($eventsManager);
}    
	return $connection;
});

/**
 * If the configuration specify the use of metadata adapter use it or use memory otherwise
 */
$di->set('modelsMetadata', function () {
    return new MetaDataAdapter();
});

/**
 * Start the session the first time some component request the session service
 */
$di->setShared('session', function () {
    $session = new SessionAdapter();
    $session->start();

    return $session;
});

/**
 * Register the flash service with custom CSS classes
 */
$di->set('flash', function(){
	return new Phalcon\Flash\Direct(array(
		'error' => 'alert alert-error',
		'success' => 'alert alert-success',
		'notice' => 'alert alert-info',
	));
});
	
/**
* add routing capabilities
*/
$di->set('router', function(){
    require __DIR__.'/router.php';
    return $router;
});

/**
 * Mail service
 */
$di->set('mail', function () {
    return new Mail();
});

/* Logger
 * 
 * */
$di->set('logger', function() use($config) {
	$logger = new Logger($config->application->logDir . "debug.log");
	
	return $logger;	
});
