<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Council extends Model
{
    use HasUuids;
    public $table = "councils";
    public $incrementing = false;
    public $keyType = 'string';
    protected $fillable = [
        'name',
        'description',
        'head_id',
        'instructor_id',
    ];

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function head()
    {
        return $this->belongsTo(User::class);
    }

    public function instructor()
    {
        return $this->belongsTo(User::class);
    }
}
