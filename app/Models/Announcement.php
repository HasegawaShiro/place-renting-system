<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Announcement extends Model
{
    use HasFactory;

    protected $primaryKey = 'announcement_id';

    protected $fillable = [
        'announcement_title',
        'announcement_type',
        'announcement_date',
        'announcement_content',
        'user_id',
        'announcement_file',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    protected $editable = [
        'announcement_title',
        'announcement_type',
        'announcement_date',
        'announcement_content',
        'user_id',
        'announcement_file',
        'updated_by',
    ];

    public function user(){
        return $this->belongsTo('App\Models\User', 'user_id', 'user_id');
    }
}
