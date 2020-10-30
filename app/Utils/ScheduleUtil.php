<?php
namespace App\Utils;

use App\Pages\Schedule as _PAGE;
use App\Models\Schedule as _MODEL;

class ScheduleUtil {
    public static function validateFromTo($data, $id = 0) {
        $place = $data['place_id'];
        $date = $data['schedule_date'];
        $from = $data['schedule_from'];
        $to = $data['schedule_to'];

        $query = _MODEL::whereDate('schedule_date', $date)
            ->where('place_id', $place)
            ->where('schedule_id', '<>', $id)
            ->where(function ($q) use ($from, $to) {
                $q->orWhere(function($q) use ($from, $to) {
                    $q->whereTime('schedule_to', '>', $from)
                    ->whereTime('schedule_to', '<', $to);
                })->orWhere(function($q) use ($from, $to) {
                    $q->whereTime('schedule_from', '>', $from)
                    ->whereTime('schedule_from', '<', $to);
                })->orWhere(function($q) use ($from, $to) {
                    $q->whereTime('schedule_from', '>=', $from)
                    ->whereTime('schedule_from', '<=', $to)
                    ->whereTime('schedule_to', '>=', $from)
                    ->whereTime('schedule_to', '<=', $to);
                })->orWhere(function($q) use ($from, $to) {
                    $q->whereTime('schedule_from', '<=', $from)
                    ->whereTime('schedule_to', '>=', $from)
                    ->whereTime('schedule_from', '<=', $to)
                    ->whereTime('schedule_to', '>=', $to);
                });
            })->get();

            // dd($query);
            // dd($query->getQuery()->tosql(), $query->getQuery()->getBindings());
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
