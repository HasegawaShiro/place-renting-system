<?php
namespace App\Pages;

use App\Models\Util as _MODEL;

use App\Utils\SessionUtil;

class Util {
    public static function fields() {
        return [
            'add' => [
                'util_code' => [
                    'required',
                    'string',
                    'max:200',
                    'unique:utils',
                    'regex:/^[A-Z0-9]*$/i',
                ],
                'util_name' => [
                    'required',
                    'string',
                    'max:200'
                ],
                'util_disabled' => [
                    'required',
                    'boolean',
                ],
                'remarks' => [
                    'nullable',
                    'string',
                    'max:200',
                ],
            ],
            'edit' => [
                'util_code' => [
                    'required',
                    'string',
                ],
                'util_name' => [
                    'required',
                    'string',
                    'max:200'
                ],
                'util_disabled' => [
                    'required',
                    'boolean',
                ],
                'remarks' => [
                    'nullable',
                    'string',
                    'max:200',
                ],
            ],
            'attributes' => [
                'util_code' => '所屬單位代碼',
                'util_name' => '所屬單位名稱',
                'util_disabled' => '停用',
                'remarks' => '備註',
            ]
        ];
    }

    public static function permission($status, $id = null) {
        $pass = true;

        $auth = SessionUtil::getLoginUser();
        if($auth['id'] !== 1) $pass = false;

        return $pass;
    }

    public static function beforeDelete(Array $data, Array &$result) {
        $pass = true;
        if($data['util_id'] == 1) {
            $pass = false;
            array_push($result['messages'], "您不能刪除 ".$data['util_name']);
        } else {
            $origin = _MODEL::find($data['util_id']);
            $toCheck = [
                $origin->users,
                $origin->schedules,
                $origin->announcements,
            ];

            foreach ($toCheck as $i) {
                if(!$i->isEmpty()) {
                    $pass = false;
                    array_push($result["messages"], 'data-is-referenced');
                }
            }
        }

        return $pass;
    }
}
