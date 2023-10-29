<?php
ini_set('display_errors', 'On');

require_once __DIR__ . '/../src/autoload.php';

use Slim\Factory\AppFactory;
use TWB\Config\Routes;
use TWB\Middlewares\DefaultErrorMiddleware;
use TWB\Services\LogService as Logger;


// Initialize application
$app = AppFactory::create();

// Initialize logger
$logger = new Logger();

// Add Middlewares
$app->addRoutingMiddleware();
$errorMiddleware = $app->addErrorMiddleware(true, true, true);

// Setting ErrorMiddleware
$errorMiddleware->setDefaultErrorHandler(new DefaultErrorMiddleware($app, $logger->getLogger()));

// Set routing
$routes = new Routes;
$app = $routes($app);

// Application running
$app->run();