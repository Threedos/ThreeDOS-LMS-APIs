<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class TaskSubmission extends Model
{
    //
    use HasUuids;
    protected $table = 'task_submissions';



    public $incrementing = false; //    auto-increment id

    public $keyType = 'string';
    protected $fillable = [
        'task_id',
        'user_id',
        'file',
        'status',
        'grade',
        'comment',
        // 'council_id',

    ];

    public function task()
    {
        return $this->belongsTo(Task::class,'task_id','id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // public function council()
    // {
    //     return $this->belongsTo(Council::class);
    // }
}
