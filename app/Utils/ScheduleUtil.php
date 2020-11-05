<?php
namespace App\Utils;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

use App\Pages\Schedule as _PAGE;
use App\Models\Schedule as _MODEL;

class ScheduleUtil {
    public static function validateFromTo($data) {
        $place = $data['place_id'];
        $date = $data['schedule_date'];
        $from = $data['schedule_from'];
        $to = $data['schedule_to'];
        $repeatId = isset($data["repeat_id"]) ? $data["repeat_id"] : 0;
        $id = isset($data['schedule_id']) ? $data['schedule_id'] : 0;

        $query = _MODEL::whereDate('schedule_date', $date)
            ->where('place_id', $place)
            ->where('schedule_id', '<>', $id)
            ->where('repeat_id', '<>', $repeatId)
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

        return [
            'available' => $available,
            'data' => $data
        ];
    }

    public static function computeRepeatDate($data) {
        $result = [];
        $bin = sprintf("%'07b",$data['schedule_repeat_days']);
        $bin = str_split($bin);
        $mode = $data["schedule_end"];
        $d = Carbon::createFromFormat("Y-m-d",$data["schedule_date"]);
        if($mode == "at"){
            $endDate = Carbon::createFromFormat("Y-m-d", $data["schedule_end_at"]);
            $count = $d->diffInDays($endDate);
        } else {
            $count = $data["schedule_end_times"];
        }

        while ($count > 0) {
            $d->addDay();
            $weekDay = $d->dayOfWeekIso-1;
            if($bin[$weekDay] === "1"){
                array_push($result,$d->toDateString());
                $count--;
            }
        }

        return $result;
    }

    public static function computeRepeatId() {
        $max = ((int) _MODEL::query()->max('repeat_id')) + 1;

        return $max;
    }

    public static function weekdayChart(int $day = 0) {
        $result = [[],[],[],[],[],[],[]];

        if($day > 0 && $day < 128){
            $bin = sprintf("%'07b",$day);
            for ($j=0; $j < 7; $j++) {
                $result[$j] = substr($bin,$j,1) == "1";
            }
        } else {
            for ($i=0; $i < 128; $i++) {
                $bin = sprintf("%'07b",$i);
                for ($j=0; $j < 7; $j++) {
                    if(substr($bin,$j,1) == "1") {
                        array_push($result[$j], $i);
                    }
                }
            }
        }

        return $result;
    }

    public static function getDayRepeat(int $day) {
        $weekdays = Self::weekdayChart();
        $result = [];

        foreach ($weekdays as $days) {
            if(array_search($day, $days) !== false) {
                foreach ($days as $d) {
                    if(array_search($d, $result) === false) array_push($result, $d);
                }
            }
        }
        sort($result);

        return $result;
    }
}
