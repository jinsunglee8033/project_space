<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class SubMmRequestType extends Model
{
    use HasFactory;

    protected $table = 'sub_mm_request_type';

    protected $fillable = [
        'id',
        'author_id',
        'type',
        'mm_request_type_id',
        'materials',
        'priority',
        'due_date',
        'due_date_urgent',
        'urgent_reason',
        'urgent_detail',
        'due_date_revision',
        'revision_cnt',
        'assignee',
        'request_type',
        'set_up_plant',
        'remark',
        'updated_at',
        'created_at',
    ];

    protected $primaryKey = 'mm_request_type_id';
    protected $keyType = 'int';

    public function assignee_obj()
    {
        return $this->belongsTo(User::class, 'assignee', 'id');
    }

    public function author_obj()
    {
        return $this->belongsTo(User::class, 'author_id', 'id');
    }
}
