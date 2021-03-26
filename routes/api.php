<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\JeuController;
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

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/user-profile', [AuthController::class, 'userProfile']);
});

//Route::post('/jeux', [JeuController::class, 'store'])->middleware('api') ;
Route::middleware('auth:api')->post('/jeux', [JeuController::class, 'store']);
Route::get('/jeux', [JeuController::class, 'index']);
Route::get('/jeux/{id}', [JeuController::class, 'show']);
/*
 * Route::group([
    'prefix' => 'jeux'
], function ($router) {
    Route::get('', [JeuController::class, 'index']);
});
*/

Route::post('/commentaires', [\App\Http\Controllers\CommentaireController::class, 'store'])->middleware('api') ;
Route::delete('/commentaires/{id}', [\App\Http\Controllers\CommentaireController::class, 'destroy'])->middleware('api') ;

Route::post('/users/{id}/achat', [\App\Http\Controllers\UserController::class, 'ajouteAchat'])->middleware('api')->where('id', '[0-9]+') ;
Route::post('/users/{id}/vente', [\App\Http\Controllers\UserController::class, 'supprimeAchat'])->middleware('api')->where('id', '[0-9]+') ;
Route::get('/users/{id}', [\App\Http\Controllers\UserController::class, 'show'])->middleware('api') ->where('id', '[0-9]+');
Route::put('/users/{id}', [\App\Http\Controllers\UserController::class, 'update'])->middleware('api')->where('id', '[0-9]+') ;



