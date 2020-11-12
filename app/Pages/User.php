<?php
namespace App\Pages;

use Illuminate\Support\Facades\Hash;

use App\Models\User as _MODEL;
use App\Models\Util;

use App\Utils\UserUtil as _UTIL;
use App\Utils\SessionUtil;
use App\Utils\ValidateUtil;

class User {
    public static function fields() {
        return [
            'add' => [
                'username' => [
                    'required',
                    'unique:users',
                    'string',
                    'max:20',
                    'regex:/^[A-Z0-9]*$/i'
                ],
                'password' => [
                    'required',
                    'string',
                    'confirmed',
                    'max:20'
                ],
                'name' => [
                    'required',
                    'string',
                    'max:200'
                ],
                'phone' => [
                    'required',
                    'string',
                    'max:200',
                    'regex:/^[0-9]{10}$/'
                ],
                'email' => [
                    'required',
                    'string',
                    'email:rfc',
                    'max:200',
                    'unique:users'
                ],
                'user_disabled' => [
                    'required',
                    'boolean'
                ],
                'util_id' => [
                    'required',
                    'exists:utils',
                    'integer'
                ],
            ],
            'edit' => [
                'username' => [
                    'required',
                    'string',
                ],
                'password' => [
                    'nullable',
                    'string',
                    'confirmed',
                    'max:20'
                ],
                'name' => [
                    'required',
                    'string',
                    'max:200'
                ],
                'phone' => [
                    'required',
                    'string',
                    'max:200',
                    'regex:/^[0-9]{10}$/'
                ],
                'email' => [
                    'required',
                    'string',
                    'email:rfc',
                    'max:200',
                    'unique:users'
                ],
                'user_disabled' => [
                    'required',
                    'boolean'
                ],
                'util_id' => [
                    'required',
                    'exists:utils',
                    'integer'
                ],
            ],
            'attributes' => [
                'username' => '帳號',
                'password' => '密碼',
                'name' => '姓名',
                'phone' => '電話',
                'email' => '電子信箱',
                'user_disabled' => '停用',
                'util_id' => '所屬單位',
            ]
        ];
    }

    public static function messages() {
        return [
            'invalid-username' => ':attribute 只能包含英數字',
            'invalid-phone' => ':attribute 須為10個數字格式'
        ];
    }

    public static function permission($status, $id = null) {
        $pass = true;
        if($status == 'get') {
            $auth = SessionUtil::getLoginUser();
            // if($auth['id'] !== 1 && $auth['id'] !== $origin->user_id)
        } else if($status == 'edit') {
            $origin = _MODEL::find($id);
            $auth = SessionUtil::getLoginUser();
            if($auth['id'] !== 1 && $auth['id'] !== $origin->user_id) $pass = false;
            // dd($origin, $auth);
        }

        return $pass;
    }

    public static function getData($request, $id = null) {
        $query = new _MODEL();
        $models = [];
        $collect = collect([]);
        $filters = isset($request->filters) ? $request->filters : [];
        $orders = isset($request->orders) ? $request->orders : ['user_id'];

        if(is_null($id)) {
            $models = $query->get();
        } else {
            array_push($models, $query::find($id));
        }

        foreach($models as $model) {
            $user = $model->toArray();
            $util = $model->util()->first()->toArray();
            $user['util_name'] = $util['util_name'];

            $collect->add($user);
        }
        // dd($collect);

        return $collect->toArray();
    }

    public static function beforeValidation(Array &$data, Array &$rules, Array &$messages, String $status) {
        $messages["username.regex"] = self::messages()["invalid-username"];
        $messages["phone.regex"] = self::messages()["invalid-phone"];
        if($status == 'edit') {
            $origin = _MODEL::find($data["user_id"]);
            if($origin->email === $data['email']){
                ValidateUtil::unsetRules($rules["email"], "unique:users");
            }
        }
    }

    public static function afterValidation(Array &$data, Array &$result, String $status) {
        $pass = true;
        if(is_null($data["password"])) {
            unset($data["password"]);
        } else {
            $data["password"] = Hash::make($data["password"]);
        }

        return $pass;
    }

    public static function afterSave(Array &$data, Array &$result, String $status, Array $old = []) {
        $success = true;

        if(SessionUtil::getLoginUser()['id'] === (int) $data["user_id"]) {
            $user = _MODEL::find($data['user_id']);
            $userArr = [
                'id' => $user->user_id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'util' => $user->util,
            ];
            session(['user' => $userArr]);
        }

        return $success;
    }
}
