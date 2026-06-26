<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = ['post_id', 'parent_id', 'user_id', 'body'];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id')->with('user.profile')->withCount('likes')->oldest();
    }

    public function likes()
    {
        return $this->hasMany(CommentLike::class);
    }
}
