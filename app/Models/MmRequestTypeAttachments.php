<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class MmRequestTypeAttachments extends Model
{
    use HasFactory;

    protected $table = 'mm_request_type_attachments';

    protected $fillable = [
        'task_id',
        'attachment_id',
        'mm_request_type_id',
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
        return $this->belongsTo(SubQraRequestType::class, 'task_id', 'id');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id', 'id');
    }


//    protected $primaryKey = 'attachment_id';
//    protected $keyType = 'int';
//    public $incrementing = true;

}
