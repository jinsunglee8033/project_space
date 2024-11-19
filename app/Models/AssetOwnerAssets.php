<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

//use App\Models\Concerns\UuidTrait;

class AssetOwnerAssets extends Model
{
    use HasFactory;

    protected $table = 'asset_owner_assets';

    protected $fillable = [
        'id',
        'order_no',
        'asset_name',
        'kiss_nails',
        'kiss_lashes',
        'kiss_hair',
        'impress_nails',
        'impress_lashes',
        'joah',
        'color_care',
        'kiss_mass_market',
        'kiss_international',
        'retailer_support',
        'kiss_beauty_supply',
        'falscara',
        'myedit',
        'meamora',
        'beautify_tips',
    ];

    protected $primaryKey = 'id';
    protected $keyType = 'int';
    public $incrementing = true;

//    public function campaigns(){
//        return $this->hasMany('App\Models\CampaignBrands');
//    }

}
