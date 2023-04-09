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
}
