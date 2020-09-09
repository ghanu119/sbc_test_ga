<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['namespace' => 'App\Http\Controllers\API'], function(){

    Route::post('/register', 'AuthController@register');
    Route::post('/login', 'AuthController@login');

    Route::post('status/get-all', 'StatusController@getAll');

    Route::group(['prefix' => 'status', 'middleware' => 'auth:api'], function(){
                Route::post('/create', 'StatusController@create');
                Route::post('/view', 'StatusController@view');
                Route::post('/delete', 'StatusController@delete');
                
            });
});