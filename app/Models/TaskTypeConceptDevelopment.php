<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class TaskTypeConceptDevelopment extends Model
{
    use HasFactory;

    protected $table = 'task_type_concept_development';

    protected $fillable = [
        'id',
        'author_id',
        'type',
        'benchmark',
        'due_date',
        'task_id',
        'updated_at',
        'created_at',
    ];

    protected $primaryKey = 'task_id';
    protected $keyType = 'int';

}
