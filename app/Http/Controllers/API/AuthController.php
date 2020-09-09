<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\API\RegisterRequest;
use App\Http\Requests\API\LoginRequest;
use App\Models\User;
use Hash;

class AuthController extends Controller
{
    /**
     * To register using API
     * 
     */
    public function register ( RegisterRequest $request ){

        $code = 200;

        $user = new User;
        $user->email = $request->get('email');
        $user->username = $request->get('username');
        $user->password = Hash::make( $request->get('password') );
        $user->registration_ip_address = $request->ip();
        
        if( $user->save() ){
            $data = [
                'status' => 'success',
                'code' => $code,
                'message' => 'User registered successfully.'
            ];
        }else{
            $code = 500;
            $data = [
                'status' => 'error',
                'code' => $code,
                'message' => 'Error ! User registration failed. Please try again.'
            ];

        }
        return response()->json( $data, $code);
    }

    /**
     * Login user
     * 
     */
    public function login ( LoginRequest $request ) {
        $code = 200 ;

        $email = $request->get('email');

        $loginKey = filter_var($email, FILTER_VALIDATE_EMAIL)  ? 'email' : 'username';

        $credentials = [
            $loginKey => $email,
            'password' => $request->get('password') ,

        ];

        if( !$token = auth()->attempt( $credentials ) ){
            $code = 500;
            $data = [
                'status' => 'error',
                'code' => $code,
                'message' => 'Credentials are invalid.'
            ];

        }else{

            $user = auth()->user();
            $user->last_login_ip_address = $request->ip();
            $user->last_login_time = date('Y-m-d H:i:s');
            $user->save();

            $data = [
                'status' => 'success',
                'code' => $code,
                'message' => 'Logged in successfully.',
                'data' => [
                    'token' => $token,
                    'type' => 'bearer',
                    'user' => $user
                ]
            ];
        }

        return response()->json( $data, $code);
    }
}
