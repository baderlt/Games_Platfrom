<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\GameVersion;

class Score extends Model
{
    use HasFactory;
    protected $table='scores';

    protected $fillable = ['score', 'version_jeu_id', 'user_id'];

    public function version()
    {
        return $this->belongsTo(GameVersion::class, 'version_jeu_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
