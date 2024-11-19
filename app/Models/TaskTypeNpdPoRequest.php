<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskTypeNpdPoRequest extends Model
{
    use HasFactory;

    protected $table = 'task_type_npd_po_request';

    protected $fillable = [
        'id',
        'author_id',
        'buyer',
        'type',
        'request_detail',
        'priority',
        'due_date',
        'due_date_urgent',
        'due_date_revision',
        'revision_cnt',
        'urgent_reason',
        'source_list_completion',
        'price_set_up',
        'forecast_completion',
        'material',
        'total_sku_count',
        'set_up_plant',
        'vendor_code',
        'vendor_name',
        'second_vendor_code',
        'second_vendor_name',
        'est_ready_date',
        'po',
        'task_id',
        'updated_at',
        'created_at',
    ];

    protected $primaryKey = 'task_id';
    protected $keyType = 'int';

    public function task_requestor_obj()
    {
        return $this->belongsTo(User::class, 'author_id', 'id');
    }

    public function buyer_obj()
    {
        return $this->belongsTo(User::class, 'buyer', 'id');
    }

}
