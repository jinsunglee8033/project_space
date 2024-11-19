<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;

//use App\Models\Concerns\UuidTrait;

class Project extends Model
{
    use HasFactory;

    protected $table = 'project';

    protected $fillable = [
        'id',
        'category',
        'team',
        'brand',
        'name',
        'description',
        'project_type',
        'project_year',
        'sku',
        'code',
        'target_date',
        'launch_date',
        'international_sales_plan',
        'sale_available_date',
        'author_id',
        'author_name',
        'status',
        'revision_reason',
        'revision_reason_note',
        'updated_at',
        'created_at'
    ];

    protected $primaryKey = 'id';
    protected $keyType = 'int';
    public $incrementing = true;

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id', 'id');
    }

}
