<?php

namespace App\Http\Controllers;

use App\Models\FavoriteModel;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{

    private function get_current_datetime()
    {
        return date('Y-m-d H:i:s');
    }

    public function like_photo(Request $request)
    {
        $check_photo = FavoriteModel::where('image_id', $request['photo']['id'])->where('user_id', 4)->first();
        if ($check_photo) {
            return response(['success' => false, 'message' => "photo_was_already_liked"]);
        } else {
            FavoriteModel::where('image_id', $request['photo']['id'])
                ->where('user_id', $request->user()->id)->delete();
                
            FavoriteModel::create([
                'image_id' => $request['photo']['id'],
                'user_id' => $request->user()->id,
                'created_at' => $this->get_current_datetime()
            ]);

            return response(['success' => true, 'message' => "photo_was_liked"]);
        }
    }

    public function remove_like_photo(Request $request)
    {
        FavoriteModel::where('image_id', $request['photo']['id'])->where('user_id', $request->user()->id)->delete();
        return response(['success' => true, 'message' => "photo_was_removed"]);
    }

    public function get_liked_photos(Request $request)
    {
        $liked_photos = FavoriteModel::where('user_id', $request->user()->id)->select('image_id', 'user_id')->get();

        $prepared_photos = [];

        foreach ($liked_photos as $photo) {
            $prepared_photos[] = ['photo_id' => $photo['image_id']];
        }

        return response(['photos' => $prepared_photos]);
    }
}
