<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class NpdDesignRequestNotes extends Model
{
    use HasFactory;

    protected $table = 'npd_design_request_notes';

    protected $fillable = [
        'id',
        'user_id',
        'npd_design_request_type_id',
        'task_id',
        'project_id',
        'note',
        'created_at',
        'updated_at'
    ];

    public function users()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    protected $primaryKey = 'id';
    protected $keyType = 'int';
    public $incrementing = true;

}
