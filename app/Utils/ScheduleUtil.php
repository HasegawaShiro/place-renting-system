<?php
namespace App\Utils;

use App\Pages\Schedule as _PAGE;
use App\Models\Schedule as _MODEL;

class ScheduleUtil {
    public static function validateFromTo($data) {
        $place = $data['place_id'];
        $date = $data['schedule_date'];
        $from = $data['schedule_from'];
        $to = $data['schedule_to'];

        $query = _MODEL::where('schedule_date', $date)
            ->where('place_id', $place)
            ->where(function ($q) use ($from, $to) {
                $q->orWhere(function($q) use ($from, $to) {
                    $q->where('schedule_to', '>=', $from)
                    ->where('schedule_to', '<=', $to);
                })->orWhere(function($q) use ($from, $to) {
                    $q->where('schedule_from', '>=', $from)
                    ->where('schedule_from', '<=', $to);
                });
            })->get();
        $available = sizeof($query) === 0;
        if(!$available){
            $schedule = $query->first()->toArray();
            $user = $query->first()->user()->get()->first()->toArray();
            $data = array_merge($schedule, $user);
        }else{
            $data = [];
        }

        // dd($data);
        return [
            'available' => $available,
            'data' => $data
        ];
    }
}
