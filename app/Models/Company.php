<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_name',
        'company_logo',
        'company_email',
        'company_phone_number',
        'address',
        'city',
        'state',
        'zip_code',
        'country',
        'new_company_registration_no',
        'old_cpompany_registration_no',
        'company_url',
        'company_cert_ssm_file',
        'other_supporting_files',
        'name_card_file',
        'premise_pictures'
    ];

}
