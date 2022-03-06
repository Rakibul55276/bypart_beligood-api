<?php

namespace App\Models\Directory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DirectoryCompany extends Model
{
    use HasFactory;

    protected $fillable = [
          "company_name",
		  "company_logo",
		  "company_email",
		  "company_phone_number",
		  "address",
		  "city",
		  "state",
		  "zip_code",
		  "country",
		  "company_url",
		  "name_card_file",
		  "premise_pictures",
		  "is_recommended",
		  "is_premium",
		  "operating_hour",
		  "premium_company_image",
		  "created_at",
		  "updated_at" 
    ];

    protected $table = 'directory_company_info';

    public static function companyList($sub_category_id){

        $query = DirectoryCompanySubCategory::where('dir_sub_category_id',$sub_category_id)->get();
        
        return $query;
    }
}
