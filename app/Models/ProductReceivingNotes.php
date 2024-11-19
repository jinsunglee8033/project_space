<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class ProductReceivingNotes extends Model
{
    use HasFactory;

    protected $table = 'product_receiving_notes';

    protected $fillable = [
        'id',
        'user_id',
        'product_receiving_type_id',
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
