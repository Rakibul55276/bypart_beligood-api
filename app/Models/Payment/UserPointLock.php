<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPointLock extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'point_lock_type',
        'lock_point_amount',
        'point_lock_description',
        'listing_id'
     ];

     protected $table ='user_point_lock';
}
