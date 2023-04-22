<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\UserPhoneNot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Intervention\Image\ImageManagerStatic as Image;


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

    public function update_profile(Request $request)
    {
        return $request;
        $user = $request->user();
        if ($user) {
            $user_fields = json_decode($request['user_fields']);
            $imageName = null;
            if ($request->file($user->id)) {
                $image = $request->file("{$request['user_id']}");

                $imageName = $user->id . "_" . time() . '.' . $image->extension();

                if (File::exists(storage_path("app/user_images/" . $user['image_url']))) {
                    File::delete(storage_path("app/user_images/" . $user->image_url));
                }

                $img512 = Image::make($image->path());
                $img512->resize(512, 512, function ($constraint) {
                    $constraint->aspectRatio();
                });
                $img512->save(storage_path("app/user_images/" . 'picture_' . $imageName));
            }

            User::where('id', $user['id'])->update([
                'name' => $user_fields->name,
                //last_name,
                //job_name,
                //company
                'image_url' => $imageName == null ? null : "picture_$imageName"
            ]);

            $updated_user = $request->user();
            return response(['success' => true, 'message' => "", 'user' => $updated_user]);
        } else {
            return response(['success' => false, 'message' => ""]);
        }
    }
}
