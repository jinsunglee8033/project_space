<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class TaskTypeProductBrief extends Model
{
    use HasFactory;

    protected $table = 'task_type_product_brief';

    protected $fillable = [
        'id',
        'author_id',
        'type',
        'product_name',
        'material_number',
        'total_sku_count',
        'target_receiving_date',
        'door',
        'nsp',
        'srp',
        'category',
        'sub_category',
        'franchise',
        'shade_names',
        'claim_weight',
        'testing_claims',
        'concept',
        'key',
        'product_format',
        'texture',
        'finish',
        'coverage',
        'must_ban',
        'highlights',
        'task_id',
        'updated_at',
        'created_at'
    ];

    protected $primaryKey = 'task_id';
    protected $keyType = 'int';

}
