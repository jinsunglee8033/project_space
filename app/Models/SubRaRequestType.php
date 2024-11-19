<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class SubRaRequestType extends Model
{
    use HasFactory;

    protected $table = 'sub_ra_request_type';

    protected $fillable = [
        'id',
        'author_id',
        'type',
        'ra_request_type_id',
        'due_date',
        'due_date_revision',
        'revision_cnt',
        'assignee',
        'vendor_code',
        'vendor_name',
        'product_type',
        'product_form',
        'if_other_product_form',
        'area_of_application',
        'if_other_area_of_application',
        'fragrance',
        'if_other_fragrance',
        'compliant_regions',
        'registration_number',
        'formula',
        'ra_remarks',
        'formula_review',
        'registration_due_date',
        'market',
        'bulk_vendor_code',
        'bulk_vendor_name',
        'filling_vendor_code',
        'filling_vendor_name',
        'packaging_vendor_code',
        'packaging_vendor_name',
        'on_going_registrations',
        'due_date_registration',
        'cpnp_stage',
        'updated_at',
        'created_at',
    ];

    protected $primaryKey = 'ra_request_type_id';
    protected $keyType = 'int';

}
