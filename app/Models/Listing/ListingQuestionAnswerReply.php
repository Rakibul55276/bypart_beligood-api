<?php

namespace App\Models\Listing;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListingQuestionAnswerReply extends Model
{
    use HasFactory;

    protected $fillable = [
        'reply',
        'reply_images',
        'listing_question_answer_id',
        'question_reply_by_user_id'
    ];

    protected $visible = ['id', 'reply', 'reply_images', 'created_at','user'];

    protected $table ='listing_question_answer_reply';

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'question_reply_by_user_id');
    }

}
