<?php
namespace App\Utils;

use Request;

class UserUtil {
    public static function permissionValidate($data) {
        $user = Request::session()->get('user');
        $pass = $user["id"] === 1 || $user["id"] === $data->created_by;

        return $pass;
    }
}
