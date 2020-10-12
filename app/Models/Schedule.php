<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $primaryKey = 'schedule_id';

    protected $fillable = [
        'place_id',
        'schedule_title',
        'schedule_date',
        'schedule_from',
        'schedule_to',
        'schedule_type',
        'schedule_content',
        'user_id',
        'schedule_contact',
        'schedule_url',
        'schedule_repeat',
        'schedule_options',
        'schedule_regitrant',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'place_id' => 'integer',
        'schedule_date' => 'date',
        'schedule_from' => 'time',
        'schedule_to' => 'time',
        'user_id' => 'integer',
        'schedule_repeat' => 'boolean',
        'schedule_options' => 'array',
        'schedule_regitrant' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer'
    ];

}
