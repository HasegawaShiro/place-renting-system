<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Opinion extends Model
{
    use HasFactory;

    protected $primaryKey = 'opinion_id';

    protected $fillable = [
        'opinion_id',
        'opinion_title',
        'opinion_content',
        'opinion_name',
        'opinion_email',
        'opinion_phone',
        'opinion_finish',
        'created_at',
        'update_at',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'opinion_finish' => 'boolean',
        'created_at' => 'datetime:Y-m-d'
    ];

    protected $editable = [
        'opinion_finish',
        'update_at'
    ];
}
