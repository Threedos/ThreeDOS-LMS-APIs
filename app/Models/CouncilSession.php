<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CouncilSession extends Model
{
    /** @use HasFactory<\Database\Factories\SessionFactory> */
    use HasFactory;
    protected $table = 'Council_sessions';
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
