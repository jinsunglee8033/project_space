<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class SubQraRequestIndex extends Model
{
    use HasFactory;

    protected $table = 'sub_qra_request_index';

    protected $fillable = [
        'id',
        'task_id',
        'author_id',
        'request_type',
        'status',
        'updated_at',
        'created_at'
    ];

    protected $primaryKey = 'id';
    protected $keyType = 'int';
    public $incrementing = true;

}
