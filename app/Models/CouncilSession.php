<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
class CouncilSession extends Model
{
    /** @use HasFactory<\Database\Factories\SessionFactory> */
    use HasFactory,HasUuids;
    protected $table = 'CouncilSession';
    protected $keyType = 'string';
    // protected $primaryKey = 'id';
    public $incrementing = false;
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
        return $this->hasMany(Attendance::class, 'session_id', 'id');
    }
}
