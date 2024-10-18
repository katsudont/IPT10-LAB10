<?php

require "vendor/autoload.php";
require "init.php";

// Database connection object (from init.php (DatabaseConnection))
global $conn;

try {



    // Create Router instance
    $router = new \Bramus\Router\Router();

    // Define routes
    $router->get('/registration-form', '\App\Controllers\RegistrationController@registerForm');
    $router->post('/register', '\App\Controllers\RegistrationController@register');
    $router->get('/login-form', '\App\Controllers\LoginController@showLoginForm');
    $router->post('/login', '\App\Controllers\LoginController@login');
    $router->get('/welcome', '\App\Controllers\HomeController@welcome');
    $router->get('/logout', '\App\Controllers\LoginController@logout');


    $router->get('/', '\App\Controllers\HomeController@index');
    $router->get('/suppliers', '\App\Controllers\SupplierController@list');
    $router->get('/suppliers/{id}', '\App\Controllers\SupplierController@single');
    $router->post('/suppliers/{id}', '\App\Controllers\SupplierController@update');

    $router->get('/registration-form', '\App\Controllers\RegistrationController@registration');
    $router->post('/register', '\App\Controllers\RegistrationController@register');

    // Run it!
    $router->run();

} catch (Exception $e) {

    echo json_encode([
        'error' => $e->getMessage()
    ]);

}
