<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class ProjectTaskIndex extends Model
{
    use HasFactory;

    protected $table = 'project_task_index';

    protected $fillable = [
        'id',
        'project_id',
        'author_id',
        'type',
        'status',
        'revision_reason',
        'revision_reason_note',
        'due_date',
        'updated_at',
        'created_at'
    ];

    protected $primaryKey = 'id';
    protected $keyType = 'int';
    public $incrementing = true;

}
