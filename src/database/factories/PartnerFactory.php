<?php

/** @var Factory $factory */

use Different\Dwfw\app\Models\Partner;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(Partner::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'contact_name' => $faker->name,
        'contact_phone' => $faker->phoneNumber,
        'contact_email' => $faker->unique()->safeEmail,
    ];
});
