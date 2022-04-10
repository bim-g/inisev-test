<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Posts extends Model
{
    use HasFactory;

    function websitePost(){
        return $this->belongsTo(Websites::class,'website_id');
    }

    function postBy(){
        return $this->belongsTo(User::class,'post_by');
    }
    function website(){
        return $this->belongsTo(Websites::class,'website_id');
    }

    function emails(){
        return $this->hasMany(Email::class,'post_id');
    }
}
