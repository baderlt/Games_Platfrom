<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\GameController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Auth;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::post('/user/register', [AuthController::class, 'createUser']);
Route::post('/user/login', [AuthController::class, 'loginUser']);

Route::middleware(['auth:sanctum','BlockUser'])->group(function () {
   // User routes
   Route::post('/user/logout', [AuthController::class, 'logout']);
   Route::get('/users/{username}', [UserController::class,'User_Profil'])->name('user.profile');

   // Games routes
   Route::prefix('games')->group(function () {
       Route::post('/', [GameController::class, 'createGame'])->name('games.create');
       Route::post('/{slug}/upload', [GameController::class, 'uploadVersion'])->name('games.uploadVersion');
       Route::get('/', [GameController::class,'Games_List'])->name('games.list');
       Route::get('/{slug}', [GameController::class,'Game_Slug'])->name('games.slug');
       Route::get('/{slug}/scores', [GameController::class,'GameScores'])->name('games.GameScores');
       Route::get('/{slug}/{version}', [GameController::class,'Game_Version_Files'])->name('games.files');
       Route::put('/{slug}', [GameController::class,'Edit_Game'])->name('games.edit');
       Route::delete('/{slug}', [GameController::class,'Delete_Game'])->name('games.delete');
       Route::post('/{slug}/scores', [GameController::class,'AddScore'])->name('games.AddScore');
   });
});




Route::fallback(function () {
   return response()->json(['status'=>"not found",'message' => 'Not found'], 404);
});
