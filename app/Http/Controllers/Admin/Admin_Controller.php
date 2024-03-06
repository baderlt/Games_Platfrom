<?php

namespace App\Http\Controllers\Admin;

use App\Events\LoginEvent;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Game;
use App\Models\GameVersion;
use App\Models\Score;
use Illuminate\Support\Facades\DB;
use Exception;
use PhpParser\Node\Stmt\Return_;

class Admin_Controller extends Controller
{
    //
    public function index()
    {
        return view('login');
    }

    ////////////// login function auth using guard admin 
    public function login(Request $request)
    {

        $validateUser = Validator::make(
            $request->all(),
            [
                'name' => 'required|string|min:4|max:60',
                'password' => 'required|min:8|max:65536'
            ]
        );
        /////// if the validation fails
        if ($validateUser->fails()) {
            return redirect()->back()->withErrors($validateUser->errors());
        }
        try {
            if (Auth::guard('admin')->attempt(['name' => $request->name, 'password' => $request->password])) {
                ///////// insert the last conexion of user if Authenticated
                $admin = Auth::guard('admin')->user();
                $admin = Admin::where('id', '=', $admin->id)->first();
                $admin->lastConextion = now()->format('Y-m-d H:i:s');
                // Auth::guard('admin')->loginUsingId($admin->id);
                $admin->save();
                event(new LoginEvent($admin));
                return redirect()->route('admin.admins');
            }


            return redirect()->back()->with('error', "Nom d'Admin  ou mot de passe incorrect");
        } catch (\Throwable $th) {

            return back()->with('error', $th->getMessage());
        }
    }

    ////// logout function 
    public function logout()
    {
        try {
            ////// check if user auth 
            if (Auth::guard('admin')->check()) {
                ////////////////  logout 
                Auth::logout();
                return redirect('/login');
            }

            return back()->with('error', 'User not authenticated');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }

    ///// block  or unblock user  
    public function BlockUser(Request $request, $user_id)
    {
        ////// using switch cases for specific the reason 
        try {

            switch ($request->reason) {
                case '0':
                    $reason = 'You have been blocked by an administrator';
                    break;

                case '1':
                    $reason = 'You have been blocked for spamming';
                    break;

                case '2':
                    $reason = 'You have been blocked for cheating';
                    break;
                default:
                    $reason = 'You have been blocked by an administrator';
                    break;
            }
            //////////////////// check if user exists 
            $user = User::where('id', '=', $user_id)->first();
            if (!$user) {
                return back()->with('error', 'user not found');
            }
            ///// if the user is unblocked block hem , and delete all his tockens
            if ($user->blocked == 0) {
                $user->blocked = 1;
                $user->reason = $reason ? $reason : null;
                $user->save();
                $user->tokens()->delete();
                return back();
                ////// if the user is blocked , unblock them 
            } elseif ($user->blocked == 1) {
                $user->blocked = 0;
                $user->reason = null;
                $user->save();

                return redirect()->route('admin.users');
            }
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }

    /////// list Admin 
    public function list_Admin(Request $request)
    {
        try {

            /// get the query parameters search 
            $query = $request->query('search');

            $admins = Admin::select('id', 'name', 'created_at', 'lastConextion')
                /////// id the query exists 
                ->when($query, function ($query_) use ($query) {
                    $query_->where('id', 'like', '%' . $query . '%')
                        ->orwhere('name', 'like', '%' . $query . '%');
                })
                ->paginate(8);
            ////// return view admins with list admins and pagination with 5 
            return view('admin.admins')->with('admins', $admins);
        } catch (\Throwable $th) {
            throw $th;
            return back()->with('error', $th->getMessage());
        }
    }


    ///////// list games with the last version 
    public function Games_List(Request $request)
    {
        try {
            /// get the query parameters search 
            $query = $request->query('search');
            // DB::enableQueryLog();

            ///// get the games with the deleted games 
            $games = Game::withTrashed()
                ////// get the last version of  game 
                ->with(['versions' => function ($query) {
                    $query->latest();
                }, 'author:id,lastConextion,name'])
                ///// if the query exists the games filter with slug or titre 
                ->when($query, function ($query_) use ($query) {
                    $query_->where('slug', 'like', '%' . $query . '%')
                        ->orwhere('titre', 'like', '%' . $query . '%');
                })
                ->get();
            // dd(DB::getQueryLog());

            return view('admin.games')->with('games', $games)->with('serched', $query);
        } catch (\Throwable $th) {
            throw $th;
        }
    }


    ////////// delete game with it scores and version 
    public function game_delete($slug)
    {
        $game = Game::where('slug', '=', $slug)->first();
        if (!$game) {
            return redirect()->back()->with('error', 'the game not found');
        }
        $version_Game = GameVersion::where("game_id", $game->id)->pluck('id')->toArray();
        ////// create a loop in the versions id and delete all scores 
        foreach ($version_Game as $id) {
            Score::where('version_jeu_id', '=', $id)->delete();
        }
        ///// detete all version of game 
        GameVersion::where("game_id", $game->id)->delete();
        $game->delete();
        return redirect()->route('admin.games');
        // return redirect()->back()->with('success','game deleted successfully');

    }


    /////////////////////////// get the games with version and scores using game slug 
    public function Game_slug(Request $request, $slug)
    {

        $query = $request->query('version');
        $user = $request->query('users');
        $game = Game::where('slug', '=', $slug)->withTrashed()->first();
        ////// check if the game exists 
        if (!$game) {
            return redirect()->back()->with('error', 'the game not found');
        }
        /////get the versions of the game for ad in input select for search scors with versions  
        $versions = $game->versions()->get('version');
        ///////// get the all info for game     
        $game = $game->leftJoin('gameversions', 'games.id', '=', 'gameversions.game_id')
            ->join('users', 'games.auteur', '=', 'users.id')
            ->leftJoin('scores', 'gameversions.id', '=', 'scores.version_jeu_id')
            ->select(DB::raw('slug, description, titre, vignette, MAX(gameversions.version) as last_version, 
        COALESCE(MAX(gameversions.created_at), games.created_at) as uploadTimestamp,
        users.name as auteur, COALESCE(SUM(scores.score), 0) as scoreCount, 
        COALESCE((SELECT path FROM gameversions 
        WHERE game_id = games.id 
        ORDER BY created_at DESC 
        LIMIT 1) , null) as gamePath '))
            ->where('games.id', '=', $game->id)
            ->groupBy('games.slug', 'games.description', 'games.titre', 'games.vignette', 'users.name')
            ->first();


        ////// get the all scores of game 
        $scores = Score::select('score', 'scores.id', 'users.name as name', 'gameversions.version as version', 'scores.created_at as date')
            ->join('gameversions', 'gameversions.id', '=', 'scores.version_jeu_id')
            ->join('games', 'games.id', '=', 'gameversions.game_id')
            ->join('users', 'scores.user_id', '=', 'users.id')
            ->where('games.slug', '=', $slug);

        /////  get the name of users has a scores in this game , for use in filter 
        $names = $scores->pluck('name')->unique()->values()->all();

        //////// gte the scores filtred with version and user ;
        $scores = $scores->when($query, function ($query_) use ($query) {
            $query_->where('gameversions.version', '=', $query);
        })
            ->when($user, function ($query_) use ($user) {
                $query_->where('users.name', $user);
            })
            ->orderby('score', 'desc')
            ->get();

        return view('admin.game')->with('game', $game)->with('scores', $scores)->with('versions', $versions)->with("versionItem", $query)
            ->with('names', $names)->with('useritem', $user);
    }



    /////////////////// get the scorse of game using slug 
    public function GameScores($slug)
    {
        $gameId = Game::where('slug', $slug)->value('id');
        if (!$gameId) {
            return redirect()->back()->with('error', 'the game not found');
        }
        // Get highest scores in game specific
        $highestScores = Score::select(
            'users.name as username',
            DB::raw('MAX(scores.score) as score'),
            DB::raw('MAX(scores.created_at) as horodatage')
        )
            ->join('gameversions', 'scores.version_jeu_id', '=', 'gameversions.id')
            ->join('users', 'scores.user_id', '=', 'users.id')
            ->where('gameversions.game_id', $gameId)
            ->groupBy('users.name')
            ->orderBy('score', 'desc')
            ->get();
        return response()->json($highestScores, 200);
    }





    /////////////////  dlete score of all game version or one version 
    public function Delete_Scores_Game(Request $request, $slug)
    {

        $game = Game::where('slug', '=', $slug)->first();
        if (!$game) {
            return redirect()->back()->with('error', 'the game not found');
        }
        $version = $request->query('version');
        if ($version == null || $version == 'all' || $version == "") {
            $version_Game = GameVersion::where("game_id", $game->id)->pluck('id')->toArray();
            foreach ($version_Game as $id) {
                Score::where('version_jeu_id', '=', $id)->delete();
            }
            return back()->with('message', "scores {$slug} deleted successfully");
        }

        $version_Game = GameVersion::where("game_id", $game->id)
            ->where('version', '=', $version)
            ->first();
        Score::where('version_jeu_id', '=', $version_Game->id)->delete();
        return back()->with('message', "scores {$slug} - version  {$version} deleted successfully");
        // return  "scores {$slug} - version  {$version} deleted successfully";
    }


    /////////////////////// delete all scores users or scores of version 
    public function Delete_User_Scores(Request $request, $user, ?int  $version = null)
    {

        try {
            // Extract the version from the query parameter
            $version = $request->query('');
            // Use the `when` method to conditionally apply the version filter
            Score::where('user_id', '=', $user)
                ->when($version, function ($query) use ($version) {
                    $query->where('version_jeu_id', '=', $version);
                })
                ->delete();
            // Return a response indicating successful deletion
            return back()->with('message', 'scores deleted with success');
            // }
        } catch (\Throwable $th) {
            throw $th;
        }
    }


    /////////////// delete on score user 
    public function Delete_one_Score_User($score_id)
    {
        try {

            Score::where('id', '=', $score_id)
                ->delete();
            return back()->with('message', 'scores deleted with success');
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    // This function  listing users based on the provided search query
    function List_Users(Request $request)
    {
        try {

            $query = $request->query('search');
            $users = User::when($query, function ($query_) use ($query) {
                $query_->where('name', 'like', '%' . $query . '%');
            })
                ->paginate(8);
            // Return the users to the 'admin.users' view

            return view('admin.users')->with('users', $users);
        } catch (\Throwable $th) {
            throw $th;
        }
    }


    ///////////  gte the user profile his games and scores 
    public function Profile_User($username)
    {
        try {

            $user = User::where('name', '=', $username)
                ->with(['games' => function ($query_) {
                    $query_->withTrashed();
                }])->first();
            if (!$user) {
                return back()->with('message', 'not found ');
            }
            // return $user;
            return view('admin.User_Profile')->with('username', $username)->with('user', $user);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
