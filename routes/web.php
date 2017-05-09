<?php
/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/
error_reporting(E_ALL ^ E_NOTICE);
$uri = trim(request()->getPathInfo(), '/');
$params = isset($uri[0]) ? explode('/', $uri) : [];
$action = ($params ? array_pop($params) : 'index') . 'Action';
$controller = ($params ? implode('\\', array_map('ucfirst', $params)) : 'Index') . 'Controller';
/*
|--------------------------------------------------------------------------
| Auto Routes
|--------------------------------------------------------------------------
*/
if (method_exists('App\Http\Controllers\\' . $controller, $action)) {
    Route::group(
        [], function () use ($uri, $controller, $action) {
        Route::match(['get', 'post'], $uri, $controller . '@' . $action);
    }
    );
}