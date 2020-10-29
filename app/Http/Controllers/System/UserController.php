<?php
namespace App\Http\Controllers\System;

use Request;
use App\Http\Controllers\Controller;

class UserController extends Controller {
    public function getUserSession() {
        $user = Request::session()->get('user');

        return response()->json(['user' => $user]);
    }


}
