<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class TaskTypeProductReceiving extends Model
{
    use HasFactory;

    protected $table = 'task_type_product_receiving';

    protected $fillable = [
        'id',
        'author_id',
        'type',
        'po',
        'materials',
        'posting_date',
        'qir_status',
        'division',
        'qir_action',
        'vendor_code',
        'vendor_name',
        'cost_center',
        'location',
        'primary_contact',
        'related_team_contact',
        'year',
        'received_qty',
        'inspection_qty',
        'defect_qty',
        'blocked_qty',
        'blocked_rate',
        'batch',
        'item_net_cost',
        'defect_area',
        'defect_type',
        'defect_details',
        'defect_cost',
        'full_cost',
        'rework_cost',
        'rsr_id',
        'special_inspection_cost',
        'processing_date',
        'aging_days',
        'capa',
        'total_claim',
        'actual_cm_total',
        'claim_status',
        'override_authorized_by',
        'waived_amount',
        'settlement_total',
        'settlement_type',
        'task_id',
        'updated_at',
        'created_at'
    ];

    protected $primaryKey = 'task_id';
    protected $keyType = 'int';

}
