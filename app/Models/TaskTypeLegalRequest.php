<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class TaskTypeLegalRequest extends Model
{
    use HasFactory;

    protected $table = 'task_type_legal_request';

    protected $fillable = [
        'id',
        'author_id',
        'type',
        'request',
        'priority',
        'task_id',
        'updated_at',
        'created_at',
    ];

    protected $primaryKey = 'task_id';
    protected $keyType = 'int';

}
