<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\UserPhoneNot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;


class AuthController extends Controller
{
    public function log_and_register_by_email(Request $request)
    {
        $user = User::create([
            'name' => 'avaz',
            'email' => 'avaz.shams.2002@gmail.com',
            'password' => Hash::make("123123"),
        ]);

        $user->notify(new UserPhoneNot($user));
    }


    public function login(Request $request)
    {
        //first check the email
        $user = User::where('email', $request['email'])->first();

        if (!$user) {
            return response([
                'success' => false,
                'message' => "User not exists"
            ]);
        }

        if (!Hash::check($request['password'], $user['password'])) {
            return response([
                'success' => false,
                'message' => "Enter correct password"
            ]);
        }

        return response([
            'success' => true,
            'token' => $user->createToken('login')->plainTextToken,
            'user' => $user
        ]);
    }

    public function check_token(Request $request)
    {
        $user = $request->user();
        if ($user) {
            return response([
                'success' => true,
                'user' => $user
            ]);
        } else {
            return response([
                'success' => false,
                'user' => $user
            ]);
        }
    }
}
