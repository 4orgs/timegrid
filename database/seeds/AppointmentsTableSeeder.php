<?php

use Illuminate\Database\Seeder;

// composer require laracasts/testdummy
use Laracasts\TestDummy\Factory as TestDummy;

class AppointmentsTableSeeder extends Seeder
{
    public function run()
    {
    	DB::table('appointments')->delete();
        // TestDummy::times(20)->create('App\Post');
    	$business = \App\Business::where(['slug' => 'sample-biz'])->first();

    	$contact = \App\Contact::where(['nin' => 'YA4128062'])->first();

        \App\Appointment::create(['contact_id' => $contact->id, 'business_id' => $business->id, 'date' => '2015-07-01', 'time' => '18:30:00', 'duration' => 30, 'comments' => 'Appointment example']);
    }
}
