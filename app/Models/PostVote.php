<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostVote extends Model
{
    protected $fillable = ['post_id', 'user_id', 'choice'];
}
