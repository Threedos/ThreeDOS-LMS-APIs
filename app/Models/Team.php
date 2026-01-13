<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    /** @use HasFactory<\Database\Factories\TeamFactory> */
    use HasFactory,HasUuids;
    public $keyType ='string';
    public $incrementing =false;
    protected $fillable =[
                'team_number',
                'council_id',
                'task_link'
            ];


    public function council(){
        return $this->belongsTo(Council::class , 'council_id');
    }

    public function team_members(){
        return $this->hasMany(TeamMembers::class , 'team_id');
    }
}
