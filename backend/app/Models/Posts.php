<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Posts extends Model
{
    use SoftDeletes;

    protected $table = 'posts';

    protected $guarded = [];

    public function attachments(){
        return $this->hasMany(PostAttachments::class, 'post_id');
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }
}
