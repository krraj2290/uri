<?php

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
        try{
            $rConf = array('server' => env('REDIS_HOST'), 'port' => env('REDIS_PORT'));
            $objRedis = app('redis');
            $objRedis->connect($rConf['server'], $rConf['port']);
            echo " Redis Server is running: " . $objRedis->ping();
            echo "<hr />";
            
        } catch (Exception $ex) {
            echo "Error:redis::" . $ex->getMessage();
        }
        die;
        
    return $router->app->version();
});
