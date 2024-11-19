<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class TaskTypeQraRequest extends Model
{
    use HasFactory;

    protected $table = 'task_type_qra_request';

    protected $fillable = [
        'id',
        'author_id',
        'type',
        'task_id',
        'request_type',
        'version',
        'material_number',
        'vendor',
        'target_region',
        'updated_at',
        'created_at',
    ];

    protected $primaryKey = 'task_id';
    protected $keyType = 'int';

}
