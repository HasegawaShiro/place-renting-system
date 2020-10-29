<?php
namespace App\Utils;

use Request;

class SessionUtil {
    public static function getLoginUser() {
        $user = Request::session()->get('user');

        return $user;
    }
}
