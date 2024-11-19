<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

//use App\Models\Concerns\UuidTrait;

class FormQrCode extends Model
{
    use HasFactory;

    protected $table = 'form_qr_code';

    protected $fillable = [
        'id',
        'name',
        'email',
        'qr_code_for',
        'brand',
        'department',
        'link_to',
        'date_1',
        'date_2',
        'date_3',
        'information',
        'qr_code_image',
        'url_destination_link',
        'short_url',
        'status',
        'qr_created',
        'qr_created_at',
    ];

    protected $primaryKey = 'id';
    protected $keyType = 'int';
    public $incrementing = true;

//    public function campaigns(){
//        return $this->hasMany('App\Models\CampaignBrands');
//    }

    public function campaignTypeAssetAttachments()
    {
        return $this->belongsTo('App\Models\CampaignTypeAssetAttachments', 'qr_code_image');
    }

    public function formQrCode()
    {
        return $this->belongsTo('App\Models\FormQrCode', 'attachment_id');
    }

}
