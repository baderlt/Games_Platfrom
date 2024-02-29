<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Score;
use App\Models\Game;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    ///////////////// get thr user profile 
    public function  User_Profil($username){
        $user=User::where('name','=',$username)    
        ->select('name', 'created_at','id')
        ->first();
        //// check if the user exists 
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
        //// check if the user blocked 
        if($user->blocked ==1){
            return response()->json(['status'=>"not found",'message'=>'Not found'], 204);
        }
        //////// get the the games of user 
        $authoredGames = Game::select('slug', 'titre as title', 'description')
        ->where('auteur','=', $user->id)
        ->get();

        ////////// get the highte scores 
        $highScores = Score::join('gameversions', 'scores.version_jeu_id', '=', 'gameversions.id')
        ->join('games', 'gameversions.game_id', '=', 'games.id')
        ->select('games.slug', 'games.titre as title', 'games.description',
         DB::raw('MAX(scores.score) as score'), DB::raw('MAX(scores.created_at) as timestamp'))
        ->where('scores.user_id', $user->id)
        ->groupBy('games.slug', 'games.titre', 'games.description')
        // ->orderBy('scores.score', 'desc')
        ->get();


    $response = [
        'username' => $user->name,
        'registeredTimestamp' => $user->created_at,
        'authoredGames' => $authoredGames,
        'highscores' => $highScores,
    ];
        return response()->json($response, 200);
    }



}