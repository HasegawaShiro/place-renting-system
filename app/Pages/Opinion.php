<?php
namespace App\Pages;

use App\Models\Opinion as _MODEL;

use App\Utils\OpinionUtil as _UTIL;
use App\Utils\SessionUtil;

class Opinion {
    public static function fields() {
        return [
            'add' => [
                'opinion_title' => [
                    'required',
                    'string',
                    'max:200'
                ],
                'opinion_content' => [
                    'required',
                    'string',
                    'max:3000'
                ],
                'opinion_name' => [
                    'required',
                    'string',
                    'max:200'
                ],
                'opinion_email' => [
                    'required',
                    'string',
                    'email:rfc'
                ],
                'opinion_phone' => [
                    'required',
                    'string',
                    'regex:/^[\-\+\#0-9]*$/'
                ],
                'opinion_finish' => [
                    'required',
                    'boolean',
                ],
            ],
            'attributes' => [
                'opinion_title' => '主題',
                'opinion_content' => '內容',
                'opinion_name' => '留言人姓名',
                'opinion_email' => '聯絡信箱',
                'opinion_phone' => '聯絡電話',
                'opinion_finish' => '狀態',
            ]
        ];
    }

    public static function permission($status, $id = null) {
        $pass = true;

        $auth = SessionUtil::getLoginUser();
        if($auth['id'] !== 1) $pass = false;

        return $pass;
    }

    public static function beforeValidation(Array &$data, Array &$rules, Array &$messages, String $status) {
        if($status == 'add') {
            $data["opinion_finish"] = false;
        }
    }
}
