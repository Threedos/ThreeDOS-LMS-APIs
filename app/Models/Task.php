<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Task extends Model
{
    //
    use HasUuids, HasFactory;
    public $incrementing = false;
    public $keyType = 'string';
    protected $fillable = [
        'title',
        'description',
        'due_date',
        'status',
        'council_session_id',
        // 'council_id',
    ];
    //to be enhance why council_id when i have session id which is related to council_id
    public function councilSession()
    {
        return $this->belongsTo(CouncilSession::class);
    }
}
