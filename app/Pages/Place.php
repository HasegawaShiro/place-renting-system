<?php
namespace App\Pages;

use Illuminate\Support\Facades\Hash;

use App\Models\Place as _MODEL;
use App\Models\Util;

use App\Utils\PlaceUtil as _UTIL;
use App\Utils\SessionUtil;
use App\Utils\ValidateUtil;

class Place {
    public static function fields() {
        return [
            'add' => [
                'place_code' => [
                    'required',
                    'string',
                    'unique:places',
                    'regex:/^[A-Z0-9]*$/i',
                    'max:200',
                ],
                'place_name' => [
                    'required',
                    'string',
                    'max:200',
                ],
                'place_disabled' => [
                    'required',
                    'boolean'
                ],
                'remarks' => [
                    'nullable',
                    'string',
                    'max:200',
                ],
            ],
            'edit' => [
                'place_code' => [
                    'required',
                    'string',
                ],
                'place_name' => [
                    'required',
                    'string',
                    'max:200',
                ],
                'place_disabled' => [
                    'required',
                    'boolean'
                ],
                'remarks' => [
                    'nullable',
                    'string',
                    'max:200',
                ],
            ],
            'attributes' => [
                'place_code' => '場地代碼',
                'place_name' => '場地名稱',
                'place_disabled' => '停用',
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
        $origin = _MODEL::find($data['place_id']);
        $toCheck = [
            $origin->schedules,
        ];

        foreach ($toCheck as $i) {
            if(!$i->isEmpty()) {
                $pass = false;
                array_push($result["messages"], 'data-is-referenced');
            }
        }

        return $pass;
    }
}
