<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
class CouncilSession extends Model
{
    /** @use HasFactory<\Database\Factories\CouncilSessionFactory> */
    use HasFactory, HasUuids;
    protected $table = 'council_sessions';
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

    public function tasks()
    {
        return $this->hasMany(Task::class, 'council_session_id', 'id');
    }
    public function council()
    {
        return $this->belongsTo(Council::class);
    }
public function attendance()
{
    return $this->hasMany(Attendance::class, 'council_session_id', 'id');
}

}
