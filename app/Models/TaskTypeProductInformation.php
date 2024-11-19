<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class TaskTypeProductInformation extends Model
{
    use HasFactory;

    protected $table = 'task_type_product_information';

    protected $fillable = [
        'id',
        'author_id',
        'type',
        'product_name',
        'product_line',
        'total_sku_count',
        'category',
        'segment',
        'product_dimension',
        'claim_weight',
        'weight_unit',
        'components',
        'what_it_is',
        'features',
        'marketing_claim',
        'applications',
        'sustainability',
        'if_others',
        'distribution',
        'if_others_distribution',
        'task_id',
        'updated_at',
        'created_at'
    ];

    protected $primaryKey = 'task_id';
    protected $keyType = 'int';

}
