<?php

// Display all errors; this is for development only
ini_set('display_errors',1);
ini_set('display_startup_errors',1); 
error_reporting(E_ALL);

// Calls a function when any class is loaded
// So every time a class is loaded, it will include the files from the directory
spl_autoload_register(function ($Name) {
    require_once(__DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $Name) . '.php');
});

use controllers\AuthController;
use controllers\ItemTypeController;
use controllers\MainPageController;
use controllers\UsersController;
use core\Application;
use core\Config;

date_default_timezone_set(Config::TIME_ZONE);

$app = Application::current();

// Define the routes
$app->router->get('/', [MainPageController::class, 'home']);

$app->router->get('/register', [AuthController::class, 'register']);
$app->router->post('/register', [AuthController::class, 'register']);
$app->router->get('/login', [AuthController::class, 'login']);
$app->router->post('/login', [AuthController::class, 'login']);
$app->router->get('/logout', [AuthController::class, 'logout']);

$app->router->get('/user', [UsersController::class, 'user']);
$app->router->post('/user', [UsersController::class, 'user']);

$app->router->get('/item-types', [ItemTypeController::class, 'item_types']);

$app->run();
