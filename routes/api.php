<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\FileController;

Route::group([
    'middleware' => 'api',
    "namespace" => "App\Http\Controllers",
], function ($router) {
    $router->controller(AuthController::class)
        ->namespace("Auth")
        ->group(function ($router) {
            $router->post('register', 'register');
            $router->post('login', 'login');
            $router->post('logout', 'logout')->middleware('auth:api');
            $router->get('me', 'getMe')->middleware('auth:api');
        });

    $router->middleware(['api.permission'])
        ->namespace("Api")
        ->group(function ($router) {
            $router->get('files/{unique_key}/{filename}', [FileController::class, 'show']);
            $router->apiResource('files', FileController::class)->except(['update']);
            $router->post('files/multiple', [FileController::class, 'multipleStore']);
        });
});
