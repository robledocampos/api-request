<?php

use Phalcon\Di\FactoryDefault;
use Phalcon\Loader;
use Phalcon\Mvc\Application;
use Phalcon\Url;
use Phalcon\Http\Request;

// Define some absolute path constants to aid in locating resources
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');

// Register an autoloader
$loader = new Loader();

$loader->registerDirs(
    [
        APP_PATH . '/exceptions/',
        APP_PATH . '/services/'
    ]
);

$loader->register();

$container = new FactoryDefault();

$application = new Application($container);

try {
    // Handle the request
    $uri = $_SERVER['QUERY_STRING'] ? explode("=", $_SERVER['QUERY_STRING'])[1] : "/";
    $response = $application->handle($uri);
    $response->send();
} catch (Exception $e) {
    echo $e->getMessage();
}
