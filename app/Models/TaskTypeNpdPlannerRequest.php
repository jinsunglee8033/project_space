<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskTypeNpdPlannerRequest extends Model
{
    use HasFactory;

    protected $table = 'task_type_npd_planner_request';

    protected $fillable = [
        'id',
        'author_id',
        'type',
        'task_id',
        'updated_at',
        'created_at',
//        'id',
//        'author_id',
//        'request_group',
//        'assignee',
//        'type',
//        'project_code',
//        'due_date_review',
//        'target_door_number',
//        'sales_channel',
//        'if_others_sales_channel',
//        'expected_reorder_max',
//        'expected_reorder_low',
//        'expected_reorder_avg',
//        'expected_sales',
//        'benchmark_item',
//        'actual_sales',
//        'display_plan',
//        'penetration_type',
//        'if_others_penetration_type',
//        'tester',
//        'promotion_items',
//        'if_others_promotion_items',
//        'return_plan',
//        'return_plan_description',
//        'due_date_upload',
//        'task_id',
//        'due_date_revision',
//        'revision_cnt',
//        'updated_at',
//        'created_at',
    ];

    protected $primaryKey = 'task_id';
    protected $keyType = 'int';

    public function task_requestor_obj()
    {
        return $this->belongsTo(User::class, 'author_id', 'id');
    }

//    public function assignee_obj()
//    {
//        return $this->belongsTo(User::class, 'assignee', 'id');
//    }

}
