<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Council extends Model
{
    use HasUuids,HasFactory;
    public $table = "councils";
    public $incrementing = false;
    public $keyType = 'string';
    protected $fillable = [
        'name',
        'description',
      
    ];

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function users()
    {
        return $this->hasMany(User::class, 'council_id');
    }

    public function head()
    {
  return $this->hasOne(User::class)
        ->whereHas('role', function ($q) {
            $q->where('name', 'Head');
        });   
    }
  
}
