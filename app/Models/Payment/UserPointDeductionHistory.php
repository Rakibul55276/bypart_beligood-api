<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPointDeductionHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'transaction_type',
        'deducted_point',
        'transaction_description',
        'other_details',
        'listing_id'
     ];

     protected $table ='user_point_deduction_history';
}
