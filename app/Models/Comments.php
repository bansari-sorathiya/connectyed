<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comments extends Model
{
    use HasFactory;
    
    protected $table = 'comments';
    protected $dates = ['deleted_at'];
    protected $fillable = ['user_id', 'post_id', 'parent_id', 'body'];

    public function post()
    {
        return $this->belongsTo(Posts::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function replies()
   {
       return $this->hasMany(Comments::class, 'parent_id');
   }
}
