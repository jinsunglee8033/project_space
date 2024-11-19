<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class TaskTypeMmRequest extends Model
{
    use HasFactory;

    protected $table = 'task_type_mm_request';

    protected $fillable = [
        'id',
        'author_id',
        'type',
        'task_id',
        'updated_at',
        'created_at',
    ];

    protected $primaryKey = 'task_id';
    protected $keyType = 'int';

    public function task_creator()
    {
        return $this->belongsTo(User::class, 'author_id', 'id');
    }

    public function assignee_obj()
    {
        return $this->belongsTo(User::class, 'assignee', 'id');
    }

}
