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

$router->group(['prefix' => 'api'], function () use ($router) {
    $router->post('country', 'CountryController@store');
    $router->get('country/{id}', 'CountryController@single');
    $router->get('country', 'CountryController@index');
    $router->get('country/tree', 'CountryController@tree');
    $router->put('country/{id}', 'CountryController@update');
    $router->delete('country/{id}', 'CountryController@delete');

    $router->post('city', 'CityController@store');
    $router->get('city/{id}', 'CityController@single');
    $router->get('city', 'CityController@index');
    $router->get('city/tree', 'CityController@tree');
    $router->put('city/{id}', 'CityController@update');
    $router->delete('city/{id}', 'CityController@delete');

    $router->post('district', 'DistrictController@store');
    $router->get('district/{id}', 'DistrictController@single');
    $router->get('district', 'DistrictController@index');
    $router->get('district/tree', 'DistrictController@tree');
    $router->put('district/{id}', 'DistrictController@update');
    $router->delete('district/{id}', 'DistrictController@delete');
});
