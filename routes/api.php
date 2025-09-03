<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\ProgrammeController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ArticleController;
use App\Http\Controllers\ArticleUserController;
use App\Http\Controllers\Admin\StatController;
use App\Http\Controllers\Admin\VideoController;

//Auth
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);


#User
Route::get('/list_programmes_for_user', [ProgrammeController::class, 'list_programmes_for_user']);

#Article User
Route::get('/list_article_for_user', [ArticleUserController::class, 'list_article_for_user']);

Route::get('/list_videos_for_user', [VideoController::class, 'list_videos_for_user']);

Route::middleware('auth:sanctum')->group(function () {

    #stat
    Route::get('/stat', [StatController::class, 'stat']);


    //User
    Route::get('/user', [AuthController::class, 'user']); //info de l'utilisateur User
    Route::get('/list_users', [UserController::class, 'list_users']); //list utilisateur Admin


    //Admin Programme
    Route::get('/list_programmes', [ProgrammeController::class, 'list_programmes']);
    Route::post('/add_programme', [ProgrammeController::class, 'add_programme']);
    // Route::get('/afficher_détalis_programme/{id}', [ProgrammeController::class, 'afficher_détalis_programme']);
    Route::post('/update_programme/{id}', [ProgrammeController::class, 'update_programme']);
    Route::delete('/delete_programme/{id}', [ProgrammeController::class, 'delete_programme']);


    //Admin Article
    Route::post('/add_article', [ArticleController::class, 'add_article']);   
    Route::get('/list_article', [ArticleController::class, 'list_article']); 
    Route::post('/update_article/{id}', [ArticleController::class, 'update_article']); 
    Route::delete('/delete_article/{id}', [ArticleController::class, 'delete_article']); 

    Route::post('/add_video', [VideoController::class, 'add_video']);
    Route::get('/list_videos', [VideoController::class, 'list_videos']);
    Route::post('/update_video/{id}', [VideoController::class, 'update_video']);
    Route::delete('/delete_video/{id}', [VideoController::class, 'delete_video']);

});



