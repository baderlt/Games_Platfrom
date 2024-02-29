<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Admin_Controller;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Api\GameController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::get('/', function () {
    return redirect()->route('login');
});


Route::middleware('guest:admin')->group(function () {
    Route::post('/admin/login', [Admin_Controller::class, 'login'])->name('admin.login');
    Route::get('/login', [Admin_Controller::class, 'index'])->name('login');
});


Route::middleware('auth:admin')->group(function () {
    Route::prefix('admin')->group(function () {

        Route::post('/logout', [Admin_Controller::class, 'logout'])->name('admin.logout');

        // Users routes
        Route::prefix('users')->group(function () {
            Route::post('/block/{user_id?}', [Admin_Controller::class, 'BlockUser'])->name('admin.BlockUser');
            Route::get('/', [Admin_Controller::class, 'List_Users'])->name('admin.users');
        });

        // Admins routes
        Route::get('/admins', [Admin_Controller::class, 'list_Admin'])->name('admin.admins');

        // Games routes
        Route::prefix('games')->group(function () {
            Route::delete('/{slug}', [Admin_Controller::class, 'game_delete'])->name('admin.games_delete');
            Route::get('/', [Admin_Controller::class, 'Games_List'])->name('admin.games');
            Route::get('/{slug}', [Admin_Controller::class, 'Game_slug'])->name('admin.games.slug');
            Route::get('/{slug}/scores', [Admin_Controller::class, 'GameScores'])->name('admin.GameScores');
            Route::delete('/scores/{slug}', [Admin_Controller::class, 'Delete_Scores_Game'])->name('admin.Delete_scores_version');
            Route::delete('/scores/user/{user}/{version?}', [Admin_Controller::class, 'Delete_User_Scores'])
                ->name('admin.Delete_User_Scores');
            Route::delete('/score/user/{user_id}/{score_id?}', [Admin_Controller::class, 'Delete_one_Score_User'])
                ->name('admin.Delete_one_Score_User');
        });
    });
});
