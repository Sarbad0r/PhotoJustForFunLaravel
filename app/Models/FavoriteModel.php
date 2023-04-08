<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FavoriteModel extends Model
{
    use HasFactory;

    protected $table = 'favorites';

    protected $guarded = ['id'];

    public $timestamps = false;
}
