<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Make;
use App\Models\CarMakeRequst;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Listing\Listing;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class MakeController extends ApiController
{
    public function getAllMake()
    {
        $makes = Make::select('make')->distinct()->get();
        return $this->successResponse($makes);
    }

    public function getAllModel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'make_name' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }

        $makes = Make::select('model_name')->where('make', $request->input('make_name'))->distinct()->get();
        return $this->successResponse($makes);
    }

    public function getManufacturedYear(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'make_name' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }
        /**
         * During listing add we need to sort the year base on model & make
         * But other place it still ok to populate with make_name only
         */
        $result = '';
        if ($request->has('model_name') && !is_null($request->input('model_name'))) {
            $allYear = Make::select('min_max_year')->where('make', $request->input('make_name'))
                ->where('model_name', $request->input('model_name'))->pluck('min_max_year');
            $i = 0;
            $totalRecords = count($allYear);
            foreach ($allYear as $year) {
                $comma = $totalRecords-1 == $i ? '' : ',';
                $result .= $year.$comma;
                $i++;
            }
        } else {
            //$min_year = Make::select('min_year')->where('make', $request->input('make_name'))->min('min_year');
            //$max_year = Make::select('max_year')->where('make', $request->input('make_name'))->max('max_year');
        }

        $result = explode(',', $result);
        $result = array_keys(array_flip($result));
        unset($result[0]);
        rsort($result);

        return $this->successResponse($result);
    }

    public function getFuelType(Request $request)
    {
        if (
            $request->has('model_name') && !is_null($request->input('model_name')) &&
            $request->has('manufactured_year') && !is_null($request->input('manufactured_year'))
        ) {
            $makes = Make::select('fuel_type')->where('make', $request->input('make_name'))
                ->where('model_name', $request->input('model_name'))
                ->where(function ($query) use ($request) {
                    $query->whereRaw("find_in_set($request->manufactured_year, min_max_year)");
                })->distinct()->get();
        } else {
            $makes = Make::select('fuel_type')->where('make', $request->input('make_name'))->distinct()->get();
        }
        return $this->successResponse($makes);
    }

    public function getCarBodyType(Request $request)
    {
        $makes = Make::select('car_body_type')
            ->where('make', $request->input('make_name'))
            ->where('model_name', $request->input('model_name'))
            ->where('fuel_type', $request->input('fuel_type'))
            ->where('variant', $request->input('variant'))
            ->where(function ($query) use ($request) {
                $query->whereRaw("find_in_set($request->manufactured_year, min_max_year)");
            })->distinct()->get();

        return $this->successResponse($makes);
    }

    public function getEngineSize(Request $request)
    {

        $makes = Make::select('engine_size')
            ->where('make', $request->input('make_name'))
            ->where('model_name', $request->input('model_name'))
            ->where('fuel_type', $request->input('fuel_type'))
            ->where('variant', $request->input('variant'))
            ->where(function ($query) use ($request) {
                $query->whereRaw("find_in_set($request->manufactured_year, min_max_year)");
            })->orderBy('engine_size', 'ASC')->distinct()->get();

        return $this->successResponse($makes);
    }

    public function getCarSeats(Request $request)
    {
        $makes = Make::select('seat')
            ->where('make', $request->input('make_name'))
            ->where('model_name', $request->input('model_name'))
            ->where('fuel_type', $request->input('fuel_type'))
            ->where('variant', $request->input('variant'))
            ->where(function ($query) use ($request) {
                $query->whereRaw("find_in_set($request->manufactured_year, min_max_year)");
            })->orderBy('seat', 'ASC')->distinct()->get();

        return $this->successResponse($makes);
    }

    public function getCarDoors(Request $request)
    {
        $makes = Make::select('door')
            ->where('make', $request->input('make_name'))
            ->where('model_name', $request->input('model_name'))
            ->where('fuel_type', $request->input('fuel_type'))
            ->where('variant', $request->input('variant'))
            ->where(function ($query) use ($request) {
                $query->whereRaw("find_in_set($request->manufactured_year, min_max_year)");
            })->orderBy('door', 'ASC')->distinct()->get();

        return $this->successResponse($makes);
    }

    public function getVariant(Request $request)
    {
        /**
         * During listing add we need to sort the varaint base on model & manufactured_year
         * But other place it still ok to populate with make_name only
         */
        if (
            $request->has('model_name') && !is_null($request->input('model_name')) &&
            $request->has('manufactured_year') && !is_null($request->input('manufactured_year')) &&
            $request->has('fuel_type') && !is_null($request->input('fuel_type'))
        ) {
            $makes = Make::select('variant')->where('variant', '!=', null)
                ->where('make', $request->input('make_name'))
                ->where('model_name', $request->input('model_name'))
                ->where(function ($query) use ($request) {
                    $query->whereRaw("find_in_set($request->manufactured_year, min_max_year)");
                })->where('fuel_type', $request->input('fuel_type'))
                ->distinct()->orderBy('variant', 'asc')->get();
        } else {
            $makes = Make::select('variant')->where('variant', '!=', null)->where('make', $request->input('make_name'))->distinct()->get();
        }
        return $this->successResponse($makes);
    }

    public function saveMakeRequestForAdminReview(Request $request)
    {
        $user = Auth::user();
        $validator = Validator::make($request->all(), [
            'make' => 'required',
            'model' => 'required',
            'manufactured_year' => 'required',
            'fuel_type' => 'required',
            'transmission' => 'required',
            'engine_capacity' => 'required',
            'condition' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422,);
        }

        $data = $request->all();
        $data['user_id'] = $user->id;

        $save = CarMakeRequst::create($data);

        if ($save->exists) {
            return $this->successResponse(true, "Your request has been saved and it will review by admin");
        }
        return $this->errorResponse("Could not save your request, please try again", 422);
    }

    /**
     * This all method to get from listing
     */
    public function getAllMakeFromListing()
    {
        //Expiring set to 5 minute
        $expire = Carbon::now()->addMinutes(5);
        $make = Cache::remember('make_from_list', $expire, function () {
            $make_name = Listing::select('car_make_name as make')->where('listing_status', '=', 'published')
                ->distinct()->orderby('make', 'asc')->get();

            $make_name->map(function ($make) {
                $listing = Listing::where('listing_status', '=', 'published')
                    ->where('car_make_name', $make->make);
                $make['model'] = $listing->distinct()->pluck('model');
                $make['car_body_type'] = $listing->distinct()->pluck('car_body_type');
                $make['fuel_type'] = $listing->distinct()->pluck('fuel_type');
                $make['engine_size'] = $listing->distinct()->pluck('engine_size');
                //For year
                $min_year = $listing->min('manufacture_year');
                $max_year = $listing->max('manufacture_year');
                $range = range(date($min_year), date($max_year));
                $make['range'] = $range;
            });

            return $make_name;
        });

        return $this->successResponse($make);
    }

    public function getCarBodyTypeFromListing(Request $request)
    {
        if ($request->has('make_name') && !is_null($request->input('make_name'))) {
            $car_body_type = Listing::select('car_body_type')->where('listing_status', '=', 'published')
                ->whereIn('car_make_name', $request->input('make_name'))->distinct()->orderby('car_body_type', 'asc')->get();
        } else {
            $car_body_type = Listing::select('car_body_type')->where('listing_status', '=', 'published')
                ->distinct()->orderby('car_body_type', 'asc')->get();
        }
        return $this->successResponse($car_body_type);
    }

    public function getModelFromListing(Request $request)
    {
        if ($request->has('make_name') && !is_null($request->input('make_name'))) {
            $model = Listing::select('model')->where('listing_status', '=', 'published')
                ->whereIn('car_make_name', $request->input('make_name'))->distinct()->orderby('model', 'asc')->get();
        } else {
            $model = Listing::select('model')->where('listing_status', '=', 'published')
                ->distinct()->orderby('model', 'asc')->get();
        }
        return $this->successResponse($model);
    }

    public function getFuelTypeFromListing(Request $request)
    {
        if ($request->has('make_name') && !is_null($request->input('make_name'))) {
            $fuel_type = Listing::select('fuel_type')->where('listing_status', '=', 'published')
                ->whereIn('car_make_name', $request->input('make_name'))->distinct()->orderby('fuel_type', 'asc')->get();
        } else {
            $fuel_type = Listing::select('fuel_type')->where('listing_status', '=', 'published')
                ->distinct()->orderby('fuel_type', 'asc')->get();
        }
        return $this->successResponse($fuel_type);
    }

    public function getEngineSizeFromListing(Request $request)
    {
        if ($request->has('make_name') && !is_null($request->input('make_name'))) {
            $engine_size = Listing::select('engine_size')->where('listing_status', '=', 'published')
                ->whereIn('car_make_name', $request->input('make_name'))->distinct()->orderby('engine_size', 'asc')->get();
        } else {
            $engine_size = Listing::select('engine_size')->where('listing_status', '=', 'published')
                ->distinct()->orderby('engine_size', 'asc')->get();
        }
        return $this->successResponse($engine_size);
    }

    public function getYearFromListing(Request $request)
    {
        if (
            $request->has('make_name') && !is_null($request->input('make_name')) &&
            $request->has('model') && !is_null($request->input('model'))
        ) {
            $min_year = Listing::select('manufacture_year')->where('listing_status', '=', 'published')->whereIn('model', $request->input('model'))->whereIn('car_make_name', $request->input('make_name'))->min('manufacture_year');
            $max_year = Listing::select('manufacture_year')->where('listing_status', '=', 'published')->whereIn('model', $request->input('model'))->whereIn('car_make_name', $request->input('make_name'))->max('manufacture_year');
        } else if (
            $request->has('make_name') && !is_null($request->input('make_name'))
        ) {
            $min_year = Listing::select('manufacture_year')->where('listing_status', '=', 'published')->whereIn('car_make_name', $request->input('make_name'))->min('manufacture_year');
            $max_year = Listing::select('manufacture_year')->where('listing_status', '=', 'published')->whereIn('car_make_name', $request->input('make_name'))->max('manufacture_year');
        } else {
            $min_year = Listing::select('manufacture_year')->where('listing_status', '=', 'published')->min('manufacture_year');
            $max_year = Listing::select('manufacture_year')->where('listing_status', '=', 'published')->max('manufacture_year');
        }

        $range = range(date($min_year), date($max_year));
        return $this->successResponse($range);
    }
}
