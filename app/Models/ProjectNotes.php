<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class ProjectNotes extends Model
{
    use HasFactory;

    protected $table = 'project_notes';

    protected $fillable = [
        'id',
        'user_id',
        'task_id',
        'type',
        'note',
        'updated_at',
        'created_at'
    ];

    public function users()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

//    protected $primaryKey = 'id';
//    protected $keyType = 'int';
//    public $incrementing = true;

}
