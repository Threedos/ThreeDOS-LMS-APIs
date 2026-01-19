<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    /** @use HasFactory<\Database\Factories\AttendanceFactory> */
    use HasFactory;

    protected $table = 'attendances';
    protected $keyType = 'string';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $fillable = [
        'user_id',
        'council_session_id',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function council_session()
    {
        return $this->belongsTo(CouncilSession::class, 'council_session_id', 'id');
    }
}
