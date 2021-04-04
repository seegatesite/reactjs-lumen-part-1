<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
 */

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->post('/item/add', 'ItemController@add');
$router->post('/item/edit', 'ItemController@edit');
$router->post('/item/delete', 'ItemController@delete');
$router->get('/item/get', 'ItemController@get');
$router->get('/item/list', 'ItemController@list');
$router->get('/item/search', 'ItemController@search');

$router->post('/customer/add', 'CustomerController@add');
$router->post('/customer/edit', 'CustomerController@edit');
$router->post('/customer/delete', 'CustomerController@delete');
$router->get('/customer/get', 'CustomerController@get');
$router->get('/customer/list', 'CustomerController@list');
$router->get('/customer/search', 'CustomerController@search');

$router->post('/sales/add', 'SalesController@add');
$router->post('/sales/delete', 'SalesController@delete');
$router->get('/sales/get', 'SalesController@get');
$router->get('/sales/list', 'SalesController@list');
