<?php

use App\Models\Make;

function makeGenerate()
{
    $make = Make::distinct()->get();
    foreach ($make as $m) {
        $model = Make::select('model_name')->where('make', $m['make'])->distinct()->get();
        foreach ($model as $mode) {
            $min_year = Make::where('make', $m->make)->where('model_name', $mode['model_name'])->value('min_year');
            $max_year = Make::where('make', $m->make)->where('model_name', $mode['model_name'])->value('max_year');
            $max_year = $max_year == "" ? date('Y') : $max_year;
            $year = range($min_year, $max_year);

            foreach ($year as $y) {
                $matchThese = [
                    "make" => $m->make,
                    "model_name" => $m->model_name,
                    "variant" => $m->variant,
                    "generation" => $m->generation,
                    "max_year" => $m->max_year,
                    "min_year" => $y,
                    "fuel_type" => $m->fuel_type,
                    "car_body_type" => $m->car_body_type,
                    "door" => $m->door,
                    "seat" => $m->seat,
                    "engine_size" => $m->engine_size,
                    "engine_code" => $m->engine_code
                ];
                $isSuccess = Make::updateOrCreate($matchThese, [
                    "make" => $m->make,
                    "model_name" => $m->model_name,
                    "variant" => $m->variant,
                    "generation" => $m->generation,
                    "max_year" => $m->max_year,
                    "min_year" => $y,
                    "fuel_type" => $m->fuel_type,
                    "car_body_type" => $m->car_body_type,
                    "door" => $m->door,
                    "seat" => $m->seat,
                    "engine_size" => $m->engine_size,
                    "engine_code" => $m->engine_code
                ]);
            }
        }
    }
}

function makegeneratorCommaSeperated()
{
    $make = Make::distinct()->get();
    $i = 0;
    foreach ($make as $m) {
        $i++;
        $min_year = Make::where('id', $i)->value('min_year');
        $max_year = Make::where('id', $i)->value('max_year');
        $max_year = $max_year == "" ? date('Y') : $max_year;
        $year = range($min_year, $max_year);
        $year_to_insert = implode(',', $year);

        Make::where("id", $i)->update([
            'min_max_year' => $year_to_insert
        ]);
    }
}
