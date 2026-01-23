<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamMember extends Model
{
    use HasFactory,HasUuids; 
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'team_id',
        'user_id',
        'rate',
        'role',
        'task'
    ];



    public function team()
    {
        return $this->belongsTo(Team::class);
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
