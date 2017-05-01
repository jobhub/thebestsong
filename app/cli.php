<?php

 use Phalcon\DI\FactoryDefault\CLI as CliDI,
     Phalcon\CLI\Console as ConsoleApp,
     Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter,
	 Phalcon\Events\Manager as EventsManager,
	 Phalcon\Logger\Adapter\File as Logger;

 define('VERSION', '1.0.0');

 //Using the CLI factory default services container
 $di = new CliDI();

 // Define path to application directory
 defined('APPLICATION_PATH')
 || define('APPLICATION_PATH', realpath(dirname(__FILE__)));


 // Load the configuration file (if any) : check on which platform we are 
 $version = getWebsiteVersion();
 $configFileName = "config_local.php";
 
 if ($version == "dev") {
	 $configFileName = "config_dev.php";
 } elseif ($version == "www") {
	 $configFileName = "config.php";
 }

 if(is_readable(APPLICATION_PATH . "/config/" . $configFileName)) {
     $config = include APPLICATION_PATH . '/config/' . $configFileName;
     $di->set('config', $config);
 }

 /**
  * Register the autoloader and tell it to register the tasks directory
  */
 $loader = new \Phalcon\Loader();
 $loader->registerDirs(
     array(
         APPLICATION_PATH . '/tasks',
         $config->application->modelsDir
     )
 );
 $loader->register();

/**
 * Database connection is created based in the parameters defined in the configuration file
 */
$di->set('db', function () use ($config) {

    $connection = new \Phalcon\Db\Adapter\Pdo\Mysql(array(
        'host' => $config->database->host,
        'username' => $config->database->username,
        'password' => $config->database->password,
        'dbname' => $config->database->dbname
    ));
    
    if (defined('CLI-DEBUG')) {
		$eventsManager = new EventsManager();

		$logger = new Logger("app/logs/db.log");

		//Listen all the database events
		$eventsManager->attach('db', function($event, $connection) use ($logger) {
			if ($event->getType() == 'beforeQuery') {
				$sqlVariables = $connection->getSQLVariables();
				
				if (count($sqlVariables)) {
					$logger->log($connection->getSQLStatement() . ' ' . join(', ', $sqlVariables), \Phalcon\Logger::INFO);
				} else {
					$logger->log($connection->getSQLStatement(), \Phalcon\Logger::INFO);
				}
			}
		});

		//Assign the eventsManager to the db adapter instance
		$connection->setEventsManager($eventsManager);
	}
	
	return $connection;
});

 //Create a console application
 $console = new ConsoleApp();
 $console->setDI($di);

 /**
 * Process the console arguments
 */
 $arguments = array();
 foreach($argv as $k => $arg) {
     if($k == 1) {
         $arguments['task'] = $arg;
     } elseif($k == 2) {
         $arguments['action'] = $arg;
     } elseif($k >= 3) {
        $arguments[] = $arg;
     }
 }
 
 // define global constants for the current task and action
 define('CURRENT_TASK', (isset($argv[1]) ? $argv[1] : null));
 define('CURRENT_ACTION', (isset($argv[2]) ? $argv[2] : null));
 
 set_error_handler('handleError');

 try {
     // handle incoming arguments
     $console->handle($arguments);
 }
 catch (ErrorException $e) {
	 echo $e->getMessage(). PHP_EOL;
	 print_r($e->getTrace()[0]);
     exit();
 } catch (\PDOException $e) {
     print "alo";
	 echo $e->getMessage() . PHP_EOL;
     echo $e->getTrace()[0];
	 exit();	 
 }

 // turn php warnings/errors into exceptions
 function handleError($errno, $errstr, $errfile, $errline, array $errcontext)
 {
   // error was suppressed with the @-operator
   if (0 === error_reporting()) {
    return false;
   }

   throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
 }
 
 function getWebsiteVersion() {
	
	if (!isset($_SERVER["SERVER_NAME"]))
		$SERVER_NAME = php_uname("n");
	else 	
		$SERVER_NAME = $_SERVER["SERVER_NAME"];
					
    $arr = explode('.', $SERVER_NAME);
    $version = $arr[0];
    
    return $version;
}
 
 ?>
