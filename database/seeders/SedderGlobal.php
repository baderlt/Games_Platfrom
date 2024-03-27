<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SedderGlobal extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $admins=[
            ['name'=>"administrateur1",'password'=>Hash::make("bonjourunivers1!")],
            ['name'=>"administrateur2",'password'=>Hash::make("bonjourunivers2!")],
        ];
        DB::table('administrateurs')->insert($admins);
        $users=[
            ['name'=>'joueur1','password'=>Hash::make('bonjour le monde1 !')],
            ['name'=>'joueur2','password'=>Hash::make('bonjour le monde2 !')],
            ['name'=>'dev1','password'=>Hash::make('bonjourbyte1')],
            ['name'=>'dev2','password'=>Hash::make('bonjourbyte2')],
        ];
        DB::table('users')->insert($users);

        $games=[
            ["titre"=>"Jeu de démonstration 1",'description'=>'Ceci est le jeu de démonstration 1','vignette'=>"games/démonstration1/2/fighte2/miniature.png",'slug'=>'démonstration1',"auteur"=>3],
            ["titre"=>"Jeu de démonstration 2",'description'=>'Ceci est le jeu de démonstration 2','vignette'=>"games/démonstration1/2/fighte2/miniature.png",'slug'=>'démonstration2',"auteur"=>4],
        ];

        DB::table('games')->insert($games);
        
        $gameversions=[
            ["game_id"=>1,'path'=>''],
            ["game_id"=>1,'path'=>'demo-game-1-v2/'],
            ["game_id"=>2,'path'=>'demo-game-2-v1/'],
        ];

        DB::table('gameversions')->insert($gameversions);

        $scores=[
            ["user_id"=>1,'version_jeu_id'=>1,'score'=>10.0],
            ["user_id"=>1,'version_jeu_id'=>1,"score"=>15.0],
            ["user_id"=>1,'version_jeu_id'=>2,"score"=>12.0],
            ["user_id"=>2,'version_jeu_id'=>2,'score'=>20.0],
            ["user_id"=>2,'version_jeu_id'=>3,"score"=>30.0],
            ["user_id"=>3,'version_jeu_id'=>2,"score"=>1000.0],
            ["user_id"=>3,'version_jeu_id'=>2,'score'=>-300.0],
        ];

        DB::table('scores')->insert($scores);
    }
}
