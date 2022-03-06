<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Make extends Model
{
    use HasFactory;

    protected $fillable = [
        'make',
        'model_name',
        'variant',
        'generation',
        'min_year',
        'max_year',
        'fuel_type',
        'car_body_type',
        'door',
        'engine_code',
        'min_max_year',
        'seat',
        'engine_size'
    ];

    protected $table = 'car_make';
}
