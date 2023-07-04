<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

error_reporting(E_ALL ^ E_NOTICE);
$uri = trim(request()->getPathInfo(), '/');
$uri = substr($uri,4);
$params = isset($uri[0]) ? explode('/', $uri) : [];
$action = ($params ? array_pop($params) : 'index') . 'Action';
$controller = ($params ? implode('\\', array_map('ucfirst', $params)) : 'Index') . 'Controller';

if (method_exists('App\Api\Controllers\\' . $controller, $action)) {
    Route::group(
        [], function () use ($uri, $controller, $action) {
        Route::match(['get', 'post'], $uri, $controller . '@' . $action);
    }
    );
}
