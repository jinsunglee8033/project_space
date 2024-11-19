<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class SubQraRequestType extends Model
{
    use HasFactory;

    protected $table = 'sub_qra_request_type';

    protected $fillable = [
        'id',
        'author_id',
        'type',
        'qra_request_type_id',
        'version',
        'material_number',
        'vendor_code',
        'vendor_name',
        'target_region',
        'registration',
        'updated_at',
        'created_at',
    ];

    protected $primaryKey = 'qra_request_type_id';
    protected $keyType = 'int';

}
