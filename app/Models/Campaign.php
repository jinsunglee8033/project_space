<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;

//use App\Models\Concerns\UuidTrait;

class Campaign extends Model
{
    use HasFactory;

    protected $table = 'campaign_item';

    protected $fillable = [
        'id',
        'name',
        'campaign_brand',
        'promotion',
        'date_from',
//        'date_to',
        'primary_message',
        'secondary_message',
        'products_featured',
        'author_name',
        'author_id',
        'author_team',
        'date_created',
        'status',
        'retailer',
        'email_brand',
        'asset_type',
        'campaign_notes',
        'type',
//        'assignee',
        'updated_at',
        'created_at'
    ];

    protected $primaryKey = 'id';
    protected $keyType = 'int';
    public $incrementing = true;

    public function getCreatedAtFormattedAttribute()
    {
        return Carbon::parse($this->attributes['created_at'])->format('d, M Y H:i:s');
    }

    public function getUpdatedAtFormattedAttribute()
    {
        return Carbon::parse($this->attributes['updated_at'])->format('d, M Y H:i:s');
    }

    public function brands()
    {
//        return $this->belongsTo('App\Models\CampaignBrands', 'campaign_brand', 'id');
        return $this->belongsTo(CampaignBrands::class, 'campaign_brand', 'id');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id', 'id');
    }

    public function asset_email_blasts()
    {
        return $this->hasMany(CampaignTypeEmailBlast::class, 'id', 'id');
    }

    public function asset_attachments()
    {
        return $this->belongsTo(CampaignTypeAssetAttachments::class, 'id', 'id');
    }

    public function campaign_type_email_blast()
    {
        return $this->belongsTo(CampaignTypeEmailBlast::class, 'id','id');
    }

}
