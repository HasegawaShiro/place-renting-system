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
        'schedule_repeat_days',
        'schedule_end_at',
        'schedule_end_times',
        'schedule_registrant',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'place_id' => 'integer',
        'schedule_date' => 'datetime:Y-m-d',
        'schedule_from' => 'datetime:H:i',
        'schedule_to' => 'datetime:H:i',
        'user_id' => 'integer',
        'schedule_repeat' => 'boolean',
        'schedule_repeat_days' => 'integer',
        'schedule_end_at' => 'datetime:Y-m-d',
        'schedule_end_times' => 'integer',
        'schedule_registrant' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer'
    ];

}
