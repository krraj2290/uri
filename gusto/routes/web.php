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
    return $router->app->version();
});

$router->group(['prefix' => 'v1/api'], function () use ($router) {
    
    // insert all the events
    $router->post('tracks', ['uses' => 'TracksController@create']);
    // update event
    $router->put('tracks/{id}', ['uses' => 'TracksController@update']);
    // get event
    $router->get('tracks/{id}', ['uses' => 'TracksController@getEvent']);
    // delete event
    $router->delete('tracks/{id}', ['uses' => 'TracksController@delete']);
    // get all events
    $router->get('tracks', ['uses' => 'TracksController@getAllEvents']);
    // redis lib check
    $router->get('redis', ['uses' => 'TracksController@checkredis']);
    
    // queue lib check
    $router->get('queue', ['uses' => 'TracksController@queue']);
});
