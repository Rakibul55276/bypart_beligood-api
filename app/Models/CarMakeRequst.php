<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarMakeRequst extends Model
{
    use HasFactory;

    protected $fillable = [
        'make',
        'model',
        'manufactured_year',
        'fuel_type',
        'transmission',
        'engine_capacity',
        'variant',
        'condition',
        'supporting_document',
        'user_id',
        'manufactured_country'
     ];

     protected $table ='car_make_requsts';
}
