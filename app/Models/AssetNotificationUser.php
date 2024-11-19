<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

//use App\Models\Concerns\UuidTrait;

class AssetNotificationUser extends Model
{
    use HasFactory;

    protected $table = 'asset_notification_user';

    protected $fillable = [
        'asset_id',
        'user_id_list',
    ];

    protected $primaryKey = 'asset_id';
    protected $keyType = 'int';
//    public $incrementing = true;

//    public function campaigns(){
//        return $this->hasMany('App\Models\CampaignBrands');
//    }

}
