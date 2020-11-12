<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Util extends Model
{
    use HasFactory;

    protected $primaryKey = 'util_id';

    protected $fillable = [
        'util_code',
        'util_name',
        'util_disabled',
        'remarks',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'util_disabled' => 'boolean',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    protected $editable = [
        'util_name',
        'remarks',
        'updated_by',
    ];
}
