<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = "/";

    public function username()
    {
        return 'username';
    }

    protected function authenticated(Request $request, $user)
    {
        if (!$user->user_disabled) {
            $userObj = [
                'id' => $user->user_id,
                'name' => $user->name,
                'util' => $user->util,
                'mail' => $user->mail,
                'phone' => $user->phone,
            ];
            $request->session()->put("user", $userObj);
            /* $request->session()->put("username", $user->username);
            $request->session()->put("user_name", $user->name);
            $request->session()->put("user_id", $user->user_id); */
            return response()->json([
                'success' => true,
                'message' => 'login-success',
                'user' => $userObj
            ], 200);
        } else {
            $this->logout($request);
            return response()->json([
                'success' => false,
                'message' => 'user-disabled',
            ], 401);
        }
    }
    protected function unauthenticated($request, $exception)
    {
        return response()->json(['error' => 'Unauthenticated.'], 401);
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
}
