<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class SubNpdDesignRequestIndex extends Model
{
    use HasFactory;

    protected $table = 'sub_npd_design_request_index';

    protected $fillable = [
        'id',
        'task_id',
        'author_id',
        'request_type',
        'status',
        'revision_reason',
        'revision_reason_note',
        'decline_reason',
        'decline_reason_note',
        'updated_at',
        'created_at'
    ];

    protected $primaryKey = 'id';
    protected $keyType = 'int';
    public $incrementing = true;

}
