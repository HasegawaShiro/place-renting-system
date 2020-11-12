<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Place extends Model
{
    use HasFactory;

    protected $primaryKey = 'place_id';

    protected $fillable = [
        'place_code',
        'place_name',
        'place_disabled',
        'remarks',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'place_disabled' => 'boolean',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    protected $editable = [
        'place_name',
        'place_disabled',
        'remarks',
        'updated_by',
    ];
}
