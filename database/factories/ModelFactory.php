<?php

//////////
// User //
//////////

$factory->define('App\Models\User', function (Faker\Generator $faker) {
    return [
        'username' => $faker->firstName,
        'name'     => $faker->firstName,
        'email'    => $faker->safeEmail,
        'password' => bcrypt('password'),
    ];
});

//////////
// Role //
//////////

$factory->define('App\Models\Role', function (Faker\Generator $faker) {
    $name = $faker->word;

    return [
        'name'        => $faker->word,
        'slug'        => str_slug($name),
        'description' => $faker->sentence,
    ];
});

/////////////
// Contact //
/////////////

$factory->define('App\Models\Contact', function (Faker\Generator $faker) {
    return [
        'firstname'      => $faker->firstName,
        'lastname'       => $faker->lastName,
        'nin'            => $faker->numberBetween(25000000, 50000000),
        'email'          => $faker->safeEmail,
        'birthdate'      => \Carbon\Carbon::now()->subYears(30)->format('m/d/Y'),
        'mobile'         => null,
        'mobile_country' => null,
        'gender'         => $faker->randomElement(['M', 'F']),
        'occupation'     => $faker->title,
        'martial_status' => null,
        'postal_address' => $faker->address,
    ];
});

//////////////
// Business //
//////////////

$factory->define('App\Models\Business', function (Faker\Generator $faker) {
    return [
        'name'            => $faker->sentence(3),
        'description'     => $faker->paragraph,
        'timezone'        => $faker->timezone,
        'postal_address'  => $faker->address,
        'phone'           => null,
        'social_facebook' => 'https://www.facebook.com/example?fref=ts',
        'strategy'        => 'dateslot',
        'plan'            => 'free',
        'category_id'     => $faker->randomElement([1, 2, 3]),
    ];
});

//////////////////
// Service Type //
//////////////////

$factory->define('App\Models\ServiceType', function (Faker\Generator $faker) {
    return [
        'business_id' => factory(App\Models\Business::class)->create()->id,
        'name'        => $faker->sentence(3),
        'description' => $faker->paragraph,
    ];
});

/////////////
// Service //
/////////////

$factory->define('App\Models\Service', function (Faker\Generator $faker) {
    return [
        'business_id'   => factory(App\Models\Business::class)->create()->id,
        'name'          => $faker->sentence(2),
        'description'   => $faker->paragraph,
        'prerequisites' => $faker->paragraph,
        'duration'      => $faker->randomElement([15, 30, 60, 120]),
    ];
});

/////////////
// Vacancy //
/////////////

$factory->define('App\Models\Vacancy', function (Faker\Generator $faker) {
    $date = $faker->dateTimeBetween('today', 'today +7 days')->format('Y-m-d');

    return [
        'business_id' => factory(App\Models\Business::class)->create()->id,
        'service_id'  => factory(App\Models\Service::class)->create()->id,
        'date'        => date('Y-m-d', strtotime($date)),
        'start_at'    => date('Y-m-d 00:00:00', strtotime($date)),
        'finish_at'   => date('Y-m-d 23:00:00', strtotime($date)),
        'capacity'    => 1,
    ];
});

/////////////////
// Appointment //
/////////////////

$factory->define('App\Models\Appointment', function (Faker\Generator $faker) {
    return [
        'business_id' => factory(App\Models\Business::class)->create()->id,
        'contact_id'  => factory(App\Models\Contact::class)->create()->id,
        'service_id'  => factory(App\Models\Service::class)->create()->id,
        'vacancy_id'  => factory(App\Models\Vacancy::class)->create()->id,
        'status'      => $faker->randomElement(['R', 'C', 'A', 'S']),
        'start_at'    => Carbon::parse(date('Y-m-d 08:00:00', strtotime('today +2 days'))),
        'duration'    => $faker->randomElement([15, 30, 60, 120]),
        'comments'    => $faker->sentence,
    ];
});

////////////
// Domain //
////////////

$factory->define('App\Models\Domain', function ($faker) {
    return [
        'slug'     => str_slug($faker->name),
        'owner_id' => factory(App\Models\User::class)->create()->id,
    ];
});
