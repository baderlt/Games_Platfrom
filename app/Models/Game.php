<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\GameVersion;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

class Game extends Model
{
    use HasFactory;
    use SoftDeletes;
    
    protected $table='games';
    protected $fillable=['titre'];

   
    public function versions()
    {
        return $this->hasMany(GameVersion::class, 'game_id');
    }

    public function author()
    {
        return $this->hasOne(User::class,'id','auteur');
    }
}
