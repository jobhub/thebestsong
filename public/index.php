<?php

try {

	$configFileName = "config_local.php";
	
	if (!isset($_SERVER["SERVER_NAME"]))
		$SERVER_NAME = php_uname("n");
	else 	
		$SERVER_NAME = $_SERVER["SERVER_NAME"];
					
    $arr = explode('.', $SERVER_NAME);
    $version = $arr[0];
 
	if ($version == "dev") {
	 $configFileName = "config_dev.php";
	} elseif ($version == "192") {
	 $configFileName = "config.php";
	}    
 

    /**
     * Read the configuration
     */
    $config = include __DIR__ . "/../app/config/" . $configFileName;

    /**
     * Read auto-loader
     */
    include __DIR__ . "/../app/config/loader.php";

    /**
     * Read services
     */
    include __DIR__ . "/../app/config/services.php";


    /**
     * Read utils functions
     */
    include __DIR__ . "/../app/config/utils.php";

    /**
     * Handle the request
     */
    
    $application = new \Phalcon\Mvc\Application($di);
	
    echo $application->handle()->getContent();

} catch (\Exception $e) {
    echo $e->getMessage();
}
