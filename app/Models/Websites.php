<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Websites extends Model
{
    use HasFactory;

    function websitePost(){
        return $this->hasMany(Posts::class,'website_id');
    }
    
    function createdBy(){
        return $this->belongsTo(User::class,'created_by');
    }
}
