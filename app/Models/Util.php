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
        'util_disabled',
        'remarks',
        'updated_by',
    ];

    public function users() {
        return $this->hasMany('App\Models\User', 'util_id', 'util_id');
    }

    public function announcements() {
        return $this->hasManyThrough(
            'App\Models\Announcement',
            'App\Models\User',
            'util_id',
            'user_id',
            'util_id',
            'user_id'
        );
    }

    public function schedules() {
        return $this->hasManyThrough(
            'App\Models\Schedule',
            'App\Models\User',
            'util_id',
            'user_id',
            'util_id',
            'user_id'
        );
    }
}
