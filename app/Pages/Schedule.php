<?php
namespace App\Pages;

use Carbon\Carbon;
use Illuminate\Validation\Rule;

use App\Models\User;
use App\Models\Schedule as _MODEL;

use App\Utils\DataUtil;
use App\Utils\ScheduleUtil as _UTIL;

class Schedule {
    public static function fields() {
        return [
            'add' => [
                'place_id' => [
                    'required',
                    'integer',
                    'exists:App\Models\Place,place_id',
                ],
                'schedule_title' => [
                    'required',
                    'string',
                    'max:200',
                ],
                'schedule_date' => [
                    'required',
                    'date_format:Y-m-d',
                ],
                'schedule_from' => [
                    'required',
                    'date_format:H:i',
                ],
                'schedule_to' => [
                    'required',
                    'date_format:H:i',
                ],
                'schedule_type' => [
                    'required',
                    Rule::in(['conference','activity','lesson','exam','other']),
                ],
                'schedule_content' => [
                    'nullable',
                    'string',
                    'max:1000'
                ],
                'user_id' => [
                    'required',
                    'integer',
                    'exists:App\Models\User,user_id',
                ],
                'schedule_contact' => [
                    'required',
                    'string',
                    'max:200'
                ],
                'schedule_url' => [
                    'nullable',
                    'string',
                    'max:200'
                ],
                'schedule_repeat' => [
                    'required',
                    'boolean'
                ],
                'schedule_repeat_days' => [
                    'required',
                    'integer',
                    'between:0,127'
                ],
                'schedule_end_at' => [
                    'nullable',
                    'date_format:Y-m-d',
                    'after:today'
                ],
                'schedule_end_times' => [
                    'nullable',
                    'integer',
                    'min:0'
                ],
                'schedule_registrant' => [
                    'required',
                    'string',
                    'max:200'
                ],
            ],
            'edit' => [],
            'attributes' => [
                'place_id' => '場地',
                'schedule_title' => '主題',
                'schedule_date' => '日期',
                'schedule_from' => '開始時間',
                'schedule_to' => '結束時間',
                'schedule_type' => '借用型態',
                'schedule_content' => '內容',
                'user_id' => '承辦人',
                'schedule_contact' => '聯絡人',
                'schedule_url' => '相關網址',
                'schedule_repeat' => '是否重複',
                'schedule_repeat_days' => '重複方式',
                'schedule_end_at' => '重複結束日期',
                'schedule_end_times' => '重複結束次數',
                'schedule_registrant' => '登記人'
            ],
        ];
    }

    public static function messages() {
        return [
            // "from-to-unavailable" => '此時段已被 :user 預約： :title',
            "from-to-unavailable" => '此時段已被 :user 預約',
        ];
    }

    public static function getData($filters, $orders, $id = null) {
        $query = new _MODEL();
        $models = [];
        $collect = collect([]);
        $datas = [];
        $orders = empty($orders) ? ['schedule_date', 'schedule_from'] : $orders;

        if(is_null($id)) {
            if(isset($filters["schedule_date_from"])) {
                $query->whereDate("schedule_date", '>=', $filters["schedule_date_from"]);
            }
            if(isset($filters["schedule_date_to"])) {
                $query->whereDate("schedule_date", '<=', $filters["schedule_date_to"]);
            }
            if(isset($filters["user_id"])) {
                $query->where("user_id", $filters["user_id"]);
            }
            if(isset($filters["place_id"])) {
                $query->where("place_id", $filters["place_id"]);
            }
            if(isset($filters["schedule_type"])) {
                $query->where("schedule_type", $filters["schedule_type"]);
            }

            $models = $query->get();
        } else {
            array_push($models, $query::find($id));
        }

        foreach ($models as $model) {
            $schedule = $model->toArray();
            $user_MODEL = $model->user()->first();
            $user = $user_MODEL->toArray();
            $util = $user_MODEL->util()->first()->toArray();
            $place = $model->place()->first()->toArray();

            $schedule["user_name"] = $user["name"];
            $schedule["phone"] = $user["phone"];
            $schedule["email"] = $user["email"];
            $schedule["util_name"] = $util["util_name"];
            $schedule["util_id"] = $util["util_id"];
            $schedule["place_name"] = $place["place_name"];
            $collect->add($schedule);
        }

        if(is_null($id) && isset($filters["util_id"])) {
            dd($collect->where('util_id', $filters["util_id"])->all());
        }

        return $collect->toArray();
    }

    public static function beforeValidation(Array &$data, Array &$rules, Array &$messages, String $status) {
        $today = Carbon::yesterday()->hour(23)->minute(59)->toDateTimeString();
        array_push($rules["schedule_to"], "after:{$data['schedule_from']}");
        array_push($rules["schedule_date"], "after:{$today}");
        $messages["schedule_to.after"] = Schedule::fields()['attributes']['schedule_to']." 必須要晚於 ".Schedule::fields()['attributes']['schedule_from'];
        $messages["schedule_date.after"] = Schedule::fields()['attributes']['schedule_date']." 必須要晚於今天日期";
    }

    public static function afterValidation(Array &$data, Array &$result, String $status) {
        $pass = true;
        if($status == 'add'){
            $validateFromTo = _UTIL::validateFromTo($data);
            if(!$validateFromTo['available']) {
                array_push(
                    $result['messages'],
                    DataUtil::replaceKeysInMessage(Schedule::messages()['from-to-unavailable'], [
                        ":user" => $validateFromTo['data']['name'],
                        ":title" => $validateFromTo['data']['schedule_title']
                    ])
                );

                $pass = false;
            }
        }

        return $pass;
    }
}
