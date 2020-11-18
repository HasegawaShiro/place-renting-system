<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\UserAuth as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $primaryKey = 'user_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username',
        'password',
        'name',
        'phone',
        'email',
        'user_disabled',
        'util_id',
        'remarks',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'user_disabled' => 'boolean',
        'util_id' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    public function util(){
        return $this->belongsTo('App\Models\Util', 'util_id', 'util_id');
    }

    public function schedules() {
        return $this->hasMany('App\Models\Schedule', 'user_id', 'user_id');
    }

    public function announcements() {
        return $this->hasMany('App\Models\Announcement', 'user_id', 'user_id');
    }
}
