<?php
namespace App\Pages;

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
