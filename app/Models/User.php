<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use App\Models\Scopes\Searchable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;
    use HasFactory;
    use HasApiTokens;

    protected $fillable = [
        'password',
        'first_name',
        'last_name',
        'email',
        'mobile_no',
        'user_type',
        'status',
        'is_mobile_verified',
        'avatar',
        'provider',
        'provider_id',
        'company_id',
        'user_ic_photo',
        'user_ic_number',
        'email_verified_at',
        'is_email_verified',
        'mobile_verification_code',
        'phone_verified_at',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'status_admin'
    ];

    protected $hidden = ['password', 'remember_token', 'email_verification_code', 'mobile_verification_code', 'password_reset_token', 'created_at', 'updated_at'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
    ];
}
