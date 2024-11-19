<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class TaskTypeDisplayRequest extends Model
{
    use HasFactory;

    protected $table = 'task_type_display_request';

    protected $fillable = [
        'id',
        'author_id',
        'type',
        'request_type',
        'show_type',
        'show_location',
        'product_category',
//        'priority',
        'due_date',
        'due_date_urgent',
        'due_date_revision',
        'revision_cnt',
        'account',
        'specify_account',
        'display_style',
        'specify_display_style',
        'display_type',
        'additional_information',
        'display',
        'total_display_qty',
        'display_budget_per_ea',
        'display_budget_code',
        'display_ready_date',
        'assignee',
        'task_category',
        'task_id',
        'created_at',
        'updated_at'
    ];

    protected $primaryKey = 'task_id';
    protected $keyType = 'int';

    public function task_creator()
    {
        return $this->belongsTo(User::class, 'author_id', 'id');
    }

    public function assignee_obj()
    {
        return $this->belongsTo(User::class, 'assignee', 'id');
    }

}
