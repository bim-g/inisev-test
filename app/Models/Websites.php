<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Websites extends Model
{
    use HasFactory;

    function posts(){
        return $this->hasMany(Posts::class,'website_id');
    }
    function subsribers(){
        return $this->hasMany(Subscribers::class,'website_id');
    }

    function createdBy(){
        return $this->belongsTo(User::class,'created_by');
    }
}
