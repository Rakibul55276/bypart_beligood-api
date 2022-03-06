<?php

namespace App\Models\Notification;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notifications extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'user_id',
        'listing_id',
        'url',
        'notification_type',
        'notification_header',
        'notification_content'
    ];

    public $timestamps = true;

    protected $table = 'notifications';
}
