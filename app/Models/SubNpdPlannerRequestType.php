<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class SubNpdPlannerRequestType extends Model
{
    use HasFactory;

    protected $table = 'sub_npd_planner_request_type';

    protected $fillable = [
        'id',
        'author_id',
        'type',
        'npd_planner_request_type_id',
        'request_type',
        'request_group',
        'assignee',
        'due_date_revision',
        'revision_cnt',
        'revision_reason',
        'project_code',
        'due_date',
        'due_date_upload',
        'uploaded_date',
        'uploaded_user',
        'target_door_number',
        'ny_target_receiving_date',
        'la_target_receiving_date',
        'ny_planned_launch_date',
        'la_planned_launch_date',
        'nsp',
        'srp',
        'sales_channel',
        'if_others_sales_channel',
        'expected_reorder',
        'expected_sales',
        'benchmark_item',
        'actual_sales',
        'display_plan',
        'if_others_display_plan',
        'display_type',
        'penetration_type',
        'if_others_penetration_type',
        'tester',
        'promotion_items',
        'if_others_promotion_items',
        'return_plan',
        'return_plan_description',
        'purpose',
        'promotion_conditions',
        'presale_start_date',
        'presale_end_date',
        'promotion_start_date',
        'promotion_end_date',
        'presale_initial_shipping_start_date',
        'update_type',
        'revised_target_door_number',
        'revised_ny_receiving_date',
        'revised_la_receiving_date',
        'revised_ny_launch_date',
        'revised_la_launch_date',
        'change_request_reason',
        'change_request_detail',
        'updated_at',
        'created_at'
    ];

    protected $primaryKey = 'npd_planner_request_type_id';
    protected $keyType = 'int';

    public function assignee_obj()
    {
        return $this->belongsTo(User::class, 'assignee', 'id');
    }

    public function uploaded_user_obj()
    {
        return $this->belongsTo(User::class, 'uploaded_user', 'id');
    }

}
