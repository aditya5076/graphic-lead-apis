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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::post('register', 'AuthController@register');
Route::post('login', 'AuthController@login');

Route::middleware('jwt.verify')->group(function () {
    Route::post('logout', 'AuthController@logout');
    Route::post('imageqr', 'ImageQrController@store');
    Route::put('imageqr/{uuid}', 'ImageQrController@update');
    Route::get('imageqr/{uuid}', 'ImageQrController@show');
    Route::get('imagequeue', 'ImageQrController@index');
    Route::get('backend-notify/{uuid}', 'ImageQrController@backendNotify');
});
