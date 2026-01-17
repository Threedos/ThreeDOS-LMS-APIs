<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamMember extends Model
{
    /** @use HasFactory<\Database\Factories\TeamMemberFactory> */
    use HasFactory; 

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
