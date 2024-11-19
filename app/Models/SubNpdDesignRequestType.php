<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class SubNpdDesignRequestType extends Model
{
    use HasFactory;

    protected $table = 'sub_npd_design_request_type';

    protected $fillable = [
        'id',
        'author_id',
        'type',
        'npd_design_request_type_id',
        'objective',
        'priority',
        'due_date',
        'due_date_urgent',
        'urgent_reason',
        'due_date_revision',
        'revision_cnt',
        'request_type',
        'design_group',
        'artwork_type',
        'scope',
        'sales_channel',
        'if_others_sales_channel',
        'target_audience',
        'head_copy',
        'reference',
        'material_number',
        'component_number',
        'assignee',
        'multiple_assignees',
        'revision_reason',
        'updated_at',
        'created_at',
    ];

    protected $primaryKey = 'npd_design_request_type_id';
    protected $keyType = 'int';

}
