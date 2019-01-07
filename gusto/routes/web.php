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

$router->group(['prefix' => 'v2/api'], function () use ($router) {
    
     // publish all the events
    $router->get('pub', ['uses' => 'PubsubController@publish']);
     // publish batch the events
    $router->get('batch-pub', ['uses' => 'PubsubController@batch_publish']);
    // subscribe the message
    $router->get('sub', ['uses' => 'PubsubController@subscribe']);
});
