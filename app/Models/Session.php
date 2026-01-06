<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    /** @use HasFactory<\Database\Factories\SessionFactory> */
    use HasFactory;
    protected $table = 'sessions';
    protected $keyType = 'string';
    protected $primaryKey = 'id';
    protected $incrementing = false;
    protected $fillable = [
        'title',
        'date',
        'description',
        'material',
        'council_id',
    ];

    public function council()
    {
        return $this->belongsTo(Council::class);
    }
    public function attendance()
    {
        return $this->hasMany(Attendance::class);
    }
}
