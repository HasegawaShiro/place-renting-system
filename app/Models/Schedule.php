<?php

namespace App\Models;

use App\Utils\ScheduleUtil;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
        'schedule_end',
        'schedule_end_at',
        'schedule_end_times',
        'schedule_registrant',
        'repeat_id',
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
        'repeat_id' => 'integer',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
        'created_by' => 'integer',
        'updated_by' => 'integer'
    ];

    protected $editable = [
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
        'schedule_end',
        'schedule_end_at',
        'schedule_end_times',
        'schedule_registrant',
        'repeat_id',
        'updated_by',
    ];

    public function user() {
        return $this->belongsTo('App\Models\User', 'user_id', 'user_id');
    }

    public function util() {
        return $this->hasOneThrough(
            'App\Models\Util',
            'App\Models\User',
            'user_id',
            'util_id',
            'user_id',
            'user_id'
        );
    }

    public function place() {
        return $this->belongsTo('App\Models\Place', 'place_id', 'place_id');
    }

    public static function boot(){
        parent::boot();

        self::saving(function(&$schedule) {
            $validator = ScheduleUtil::validateFromTo($schedule->toArray());
            if(!$validator["available"]){
                $schedule->fails();
                $schedule->setError($validator);
            }

            return $validator["available"];
        });
    }
}
