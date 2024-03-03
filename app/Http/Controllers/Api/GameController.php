<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Game;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use ZipArchive;
use App\Models\GameVersion;
use Illuminate\Support\Facades\Storage;
use App\Models\Score;
use Illuminate\Support\Facades\Validator;

class GameController extends Controller
{
    ////////// get the games List 
    public function Games_List(Request $request)
    {
        // Extract request parameters or use default values
        $page = $request->page ?? 0;
        $taille = $request->taille ?? 10;
        $trier_par = in_array(strtoupper($request->trier_par), ['titre', 'slug', 'uploadTimestamp', 'scoreCount']) ? $request->trier_par : "titre";
        $sortDir = strtoupper($request->sortDir) === 'DESC' ? 'DESC' : 'ASC';

        //  get paginated games
        if ($taille < 1) {
            $taille = 1;
        }
        if ($page < 0) {
            $page = 0;
        }
        
        ///// get the games with slug,titre,vignette,uploadTimestamp (the last version of game ),name of auteur ,scoreCount the sum scores in this game 
        $query = Game::join('gameversions', 'games.id', '=', 'gameversions.game_id')
            ->join('users', 'games.auteur', '=', 'users.id')
            ->leftJoin('scores', 'gameversions.id', '=', 'scores.version_jeu_id')
            ->select(DB::raw('slug, titre, vignette, MAX(gameversions.created_at) as uploadTimestamp,
        users.name as auteur, COALESCE(SUM(scores.score), 0) as scoreCount'))
            ->orderBy($trier_par, $sortDir)
            ->groupBy('games.slug', 'gameversions.game_id', 'games.titre', 'games.vignette', 'users.name')
            ->offset($page * $taille)
            ->limit($taille);

        // Execute the query
        $games = $query->get();
        ///totale count
        $totalCount = $games->count();
        $pageCount = ceil($totalCount / $taille);

        $isLastPage = ($page + 1) * $taille >= $totalCount;

        $response = [
            'isLastPage' => $isLastPage,
            'pageCount' => $pageCount,
            'page' => $page,
            'taille' => $taille,
            'totalElements' => $totalCount,
            'contenu' => $games,
        ];
        // Return the response as JSON
        return response()->json($response, 200);
    }

    //////////// create a new game wit genirate a unique slug
    public function createGame(Request $request)
    {
        // Validate the request data using  Validator
        $validator = Validator::make($request->all(), [
            'titre' => 'required|string|min:3|max:60',
            'description' => 'string|min:0|max:200',

        ]);
        // Check if validation fails
        if ($validator->fails()) {
            $violations = [];
            foreach ($validator->errors()->messages() as $field => $messages) {
                $violations[$field] = [
                    'message' => $messages[0],
                ];
            }

            return response()->json([
                'status' => 'invalid',
                'message' => 'Le corps de la demande n\'est pas valide.',
                'violations' => $violations,
            ], 400);
        }
        // Generate a unique slug for the game based on the title
        $slug = Str::slug($request->titre, '-', 'en');
        // slug is unique in the database
        $uniqueSlug = $slug;
        // Check if the generated slug already exists in the database
        if (Game::where('slug', $uniqueSlug)->exists()) {
            return response()->json([
                "status" => "invalid",
                "slug" =>  "Le titre du jeu existe déjà"
            ], 400);
        }
        //// create a new game 
        $game = new Game;
        $game->titre = $request->titre;
        $game->description = $request->description;
        $game->slug = $uniqueSlug;
        $game->auteur = auth()->user()->id;
        $game->save();
        // Return a JSON response with success status, created status code, and the unique slug
        return response()->json(["status" => "succès", 'slug' => $uniqueSlug], 201);
    }


    ////////////get Game With Slug
    public function Game_Slug($slug = null)
    {
        // Find the game with the given slug with the deleted games  games
        $game = Game::where('slug', '=', $slug)->withTrashed()->first();
        // Check if the game exists
        if (!$game) {
            // If the game is not found, return a JSON response with a 404 status
            return response()->json(['status' => "not found", 'message' => 'Not found']);
        }
        // Query to retrieve detailed information about the game, including versions, author, and scores
        $game = $game->leftJoin('gameversions', 'games.id', '=', 'gameversions.game_id')
            ->join('users', 'games.auteur', '=', 'users.id')
            ->leftJoin('scores', 'gameversions.id', '=', 'scores.version_jeu_id')
            ->select(DB::raw('slug, description, titre, vignette, 
            COALESCE(MAX(gameversions.created_at), games.created_at) as uploadTimestamp,
            users.name as auteur, COALESCE(SUM(scores.score), 0) as scoreCount, 
            COALESCE((SELECT path FROM gameversions 
            WHERE game_id = games.id 
            ORDER BY created_at DESC 
            LIMIT 1) , null) as gamePath'))
            ->groupBy('games.slug', 'games.description', 'games.titre', 'games.vignette', 'users.name')
            ->get();

        return response()->json($game, 200);
    }


    /////////// upload a new version for game 
    public function uploadVersion(Request $request, $slug)
    {
        try {
            // Check if a file is uploaded

            // $result =$this->UploadGame->Upload();
            // return $result;
            if (!$request->hasFile('zipfile')) {
                return response()->json(['status' => 'invalid', 'message' => 'File Zip not found '], 400);
            }
            // get the game bu slug
            $game = Game::where('slug', '=', $slug)->first();
            if (!$game) {
                return response()->json(['status' => 'not found', 'message' => 'game not found '], 403);
            }
            // Check if  user is the author of the game
            if (auth()->user()->id !== $game->auteur) {
                return response()->json(['status' => 'forbidden', 'message' => 'You are not the game author'], 403);
            }
            // Validation file the size and the type 'mimes=zip'

            // get the last version and increment with 1 
            $NextVersion = GameVersion::where('game_id', '=', $game->id)->max('version') + 1;
            // Extracted file  path
            $extractedPath = "games/{$slug}";
            // Store the uploaded ZIP file in the extracted path
            $zipFile = $request->file('zipfile');
            $zipFileName = $zipFile->getClientOriginalName();
            $zipFile->storeAs($extractedPath, $zipFileName,  'public');
            $zip = new ZipArchive;
            $zipFilePath = public_path("storage/{$extractedPath}/{$zipFileName}");
            //// check if zip file open with succses
            // dd($zip->open($zipFilePath,ZipArchive::CREATE));
            if ($zip->open($zipFilePath) === true) {
                // extracted the file zip
                // dd('ggg');
            //    dd(public_path("storage/{$extractedPath}"));
                $extractResult = $zip->extractTo(public_path("storage/{$extractedPath}"));
                $zip->close();
                // dd($extractResult);
                if ($extractResult === true) {
                    //// delete the file zip extracted 
                    unlink(public_path("storage/{$extractedPath}/{$zipFileName}"));

                    ////// get the file name of extracted game 
                    $fileName = pathinfo($zipFileName, PATHINFO_FILENAME);
                    $from = public_path("storage/{$extractedPath}/{$fileName}");
                    $to = public_path("storage/{$extractedPath}/{$NextVersion}");
                    ////// check if the file index.html exists
                    if (!file_exists($from . "/index.html")) {
                        ////// if not exists delete the file game extracted
                        Storage::disk('local')->deleteDirectory("storage/{$extractedPath}/{$fileName}");
                        return response()->json(['status' => 'invalid', 'message' => 'File index.html not exists'], 400);
                    }

                    ///// rename the file extracted 
                    rename($from, $to);
                    // Game path
                    $gamePath = "{$extractedPath}/{$NextVersion}";
                    // create a new version of game 
                    $gameVersion = new GameVersion;
                    $gameVersion->game_id = $game->id;
                    $gameVersion->version = $NextVersion;
                    $gameVersion->path = $gamePath;
                    $gameVersion->save();
                    // Update the game miniature path if miniature.png is exists
                    $miniature = file_exists(public_path("storage/{$gamePath}/miniature.png"))
                        ? "{$gamePath}/miniature.png"
                        : null;
                    if ($miniature !== null) {
                        $game->vignette = $miniature;
                        $game->save();
                    };
                    return response()->json(['status' => 'succes', 'path' => $gamePath]);
                } else {
                    // Extraction failed
                    return response()->json(['status' => 'invalid', 'message' => 'ZIP file extraction failed'], 400);
                }
            } else {
                //// Unable to open the ZIP file
                return response()->json(['status' => 'invalid', 'message' => 'Unable to open the ZIP file'], 400);
            }
        } catch (\Throwable $th) {
            return $th;
            return response()->json(['status' => 'error', 'message' => $th->getMessage()], 500);
        }
    }



    //////////////////////// get index.html file for game specific and vesrsion specific
    public function Game_Version_Files($slug = null, $version = null)
    {
        try {
            /// get the path of the game slug is $slug and version is $version
            $game = Game::where(['slug' => $slug])->where(['gameversions.version' => $version])
                ->leftJoin('gameversions', 'games.id', '=', 'gameversions.game_id')
                ->select('gameversions.path')
                ->first();
            // Construct the file path for the index.html file
            $filePath = $game->path . '/index.html';
            // Check if the file exists in the public storage path
            if (file_exists(public_path("storage/$filePath"))) {
                // If the file exists, return the file as a response
                return response()->file(public_path("storage/{$filePath}"));
            } else {
                // If the file is not found, return a JSON response with a 404 status
                return response()->json(['status' => 'invalid', 'message' => 'Not Found '], 404);
            }
        } catch (\Throwable $th) {
            return response()->json(['status' => 'error', 'message' => 'Not Found'], 500);
        }
    }

    ////////////////////////////// edit game title and description
    public function Edit_Game(Request $request, $slug)
    {
        // $request->validate([
        //     'titre' => 'required|string|max:60',
        //     'description' => 'max:200',
        // ]);
        // Validate  data using  Validator
        $validator = Validator::make($request->all(), [
            'titre' => 'required|string|max:60',
            'description' => 'max:200',

        ]);
        // Check if validation fails
        if ($validator->fails()) {
            $violations = [];
            foreach ($validator->errors()->messages() as $field => $messages) {
                $violations[$field] = [
                    'message' => $messages[0],
                ];
            }
            // Return a JSON response with invalid status, error message, and violations
            return response()->json([
                'status' => 'invalid',
                'message' => 'Le corps de la demande n\'est pas valide.',
                'violations' => $violations,
            ], 400);
        }

        try {

            // Find the game  by slug
            $game = Game::where('slug', '=', $slug)->first();
            // Check if the game exists
            if (!$game) {
                // If the game is not found, return a 404 error response
                return response()->json(['error' => 'Game not found'], 404);
            }
            // Check if the authenticated user is the author of the game
            if (auth()->user()->id !== $game->auteur) {
                // If the user is not the author, return a 403 forbidden error response
                return response()->json(['status' => 'forbidden', 'message' => 'You are not the game author'], 403);
            }
            $game->titre = $request->titre;
            $game->description = $request->description ? $request->description : $game->description;
            // Save the changes to the database
            $game->save();
            return response()->json(['status' => "succès"], 200);
        } catch (\Throwable $th) {
            // If an error occurs during the process, return an error response with the error message
            return response()->json(['status' => 'error', 'message' => $th->getMessage()]);
        }
    }






    //////////////////// delete game with all vesions and scores
    public function Delete_Game($slug)
    {
        try {
            // Find the game bu slug
            $game = Game::where('slug', '=', $slug)->first();
            // Check if the game exists
            if (!$game) {
                // If the game is not found, return a 404 error response
                return response()->json(['error' => 'Game not found'], 404);
            }
            // Check if the authenticated user is the author of the game
            if (auth()->user()->id !== $game->auteur) {
                // If the user is not the author, return a 403 forbidden error response
                return response()->json(['status' => 'forbidden', 'message' => 'You are not the game autho'], 403);
            }
            // Find all versions of the game and get their IDs
            $version_Game = GameVersion::where("game_id", $game->id)->pluck('id')->toArray();
            // Loop through each version and delete associated scores
            foreach ($version_Game as $id) {
                Score::where('version_jeu_id', '=', $id)->delete();
            }
            // Delete all versions of the game
            GameVersion::where("game_id", $game->id)->delete();
            $game->delete();
            // Return a success response with HTTP status code 204
            return response()->json(['status' => 'succes'], 204);
        } catch (\Throwable $th) {
            // If an error occurs during the process, return an error response with the error message
            return response()->json(['status' => "error", 'message' => $th->getMessage()]);
        }
    }




    public function GameScores($slug)
    {

        // Get game ID by slug
        $gameId = Game::where('slug', $slug)->value('id');
        if (!$gameId) {
            return response()->json(['error' => 'Game not found'], 404);
        }
        // Get highest scores 
        $highestScores = Score::select(
            'users.name as username',
            DB::raw('MAX(scores.score) as score'),
            DB::raw('MAX(scores.created_at) as horodatage')
        )
            ->join('gameversions', 'scores.version_jeu_id', '=', 'gameversions.id')
            ->join('users', 'scores.user_id', '=', 'users.id')
            ->where('gameversions.game_id', $gameId)
            ->where('users.blocked', '=', 0)
            ->groupBy('users.name')
            ->orderBy('score', 'desc')
            ->get();
        $response = [
            'scores' => $highestScores,
        ];
        return response()->json($response, 200);
    }




    //////////// add score for user 
    public function AddScore(Request $request, $slug)
    {

        /////// add  validation 
        $validator = Validator::make($request->all(), [
            'score' => 'required|integer',

        ]);
        if ($validator->fails()) {
            $violations = [];
            foreach ($validator->errors()->messages() as $field => $messages) {
                $violations[$field] = [
                    'message' => $messages[0],
                ];
            }

            return response()->json([
                'status' => 'invalid',
                'message' => 'Le corps de la demande n\'est pas valide.',
                'violations' => $violations,
            ], 400);
        }

        // Find the game by slug
        $game = Game::where('slug', $slug)->first();

        if (!$game) {
            return response()->json(['status' => 'invalid', 'message' => 'Game not found'], 404);
        }

        // Get the latest game version
        $latestVersion = GameVersion::where('game_id', $game->id)->latest()->first();

        // Create a new score
        $score = new Score;
        $score->score = $request->score;
        $score->version_jeu_id = $latestVersion->id;
        $score->user_id = auth()->user()->id;

        try {
            $score->save();

            return response()->json(['status' => 'success'], 201);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Failed to save score'], 500);
        }
    }
}
