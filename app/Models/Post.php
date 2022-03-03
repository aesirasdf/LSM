<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;
    protected $fillable = [
        "content",
        "user_id"
    ];
    public $createdAtFormat;

    public function User(){
        return $this->belongsTo(User::class, "user_id", "id");
    }

    public function Likes(){
        return $this->belongsToMany(User::class, 'post_likes', 'post_id', 'user_id')->withTimestamps();
    }

    public function Comments(){
        return $this->hasMany(Post::class, "post_id", "id");
    }

    public function createdAtFormat(){
        $date = (array)date_diff(date_Create($this->created_at), now());
        if($date["y"] != 0){
            $unit = $date["y"] == 1 ? " year ago" : " years ago";
            $response = $date["y"] . $unit;
        }
        else if($date["m"] != 0){
            $unit = $date["m"] == 1 ? " month ago" : " months ago";
            $response = $date["m"] . $unit;
        }
        else if($date["d"] |= 0){
            $unit = $date["d"] == 1 ? " day ago" : " days ago";
            $response = $date["d"] . $unit;
        }
        else if($date["h"] != 0){
            $unit = $date["h"] == 1 ? " hour ago" : " hours ago";
            $response = $date["h"] . $unit;
        }
        else if($date['i'] != 0){
            $unit = $date["i"] == 1 ? " minute ago" : " minutes ago";
            $response = $date['i'] . $unit;
        }
        else if($date['s'] != 0){
            $response = "Just now";
        }
        return $response;
    }
    
    public function createdAtFormat2(){
        $date = (array)date_diff(date_Create($this->created_at), now());
        if($date["y"] != 0){
            $unit = "y";
            $response = $date["y"] . $unit;
        }
        else if($date["m"] != 0){
            $unit = "mon";
            $response = $date["m"] . $unit;
        }
        else if($date["d"] |= 0){
            $unit = "d";
            $response = $date["d"] . $unit;
        }
        else if($date["h"] != 0){
            $unit = "h";
            $response = $date["h"] . $unit;
        }
        else if($date['i'] != 0){
            $unit = "m";
            $response = $date['i'] . $unit;
        }
        else if($date['s'] != 0){
            $response = "Just now";
        }
        return $response;
    }

}
