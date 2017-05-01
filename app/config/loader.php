<?php

$loader = new \Phalcon\Loader();

/**
 * We're a registering a set of directories taken from the configuration file
 */
$loader->registerDirs(
    array(
        $config->application->controllersDir,
        $config->application->modelsDir,
        $config->application->libraryDir,
        $config->application->libraryDir . "/facebook-php-sdk/src"
    )
)->register();

$loader->registerNamespaces(array(
    'TBS' => $config->application->libraryDir,
    'TBS\Models' => $config->application->modelsDir,
    'TBS\Forms' => $config->application->formsDir,
))->register();

// Use composer autoloader to load vendor classes
require_once __DIR__ . '/../../vendor/autoload.php';
