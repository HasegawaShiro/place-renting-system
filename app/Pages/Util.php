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

        if($status == 'edit') {
            $auth = SessionUtil::getLoginUser();
            if($auth['id'] !== 1) $pass = false;
        } else if($status == 'edit') {
            $origin = _MODEL::find($id);
            $auth = SessionUtil::getLoginUser();
            if($auth['id'] !== 1 && $auth['id'] !== $origin->created_by) $pass = false;
        }

        return $pass;
    }

}
