<?php

namespace App\Models\Listing;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListingQuestionAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'question',
        'question_images',
        'question_view_count',
        'listing_id',
        'question_asked_by_user_id'
    ];

    protected $table = 'listing_question_answer';

    // So listing_question_answer must need to belong to listing
    public function listing()
    {
        return $this->belongsTo('App\Models\Listing');
    }

    //Has many listing_question_answer_reply
    public function listing_question_answer_reply()
    {
        return $this->hasMany('App\Models\Listing\ListingQuestionAnswerReply');
    }

    //Count of reply
    public function listing_question_answer_reply_count()
    {
        return $this->listing_question_answer_reply()
            ->selectRaw('listing_question_answer_id, count(*) as reply_count')
            ->groupBy('listing_question_answer_id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'question_asked_by_user_id');
    }
}
