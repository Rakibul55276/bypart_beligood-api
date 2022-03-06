<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForumComment extends Model
{
    use HasFactory;

    protected $fillable = [

    	'user_id',
        'comment',
        'images',
        'post_id',
        'reply_type',
        'parent_comment_id',
        'created_at'
        
    ];

    protected $table = 'forum_comment';
}
