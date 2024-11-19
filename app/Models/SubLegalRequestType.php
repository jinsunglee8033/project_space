<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class SubLegalRequestType extends Model
{
    use HasFactory;

    protected $table = 'sub_legal_request_type';

    protected $fillable = [
        'id',
        'author_id',
        'type',
        'legal_request_type_id',
        'request_description',
        'trademark_owner',
        'description_of_goods',
        'request_category',
        'if_other_request_category',
        'priority',
        'vendor_code',
        'vendor_name',
        'vendor_location',
        'due_date',
        'due_date_urgent',
        'urgent_reason',
        'due_date_revision',
        'revision_cnt',
        'assignee',
        'legal_remarks',
        'target_region',
        'if_selected_others',
        'registration',
        'updated_at',
        'created_at',
    ];

    protected $primaryKey = 'legal_request_type_id';
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
