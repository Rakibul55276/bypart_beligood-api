<?php

namespace App\Http\Controllers\Api\Directory;

use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Directory\DirectoryCategory;
use App\Models\Directory\DirectorySubCategory;
use App\Models\Directory\DirectoryCompany;
use App\Models\Directory\DirectoryCompanySubCategory;
use Illuminate\Support\Facades\DB;

use DateTime;
use DateTimeZone;


class DirectoryController extends ApiController
{   
    public function __construct() {

        $date = new DateTime("now", new DateTimeZone('Asia/Kuala_Lumpur'));
        $this->currentdate = $date->format('Y-m-d H:i:s');

    }

    public function basicDataPolulation(){

        $category_list=DB::table("directory_company_info")->select("category_name")->distinct("category_name")->get()->toArray();
        $category_list=array_column(json_decode(json_encode($category_list)), "category_name");

        foreach ($category_list as $key => $value) {
            $insertCategory=DB::table("directory_category")->insertGetId(["category_name"=>$value]);

            $sub_category_list=DB::table("directory_company_info")->select("sub_category_name")->where("category_name",$value)->distinct("sub_category_name")->get()->toArray();

            $sub_category_list=array_column(json_decode(json_encode($sub_category_list)), "sub_category_name");

            foreach ($sub_category_list as $k_sub => $v_sub) {
                $insertSubCategory=DB::table("directory_sub_category")->insertGetId(["sub_category_name"=>$v_sub,"directory_category_id"=>$insertCategory]);

                $company_list=DB::table("directory_company_info")->select("id")->where("sub_category_name",$v_sub)->get()->toArray();
                $company_list=array_column(json_decode(json_encode($company_list)), "id");

                foreach ($company_list as $k_com => $v_com) {
                    DB::table("directory_com_to_sub_cat")->insert(["dir_sub_category_id"=>$insertSubCategory,"dir_company_info_id"=>$v_com]);
                }
            }
            
        }
        
    }

    public function directoryScript(Request $request){
        $category_list='{
                        "data":[ {
                                    "category_name": "Accessories & Electronics",
                                    "subcategories": [
                                      {
                                        "category_name": "Accessories Retails",
                                        "category_icon":"https://byparts-spaces.sgp1.digitaloceanspaces.com/byparts-spaces/market-place/directory-image/4-accessories.png"
                                      },
                                      {
                                        "category_name": "Audio System - In Car Entertainment",
                                        "category_icon":"https://byparts-spaces.sgp1.digitaloceanspaces.com/byparts-spaces/market-place/directory-image/7-audio.png"
                                      },
                                      {
                                        "category_name": "Car Mats",
                                        "category_icon": ""
                                      },
                                      {
                                        "category_name": "Keys & Remote",
                                        "category_icon": ""
                                      },
                                      {
                                        "category_name": "Security System",
                                        "category_icon": ""
                                      },
                                      {
                                        "category_name": "Solar Films & Tinting",
                                        "category_icon":"https://byparts-spaces.sgp1.digitaloceanspaces.com/byparts-spaces/market-place/directory-image/3-solar.png"
                                      },
                                      {
                                        "category_name": "Upholstery-Seats & Tops",
                                        "category_icon":"https://byparts-spaces.sgp1.digitaloceanspaces.com/byparts-spaces/market-place/directory-image/12-seats.png"
                                      },
                                      {
                                        "category_name": "Tracking & Navigation",
                                        "category_icon": ""
                                      }
                                    ]
                                  },

                                  {
                                    "category_name": "Performance & Car Parts",
                                    "subcategories": [
                                      {
                                        "category_name": "Bodykits & Parts",
                                        "category_icon":"https://byparts-spaces.sgp1.digitaloceanspaces.com/byparts-spaces/market-place/directory-image/13-bodykits.png"
                                      },
                                      {
                                        "category_name": "Car Parts Stockists",
                                        "category_icon": ""
                                      },
                                      {
                                        "category_name": "Decals,Stickers & Number Plates",
                                        "category_icon":"https://byparts-spaces.sgp1.digitaloceanspaces.com/byparts-spaces/market-place/directory-image/14-license-plate.png"
                                      },
                                      {
                                        "category_name": "Handling & Safety",
                                        "category_icon": ""
                                      },
                                      {
                                        "category_name": "Power Enhancement",
                                        "category_icon":"https://byparts-spaces.sgp1.digitaloceanspaces.com/byparts-spaces/market-place/directory-image/18-power.png"
                                      },
                                      {
                                        "category_name": "Racing & Motorsports Equipments",
                                        "category_icon": ""
                                      },
                                      {
                                        "category_name": "Tyres & Rims",
                                        "category_icon":"https://byparts-spaces.sgp1.digitaloceanspaces.com/byparts-spaces/market-place/directory-image/9-tires.png"
                                      }
                                    ]
                                  },

                                  {
                                    "category_name": "Grooming & Car Care",
                                    "subcategories": [
                                      {
                                        "category_name": "Car Care Products",
                                        "category_icon": ""
                                      },
                                      {
                                        "category_name": "Car Grooming-Wash & Polish",
                                        "category_icon":"https://byparts-spaces.sgp1.digitaloceanspaces.com/byparts-spaces/market-place/directory-image/16-grooming.png"
                                      },
                                      {
                                        "category_name": "Car Fumigation & Pest Removal",
                                        "category_icon": ""
                                      },
                                      {
                                        "category_name": "Mobile car Grooming",
                                        "category_icon": ""
                                      },
                                      {
                                        "category_name": "Paint Protection",
                                        "category_icon":"https://byparts-spaces.sgp1.digitaloceanspaces.com/byparts-spaces/market-place/directory-image/15-painting-protection.png"
                                      }
                                    ]
                                  },

                                  {
                                    "category_name": "Maintenance & Repair",
                                    "subcategories": [
                                      {
                                        "category_name": "Accident Repair & Claims",
                                        "category_icon":"https://byparts-spaces.sgp1.digitaloceanspaces.com/byparts-spaces/market-place/directory-image/10-accident.png"
                                      },
                                      {
                                        "category_name": "Air Conditioning",
                                        "category_icon":"https://byparts-spaces.sgp1.digitaloceanspaces.com/byparts-spaces/market-place/directory-image/1-air-condition.png"
                                      },
                                      {
                                        "category_name": "Authorised Distributer Workshops",
                                        "category_icon": ""
                                      },
                                      {
                                        "category_name": "Authorised Insurance Workshops",
                                        "category_icon": ""
                                      },
                                      {
                                        "category_name": "CNG Workshop",
                                        "category_icon": ""
                                      },
                                      {
                                        "category_name": "Commercial Vehicle Workshops",
                                        "category_icon": ""
                                      },
                                      {
                                        "category_name": "Mobile Battery & Tyre Replacement",
                                        "category_icon": ""
                                      },
                                      {
                                        "category_name": "Repair & Servicing",
                                        "category_icon":"https://byparts-spaces.sgp1.digitaloceanspaces.com/byparts-spaces/market-place/directory-image/17-repair-service.png"
                                      },
                                      {
                                        "category_name": "Specialty Workshops",
                                        "category_icon": ""
                                      },
                                      {
                                        "category_name": "Spray Painting",
                                        "category_icon":"https://byparts-spaces.sgp1.digitaloceanspaces.com/byparts-spaces/market-place/directory-image/6-spray.png"
                                      },
                                      {
                                        "category_name": "Transmission and gearbox",
                                        "category_icon": ""
                                      },
                                      {
                                        "category_name": "Car Towing & Roadside Assistance",
                                        "category_icon": ""
                                      },
                                      {
                                        "category_name": "Vintage Vehicle Workshops",
                                        "category_icon": ""
                                      },
                                      {
                                        "category_name": "Windscreen Repair",
                                        "category_icon": ""
                                      }
                                    ]
                                  },

                                  {
                                    "category_name": "Car Sale & Rental",
                                    "subcategories": [
                                      {
                                        "category_name": "Car Auctions",
                                        "category_icon": ""
                                      },
                                      {
                                        "category_name": "Car Rental",
                                        "category_icon":"https://byparts-spaces.sgp1.digitaloceanspaces.com/byparts-spaces/market-place/directory-image/2-car-rental.png"
                                      },
                                      {
                                        "category_name": "Car Leasing",
                                        "category_icon":"https://byparts-spaces.sgp1.digitaloceanspaces.com/byparts-spaces/market-place/directory-image/11-leasing.png"
                                      },
                                      {
                                        "category_name": "Car Sharing",
                                        "category_icon": ""
                                      },
                                      {
                                        "category_name": "Commercial Vehicle Rental",
                                        "category_icon":"https://byparts-spaces.sgp1.digitaloceanspaces.com/byparts-spaces/market-place/directory-image/5-commercial.png"
                                      },
                                      {
                                        "category_name": "Chauffeur & Limuousine Service",
                                        "category_icon": ""
                                      },
                                      {
                                        "category_name": "Super & Exotic Car Rental",
                                        "category_icon": ""
                                      },
                                      {
                                        "category_name": "New Car Dealers",
                                        "category_icon": ""
                                      },
                                      {
                                        "category_name": "Used Car Dealers",
                                        "category_icon": ""
                                      }
                                    ]
                                  },

                                  {
                                    "category_name": "General Information",
                                    "subcategories": [
                                      {
                                        "category_name": "Evaluation Centres",
                                        "category_icon": ""
                                      },
                                      {
                                        "category_name": "Inspection Centres",
                                        "category_icon": ""
                                      },
                                      {
                                        "category_name": "Motor Insurance Companies",
                                        "category_icon": ""
                                      },
                                      {
                                        "category_name": "Movers & Storage Services",
                                        "category_icon": ""
                                      },
                                      {
                                        "category_name": "Scrap Yards & Exporters",
                                        "category_icon": ""
                                      },
                                      {
                                        "category_name": "Finance Companies & Banks",
                                        "category_icon": ""
                                      },
                                      {
                                        "category_name": "Valet services",
                                        "category_icon": ""
                                      }
                                    ]
                                  }
                                ]
                        }';
        $category_list=json_decode($category_list,true);

        foreach ($category_list as $key => $value) {
            foreach ($value as $kcat => $vcat) {
                $category_id=DB::table("directory_category")->insertGetId(["category_name"=>$vcat['category_name']]);

                $insertSubCategories=array();

                foreach ($vcat['subcategories'] as $ksub => $vsub) {
                    $insertSubCategories[$ksub]['sub_category_name']=$vsub['category_name'];
                    $insertSubCategories[$ksub]['sub_category_icon']=$vsub['category_icon'];
                    $insertSubCategories[$ksub]['directory_category_id']=$category_id;
                }

                DB::table("directory_sub_category")->insert($insertSubCategories);
            }
        }
    }

    public function getPopularCategories(){

        $categories=DirectorySubCategory::where("is_popular",1)->get()->toArray();

        return $this->successResponse($categories);
    }

    public function getAllCategories(){

        $categories=DirectoryCategory::all();

        foreach ($categories as $key => $value) {
            $sub_categories=DirectoryCategory::subCategoryList($value->id);

            $categories[$key]->sub_categories=$sub_categories;
        }

        return $this->successResponse($categories);
    }

    public function getCompanies(Request $request){

        $validator = Validator::make($request->all(), [
            'sub_category_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }

        $sub_category_id=$request->sub_category_id;
        $search_key=isset($request->search_key)?$request->search_key:"";

        $search_str="(1=1)";

        if(!empty($search_key)){
          $search_str="(company_name LIKE '%".$search_key."%' OR address LIKE '%".$search_key."%')";
        }

        $limit=isset($request->sub_category_id)?$request->sub_category_id:20;

        $company_ids=DirectoryCompany::companyList($sub_category_id);

        $company_ids=array_column(json_decode(json_encode($company_ids)), "dir_company_info_id");

        $company_list=DirectoryCompany::whereIn("id",$company_ids)->whereRaw($search_str)->paginate($limit);

        return $this->successResponse($company_list);
    }

    public function getRecomendedMerchants(){

        $rawSQL="(is_premium=1 OR is_recommended=1)";

        $sub_category_ids=DB::table('directory_com_to_sub_cat')
                          ->leftjoin('directory_company_info','directory_com_to_sub_cat.dir_company_info_id','=','directory_company_info.id')
                          ->whereRaw($rawSQL)->orderBy("is_premium","desc")->orderBy("is_recommended","desc")
                          ->get()->toArray();

        $recommended_array=array();

        foreach ($sub_category_ids as $key => $value) {
            $category_name=DirectorySubCategory::where("id",$value->dir_sub_category_id)->value("sub_category_name");

            if(!empty($category_name)){
              $recommended_array[$key]['category_name']=$category_name;

              $company_info=DirectoryCompany::where("id",$value->dir_company_info_id)->get()->toArray();
              $recommended_array[$key]['company_info']=$company_info[0];
            }
            
        }

        return $this->successResponse($recommended_array);
    }
    
}
