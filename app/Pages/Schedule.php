<?php

namespace App\Pages;

use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

use App\Models\Place;
use App\Models\Schedule as _MODEL;

use App\Utils\DataUtil;
use App\Utils\SessionUtil;
use App\Utils\ScheduleUtil as _UTIL;
use App\Utils\ValidateUtil;

class Schedule
{
    public static function fields()
    {
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
                    Rule::in(['conference', 'activity', 'lesson', 'exam', 'lecture', 'camp', 'other']),
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
                    'nullable',
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
                'schedule_end' => [
                    'string',
                    Rule::in(['at', 'times'])
                ],
                'schedule_end_at' => [
                    'date_format:Y-m-d',
                ],
                'schedule_end_times' => [
                    'integer',
                    'max:24'
                ],
                'schedule_registrant' => [
                    'nullable',
                    'string',
                    'max:200'
                ],
                'schedule_document' => [
                    'nullable',
                    'file',
                    'max:20000'
                ],
                'repeat_id' => [
                    'nullable',
                    'integer'
                ],
            ],
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
                'schedule_document' => '核准開班公文',
                'schedule_repeat' => '是否重複',
                'schedule_repeat_days' => '重複方式',
                'schedule_end_at' => '重複結束日期',
                'schedule_end_times' => '重複結束次數',
                'schedule_registrant' => '登記人'
            ],
        ];
    }

    public static function messages()
    {
        return [
            // "from-to-unavailable" => '此時段已被 :user 預約： :title',
            "from-to-unavailable" => '此時段已被 :user 預約',
            "place-is-disabled" => '此場地不存在或已被停用',
            "schedule-data-expired" => '不可修改已過期資料',
            "date-unavailable" => ':date 同一時段已被 :user 預約',
            "repeat-days-zero" => '重複週期請至少選取一天',
            "repeat-times-over" => '重複週數會超出可預約日期限制'
        ];
    }

    public static function permission($status, $id = null)
    {
        $pass = true;
        if ($status == 'edit') {
            $origin = _MODEL::find($id);
            $auth = SessionUtil::getLoginUser();
            if ($auth['id'] !== 1 && $auth['id'] !== $origin->created_by) $pass = false;
        } else {
            $pass = Auth::check();
        }

        return $pass;
    }

    public static function getData($request, $id = null)
    {
        $query = _MODEL::with(['util', 'user', 'place']);
        $auth = empty(SessionUtil::getLoginUser()) ? ["id" => 0] : SessionUtil::getLoginUser();
        $models = [];
        $collect = collect([]);
        $filters = isset($request->filters) ? (array) json_decode($request->filters) : [];
        $orders = isset($request->orders) && !empty($request->orders) ? array_map(function ($arr) {
            return (array) json_decode($arr);
        }, $request->orders) : [
            [
                'name' => 'schedule_date',
                'method' => 'DESC'
            ],
            [
                'name' => 'schedule_from',
                'method' => 'ASC'
            ],
            [
                'name' => 'schedule_to',
                'method' => 'ASC'
            ],
        ];

        if (is_null($id)) {
            if (isset($filters["schedule_date_from"])) {
                $query->whereDate("schedule_date", '>=', $filters["schedule_date_from"]);
            }
            if (isset($filters["schedule_date_to"])) {
                $query->whereDate("schedule_date", '<=', $filters["schedule_date_to"]);
            }
            if (isset($filters["user_id"])) {
                $query->where("user_id", $filters["user_id"]);
            }
            if (isset($filters["place_id"])) {
                $query->where("place_id", $filters["place_id"]);
            }
            if (isset($filters["schedule_type"])) {
                $query->where("schedule_type", $filters["schedule_type"]);
            }
            if (isset($filters["util_id"])) {
                $query->with(['util' => function ($q) use ($filters) {
                    $q->where('utils.util_id', $filters["util_id"]);
                }]);
            }

            foreach ($orders as $order) {
                if (isset($order["from"]) && !is_null($order["from"])) {
                    $query->with([$order['from'] => function ($q) use ($order) {
                        $q->orderBy("{$order['from']}s.{$order['name']}", $order['method']);
                    }]);
                } else {
                    $query->orderBy($order["name"], $order['method']);
                }
            }
            $models = $query->get();
        } else {
            array_push($models, $query->where('schedule_id', $id));
        }
        $models = $query->get();

        $today = Carbon::today()->timestamp;
        $repeated = [];
        foreach ($models as $model) {
            try {
                $schedule = $model->toArray();

                $schedule["user_name"] = $model->user->name;
                $schedule["phone"] = $model->user->phone;
                $schedule["email"] = $model->user->email;
                $schedule["util_name"] = $model->util->util_name;
                $schedule["util_id"] = $model->util->util_id;
                $schedule["place_name"] = $model->place->place_name;
                $schedule["place_color"] = $model->place->place_color;
                $schedule["place_disabled"] = $model->place->place_disabled;

                if (!is_null($schedule["repeat_id"]) && $schedule["schedule_repeat"]) {
                    if (array_search($schedule["repeat_id"], $repeated) === false) {
                        array_push($repeated, $schedule["repeat_id"]);
                    } else {
                        $schedule["showOnList"] = false;
                    }

                    $firstRepeat = self::getRepeatIdFirst($model, true, "model")->toArray();
                    if ($firstRepeat["schedule_id"] != $schedule["schedule_id"]) {
                        $schedule["repeat_first"] = $firstRepeat;
                    }
                }

                if (($auth["id"] === 1 || $auth["id"] === $schedule["created_by"]) && strtotime($schedule["schedule_date"]) >= $today) {
                    $schedule["editable"] = true;
                    $schedule["deletable"] = true;
                } else {
                    $schedule["editable"] = false;
                    $schedule["deletable"] = false;
                }

                $collect->add($schedule);
            } catch (\Throwable $th) {
            }
        }
        return $collect->toArray();
    }

    public static function getFile($id, $field, $filename)
    {
        $table = strtolower(_MODEL::getModelName());
        $id = self::getRepeatIdFirst($id);

        return storage_path("app/uploads/$table/$field/$id-$filename");
    }

    public static function getRepeatIdFirst($search, $editable = false, $getType = "id")
    {
        $id = 0;
        if (is_a($search, "App\\Models\\Schedule")) {
            $data = $search;
        } else if (is_string($search) || is_numeric($search)) {
            $data = _MODEL::find($search);
        }

        if ($data->schedule_repeat) {
            $today = Carbon::today()->toDateString();
            $auth = empty(SessionUtil::getLoginUser()) ? ["id" => 0] : SessionUtil::getLoginUser();
            $repeat = _MODEL::where('repeat_id', $data->repeat_id)->orderBy('schedule_date', 'ASC');
            if ($editable) {
                $repeat->whereDate("schedule_date", ">=", $today);
                if ($auth["id"] !== 1) $repeat->where("created_by", $auth["id"]);
            }
            $data = empty($repeat->first()) ? $data : $repeat->first();
            $id = $data->getKey();
        } else {
            $id = $data->getKey();
        }

        if ($getType === "id") {
            return $id;
        } else {
            return $data;
        }
    }

    public static function beforeValidation(array &$data, array &$rules, array &$messages, String $status)
    {
        $fields = self::fields();
        $today = Carbon::yesterday()->endOfDay()->toDateTimeString();
        $atLimit = Carbon::today()->addYear()->addDay()->toDateString();
        array_push($rules["schedule_to"], "after:{$data['schedule_from']}");
        array_push($rules["schedule_date"], "after:{$today}");
        array_push($rules["schedule_date"], "before:{$atLimit}");

        if ($data['schedule_repeat']) {
            array_push($rules["schedule_end"], "required");
            ValidateUtil::unsetRules($rules["schedule_repeat_days"], "between:0,127");
            ValidateUtil::setRules($rules["schedule_repeat_days"], "between:1,127");
            $messages["schedule_repeat_days.between"] = self::messages()['repeat-days-zero'];
            if (array_search($data['schedule_end'], ['at', 'times']) !== false) {
                $type = $data['schedule_end'];

                array_push($rules["schedule_end_$type"], "required");
                if ($type == 'at') {
                    array_push($rules["schedule_end_at"], "after:{$data['schedule_date']}");
                    array_push($rules["schedule_end_at"], "before:{$atLimit}");
                    array_push($rules["schedule_end_times"], "nullable");
                } else {
                    array_push($rules["schedule_end_at"], "nullable");
                    array_push($rules["schedule_end_times"], 'min:1');
                }
            }
        } else {
            array_push($rules["schedule_end_at"], "nullable");
            array_push($rules["schedule_end_times"], "nullable");
        }

        $fileEmpty = empty($data["schedule_document"]);
        if ($status == 'edit') {
            $originFile = _MODEL::find($data['schedule_id'])->schedule_document;
            if (!empty($originFile) && $originFile === $data['schedule_document']) {
                $fileEmpty = false;
                // unset($data['schedule_document']);
                ValidateUtil::unsetRules($rules["schedule_document"], 'file', 'max:20000');
            }
        }
        if (empty($data["schedule_url"]) && $fileEmpty) {
            ValidateUtil::setRules($rules["schedule_url"], "required");
            $messages["schedule_url.required"] = ":attribute 與 " . $fields['attributes']['schedule_document'] . " 請至少擇一填寫";
        }

        $messages["schedule_to.after"] = ":attribute 必須要晚於 " . $fields['attributes']['schedule_from'];
        $messages["schedule_date.after"] = ":attribute 必須要晚於今天日期";
    }

    public static function afterValidation(array &$data, array &$result, String $status)
    {
        $pass = true;
        if ($status == 'add') {
            $validateFromTo = _UTIL::validateFromTo($data);
        } else {
            $validateFromTo = _UTIL::validateFromTo($data, $data['schedule_id']);
            $origin = _MODEL::find($data['schedule_id']);
            $originalDate = strtotime($origin->schedule_date);
            $today = Carbon::today()->timestamp;
            if ($originalDate < $today) {
                array_push(
                    $result['messages'],
                    self::messages()['schedule-data-expired']
                );
                $pass = false;
            }
        }

        if (!$validateFromTo['available']) {
            array_push(
                $result['messages'],
                DataUtil::replaceKeysInMessage(self::messages()['from-to-unavailable'], [
                    ":user" => $validateFromTo['data']['name'],
                    ":title" => $validateFromTo['data']['schedule_title']
                ])
            );

            $pass = false;
        }

        if ($data['schedule_repeat'] && $data['schedule_end'] === "times") {
            $atLimit = Carbon::today()->addYear()->addDay()->timestamp;
            $dates = _UTIL::computeRepeatDate($data);
            $lastDate = array_pop($dates);
            if (strtotime($lastDate) > $atLimit) {
                array_push(
                    $result['messages'],
                    self::messages()['repeat-times-over']
                );
                $pass = false;
            }
        }

        $place = Place::find($data['place_id']);
        if (is_null($place) || $place->place_disabled) {
            array_push(
                $result['messages'],
                self::messages()['place-is-disabled']
            );
            $pass = false;
        }

        return $pass;
    }

    public static function afterSave(array &$data, array &$result, String $status, array $old = [])
    {
        $success = true;

        if (!empty($old) && (isset($data['repeat_edit']) && $data['repeat_edit'] == 'all')) {
            $oldRepeatId = $old["repeat_id"];
            if ($old["schedule_repeat"] && !is_null($oldRepeatId)) {
                $oldRepeatFirst = _MODEL::where('repeat_id', $oldRepeatId)->first();
                $id = $oldRepeatFirst->getKey();
                $filename = $oldRepeatFirst->schedule_document;
                $table = strtolower(_MODEL::getModelName());
                $oldFile = "uploads/$table/schedule_document/$id-$filename";
                $newFile = "uploads/$table/schedule_document/{$data['schedule_id']}-$filename";
                if ($id != $data['schedule_id'] && Storage::exists($oldFile)) {
                    Storage::move($oldFile, $newFile);
                }

                _MODEL::where('repeat_id', $oldRepeatId)
                    ->whereDate("schedule_date", ">", $data["schedule_date"])
                    ->delete();
            }
        }

        $repeatId = null;
        if ($data['schedule_repeat']) {
            $saveRepeat = function ($data, $repeatId) use (&$result, &$success) {
                $tempToSave = $data;
                $dates = _UTIL::computeRepeatDate($data);
                $user = SessionUtil::getLoginUser();
                foreach ($dates as $d) {
                    $tempToSave["schedule_date"] = $d;
                    $tempToSave["repeat_id"] = $repeatId;
                    $tempToSave["created_by"] = $user['id'];
                    $tempToSave["updated_by"] = $user['id'];

                    $created = _MODEL::create($tempToSave);
                    if ($created->isFailed()) {
                        $err = $created->getError();
                        array_push(
                            $result['messages'],
                            DataUtil::replaceKeysInMessage(self::messages()['date-unavailable'], [
                                ":date" => $err['data']['schedule_date'],
                                ":user" => $err['data']['name']
                            ])
                        );
                        $success = false;
                    }
                }
            };

            if ($status == 'add' || is_null($data["repeat_id"]) || $data["repeat_edit"] === "one") {
                $repeatId = _UTIL::computeRepeatId();
            } else {
                $repeatId = $data["repeat_id"];
            }

            $saveRepeat($data, $repeatId);
            $origin = _MODEL::find($data["schedule_id"]);
            $origin->repeat_id = $repeatId;
            $origin->save();
        }

        return $success;
    }

    public static function beforeDelete(array $data, array &$result, array $options)
    {
        $pass = true;

        $today = strtotime("today");
        $deleteDate = strtotime($data["schedule_date"]);

        if ($deleteDate < $today) {
            array_push($result['messages'], '無法刪除已過期資料');
            $pass = false;
        }

        if (isset($options['repeat-mode']) && $options['repeat-mode'] == 'all') {
            $models = _MODEL::where('repeat_id', $data['repeat_id'])->get();
            foreach ($models as $model) {
                $model->delete();
            }
        }

        return $pass;
    }
}
