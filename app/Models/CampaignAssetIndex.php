<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class CampaignAssetIndex extends Model
{
    use HasFactory;

    protected $table = 'campaign_asset_index';

    protected $fillable = [
        'id',
        'campaign_id',
        'author_id',
        'type',
        'team_to',
        'skip_creative',
        'status',
        'assignee',
        'assigned_at',
        'delay',
        'target_at',
        'start_at',
        'done_at',
        'copy_writer',
        'copy_assigned_at',
        'copy_delay',
        'copy_target_at',
        'copy_done_at',
        'decline_copy',
        'decline_creative',
        'decline_kec'
    ];

    protected $primaryKey = 'id';
    protected $keyType = 'int';
    public $incrementing = true;

}
