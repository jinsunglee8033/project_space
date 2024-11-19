<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class ProjectTypeTaskAttachments extends Model
{
    use HasFactory;

    protected $table = 'project_type_task_attachments';

    protected $fillable = [
        'project_id',
        'attachment_id',
        'task_id',
        'type',
        'author_id',
        'attachment',
        'file_ext',
        'file_type',
        'file_size',
        'updated_at',
        'created_at'
    ];

    protected $primaryKey = 'attachment_id';
    protected $keyType = 'int';
    public $incrementing = true;

    public function attachment()
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id', 'id');
    }


//    protected $primaryKey = 'attachment_id';
//    protected $keyType = 'int';
//    public $incrementing = true;

}
