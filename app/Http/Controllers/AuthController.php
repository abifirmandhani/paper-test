<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\User;
use Log;
use Carbon\Carbon;
use Auth;

class AuthController extends Controller
{
    /**
        *   @SWG\Post(
        *       path="/register",
        *       tags={"Auth"},
        *
        *        @SWG\Parameter(
        *            name="USER",
        *            required=true,
        *            in="body",
        *            @SWG\Schema(
        *                 type="object",
        *                 @SWG\Property(
        *                     property="username",
        *                     type="string"
        *                 ),
        *                 @SWG\Property(
        *                     property="password",
        *                     type="string"
        *                 ),
        *                 @SWG\Property(
        *                     property="name",
        *                     type="string"
        *                 ),
        *                 @SWG\Property(
        *                     property="email",
        *                     type="string"
        *                 ),
        *             )
        *        ),
        *       @SWG\Response(
        *           response="200",
        *           description="Success register",
        *       ),
        *       @SWG\Response(
        *           response="400",
        *           description="Bad request",
        *       ),
        *   )
    */
    public function register(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'name'      => 'required|string|max:255',
                'username'  => 'required|string|max:255|unique:users,username',
                'email'     => 'required|string|email|max:255|unique:users,email',
                'password'  => 'required|string|min:8|max:20',
            ]);

            if ($validator->fails()) {
                $message = $validator->errors();
                return $this->ResponseJson(
                    CONFIG("statusmessage.BAD_REQUEST"),
                    null,
                    $message,
                );
            }

            $user = new User;
            $user->name = $request->get("name");
            $user->email = $request->get("email");
            $user->username = $request->get("username");
            $user->password = Hash::make($request->get("password"));
            $user->created_at = time();
            $user->save();
    
            return $this->ResponseJson(
                CONFIG("statusmessage.SUCCESS"),
            );
        } catch (\Exception $e) {
            Log::error($e);
            return $this->ResponseJsonError();
        }
    }

    public function login(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'email'     => 'required|string|email',
                'password'  => 'required|string',
            ]);

            if ($validator->fails()) {
                $message = $validator->errors();
                return $this->ResponseJson(
                    CONFIG("statusmessage.BAD_REQUEST"),
                    null,
                    $message,
                );
            }

            $credentials = request(['email', 'password']);

            if (! $token = auth()->attempt($credentials)) {
                return $this->ResponseJson(
                    CONFIG("statusmessage.WRONG_CREDENTIAL"),
                );
            }

            $credentials = [
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth()->factory()->getTTL() * 60
            ];
    
            return $this->ResponseJson(
                CONFIG("statusmessage.SUCCESS"),
                $credentials
            );
        } catch (\Exception $e) {
            Log::error($e);
            return $this->ResponseJsonError();
        }
    }

    public function logout(){
        try {
            auth()->logout(true);
            return $this->ResponseJson(
                CONFIG("statusmessage.SUCCESS"),
            );
        } catch (\Exception $e) {
            Log::error($e);
            return $this->ResponseJsonError();
        }
    }

    public function me(){
        try {
            $user = auth()->user();
            return $this->ResponseJson(
                CONFIG("statusmessage.SUCCESS"),
                $user
            );
        } catch (\Exception $e) {
            Log::error($e);
            return $this->ResponseJsonError();
        }
    }
}
