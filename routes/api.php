<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
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


// Route::middleware('json.verify')->group(function () {
Route::post('register', 'AuthController@register');
Route::post('login', 'AuthController@login');

Route::middleware(['jwt.verify'])->group(function () {
    Route::post('logout', 'AuthController@logout');
    Route::post('imageqr', 'ImageQrController@store')->middleware('json.verify');
    Route::put('imageqr/{uuid}', 'ImageQrController@update');
    Route::get('imageqr/{uuid}', 'ImageQrController@show');
    Route::get('imagequeue', 'ImageQrController@index');
});
Route::get('backend-notify/{uuid}', 'ImageQrController@backendNotify');
// });


// Route::get('storage/{filename}', function ($filename) {
//     $path = storage_path('images/' . $filename);

//     if (!File::exists($path)) {
//         abort(404);
//     }

//     $file = File::get($path);
//     $type = File::mimeType($path);

//     $response = Response::make($file, 200);
//     $response->header("Content-Type", $type);

//     return $response;
// });
