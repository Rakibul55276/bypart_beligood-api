<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompanyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Company::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {

        return [
            'company_name' => $this->faker->text(30),
            'company_logo' => "http://lorempixel.com/400/200/sports/",
            'company_email' => $this->faker->email,
            'company_phone_number' => $this->faker->randomNumber(0),
            'address' => $this->faker->streetAddress,
            'city' => $this->faker->city,
            'state' => $this->faker->state,
            'zip_code' => $this->faker->postcode,
            'country' => 'Malaysia',
            'new_company_registration_no' => $this->faker->numberBetween,
            'old_cpompany_registration_no' => $this->faker->numberBetween,
            'company_url' => $this->faker->url
        ];
    }
}
