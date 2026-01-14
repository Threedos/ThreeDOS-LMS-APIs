<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    //
    use HasUuids;
       public $incrementing = false;
    public $keyType = 'string';
    protected $fillable = [
        'title',
        'description',
        'due_date',
        'status',
        'CouncilSession_id',
        'council_id',
    ];

    public function council()
    {
        return $this->belongsTo(Council::class);
    }
}
