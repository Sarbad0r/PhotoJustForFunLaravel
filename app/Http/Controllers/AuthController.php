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

        //check the user
        $user = $request->user();
        //if user exists
        if ($user) {
            //get fieds decoding becouse getting field will be string type
            $user_fields = json_decode($request['user_fields']);

            $imageName = null;
            //check if getting request have a file with key 
            if ($request->file("{$user->id}")) {
                //if its true get this file
                $image = $request->file("{$user->id}");
                //get image and rename image name
                $imageName = $user->id . "_" . time() . '.' . $image->extension();

                //check the storage file if there user have any image delete them
                if (File::exists(storage_path("app/user_images/" . $user['image_url']))) {
                    File::delete(storage_path("app/user_images/" . $user->image_url));
                }

                //resize the image with laravel image intervention package
                $img512 = Image::make($image->path());
                $img512->resize(512, 512, function ($constraint) {
                    $constraint->aspectRatio();
                });
                //then save in storage path
                $img512->save(storage_path("app/user_images/" . 'picture_' . $imageName));
            }

            //if imagename null check the user, maybe user image field is not empty
            if ($imageName == null) {
                $imageName = $user->image_url == null ? null : $user->image_url;
            }

            //if image exists and image name does not contain the "picture_"
            if ($imageName && (str_contains($imageName, "picture_") == false)) {
                //change the image name
                $imageName = "picture_$imageName";
            }


            User::where('id', $user['id'])->update([
                'name' => $user_fields->name,
                'last_name' => $user_fields->last_name,
                "job_name" => $user_fields->prof,
                "company" => $user_fields->company,
                'image_url' => $imageName == null ? null : $imageName
            ]);

            //get updated user
            $updated_user = User::where("id", $request->user()->id)->first();

            return response(['success' => true, 'message' => "User successfully updated", 'user' => $updated_user]);
        } else {
            return response(['success' => false, 'message' => "User not found"]);
        }
    }

    public function get_user_image($id)
    {
        //check the user with sending id
        $user = User::where("id", $id)->first();
        //if user exists
        if ($user) {
            //check user image field
            if ($user['image_url']) {
                //if the user image field contains http or https
                if (str_contains($user['image_url'], 'https://')) {
                    //we will redirect to this url
                    return redirect($user['image_url']);
                } else {
                    //else check the image from storage path
                    if (File::exists(storage_path("app/user_images/" . $user['image_url']))) {
                        //if image exists return this image as url
                        return response()->file(storage_path("app/user_images/" . $user['image_url']));
                    }
                    //or return emtpy string
                    return '';
                }
            } else {
                //if user does not have image url return empty string
                return response([
                    'image' => ''
                ]);
                //https://lh3.googleusercontent.com/a/AGNmyxae2kasrGxu44SfgkOBdGJI5cAwnOvUDJ4qllcBZg=s96-c
            }
        } else {
            //if user does not exist return empty string
            return response([
                'image' => ''
            ]);
        }
    }
}
