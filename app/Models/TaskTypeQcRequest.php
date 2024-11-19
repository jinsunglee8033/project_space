<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class TaskTypeQcRequest extends Model
{
    use HasFactory;

    protected $table = 'task_type_qc_request';

    protected $fillable = [
        'id',
        'author_id',
        'type',
        'work_type',
        'ship_date',
        'qc_date',
        'po',
        'po_usd',
        'materials',
        'item_type',
        'vendor_code',
        'vendor_name',
        'country',
        'vendor_primary_contact_name',
        'vendor_primary_contact_email',
        'vendor_primary_contact_phone',
        'facility_address',
        'performed_by',
        'critical',
        'result',
        'decision',
        'qc_completed_date',
        'task_id',
        'updated_at',
        'created_at',
    ];

    protected $primaryKey = 'task_id';
    protected $keyType = 'int';

}
