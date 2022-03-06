<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'amount',
        'reference_id',
        'transaction_id',
        'paydate',
        'status',
        'message'
     ];

     protected $table ='payment_history';
}
