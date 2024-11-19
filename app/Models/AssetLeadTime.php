<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

//use App\Models\Concerns\UuidTrait;

class AssetLeadTime extends Model
{
    use HasFactory;

    protected $table = 'asset_lead_time';

    protected $fillable = [
        'id',
        'order_no',
        'asset_name',
        'copywriter_assign',
        'copy',
        'copy_review',
        'creator_assign',
        'creative_work',
        'final_review',
        'development',
        'kdo',
        'time_to_spare',
        'total',
    ];

    protected $primaryKey = 'id';
    protected $keyType = 'int';
    public $incrementing = true;

//    public function campaigns(){
//        return $this->hasMany('App\Models\CampaignBrands');
//    }

}
