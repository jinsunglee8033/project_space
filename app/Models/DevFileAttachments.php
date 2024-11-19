<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class DevFileAttachments extends Model
{
    use HasFactory;

    protected $table = 'dev_file_attachments';

    protected $fillable = [
        'id',
        'attachment_id',
        'dev_id',
        'type',
        'author_id',
        'attachment',
        'file_ext',
        'file_type',
        'file_size',
        'date_created'
    ];

    protected $primaryKey = 'attachment_id';
    protected $keyType = 'int';
    public $incrementing = true;

    public function attachment()
    {
        return $this->belongsTo(Campaign::class, 'id', 'id');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id', 'id');
    }


//    protected $primaryKey = 'attachment_id';
//    protected $keyType = 'int';
//    public $incrementing = true;

}
