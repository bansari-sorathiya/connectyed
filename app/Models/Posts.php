<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Posts extends Model
{
    use SoftDeletes;

    protected $table = 'posts';

    protected $fillable = array('title','slug','content','author_id','post_date');
    public $timestamps = true;

    public function Author(){
        return $this->belongsTo('App\User','author_id');
    }

    public function Postuser()
    {
        return $this->hasMany('App\User', "id", "author_id");
    }

    public function comments()
    {
        return $this->hasMany(Comments::class)->whereNull('parent_id');
    }
}
