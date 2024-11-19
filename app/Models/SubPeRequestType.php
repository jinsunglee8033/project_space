<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class SubPeRequestType extends Model
{
    use HasFactory;

    protected $table = 'sub_pe_request_type';

    protected $fillable = [
        'id',
        'author_id',
        'type',
        'pe_request_type_id',
        'request_detail',
        'total_quantity',
        'item_number',
        'color_pattern',
        'tooling_budget_code',
        'due_date',
        'display_ready_date',
        'assignee',
        'mold_design_start_date',
        'mold_design_finish_date',
        'revision_cnt_ed',
        'revising_ed',
        'revision_cnt_md',
        'revising_md',
        'revision_cnt_cam',
        'revising_cam',
        'design_start_date',
        'design_finish_date',
        'sample_start_date',
        'sample_finish_date',
        'sample_type',
        'sample_quantity',
        'cam_start_date',
        'cam_finish_date',
        'machining_start_date',
        'machining_finish_date',
        'machining_cost',
        'due_date_revision',
        'revision_cnt',
        'request_category' ,
        'show_type',
        'show_location',
        'product_category',
        'display_type',
        'display_style',
        'specify_display_style',
        'display',
        'total_display_qty',
        'display_budget_per_ea',
        'display_budget_code',
        'account',
        'specify_account',
        'additional_information',
        'task_category',
        'kdc_delivery_due_date',
        'updated_at',
        'created_at',
    ];

    protected $primaryKey = 'pe_request_type_id';
    protected $keyType = 'int';

}
