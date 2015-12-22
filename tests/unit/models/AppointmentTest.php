<?php

use App\Models\Appointment;
use App\Models\Business;
use App\Models\Contact;
use App\Models\User;
use App\Models\Vacancy;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laracasts\TestDummy\Factory;

class AppointmentTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @test
     */
    public function it_creates_an_appointment()
    {
        $appointment = Factory::create('App\Models\Appointment');

        $this->assertInstanceOf(Appointment::class, $appointment);
    }

    /**
     * @covers \App\Models\Appointment::user
     * @test
     */
    public function it_gets_the_contact_user_of_appointment()
    {
        $appointment = Factory::create('App\Models\Appointment');
        $user = $this->makeUser();
        $user->save();

        $contact = $this->makeContact($user);
        $contact->save();

        $business = $this->makeBusiness($user);
        $business->save();

        $appointment = $this->makeAppointment($business, $user, $contact);

        $this->assertEquals($user, $appointment->user());
    }

    /**
     * @covers \App\Models\Appointment::user
     * @test
     */
    public function it_gets_no_user_from_contact_of_appointment()
    {
        $issuer = $this->makeUser();
        $contact = $this->makeContact();
        $business = $this->makeBusiness($issuer);
        $appointment = $this->makeAppointment($business, $issuer, $contact);

        $this->assertNull($appointment->user());
    }

    /**
     * @covers \App\Models\Appointment::duplicates
     * @test
     */
    public function it_detects_a_duplicate_appointment()
    {
        $issuer = $this->makeUser();
        $issuer->save();

        $contact = $this->makeContact();
        $contact->save();

        $business = $this->makeBusiness($issuer);
        $business->save();

        $appointment = $this->makeAppointment($business, $issuer, $contact);
        $appointment->save();

        $appointmentDuplicate = $this->makeAppointment($business, $issuer, $contact);

        $this->assertTrue($appointmentDuplicate->duplicates());
    }

    /**
     * @covers \App\Models\Appointment::getFinishAtAttribute
     * @test
     */
    public function it_gets_the_finish_datetime_of_appointment()
    {
        $appointment = Factory::create('App\Models\Appointment', [
            'startAt'  => Carbon::parse('2015-12-08 08:00:00 UTC'),
            'duration' => 90,
        ]);

        $startAt = $appointment->startAt;
        $finishAt = $appointment->finishAt;

        $this->assertEquals('2015-12-08 09:30:00', $finishAt);
    }

    /**
     * @covers \App\Models\Appointment::vacancy
     * @test
     */
    public function it_gets_the_associated_vacancy()
    {
        $business = Factory::create(Business::class);

        $vacancy = Factory::create(Vacancy::class, [
            'business_id' => $business->id
            ]);

        $appointment = Factory::create(Appointment::class, [
            'business_id' => $business->id,
            'vacancy_id'  => $vacancy->id,
            'startAt'     => Carbon::parse('2015-12-08 08:00:00 UTC'),
            'duration'    => 90,
            ]);

        $this->assertInstanceOf(Vacancy::class, $appointment->vacancy);
    }

    /**
     * @covers \App\Models\Appointment::getDateAttribute
     * @test
     */
    public function it_gets_the_date_attribute_at_000000utc()
    {
        $business = Factory::create(Business::class);

        $appointment = Factory::create(Appointment::class, [
            'business_id' => $business->id,
            'startAt'     => Carbon::parse('2015-12-08 00:00:00 UTC'),
            'duration'    => 90,
            ]);

        $this->assertEquals($appointment->start_at->timezone($business->timezone)->toDateString(), $appointment->date);
    }

    /**
     * @covers \App\Models\Appointment::getDateAttribute
     * @test
     */
    public function it_gets_the_date_attribute_at_120000utc()
    {
        $business = Factory::create(Business::class);

        $appointment = Factory::create(Appointment::class, [
            'business_id' => $business->id,
            'startAt'     => Carbon::parse('2015-12-08 12:00:00 UTC'),
            'duration'    => 90,
            ]);

        $this->assertEquals($appointment->start_at->timezone($business->timezone)->toDateString(), $appointment->date);
    }

    /**
     * @covers \App\Models\Appointment::getDateAttribute
     * @test
     */
    public function it_gets_the_date_attribute_at_235959utc()
    {
        $business = Factory::create(Business::class);

        $appointment = Factory::create(Appointment::class, [
            'business_id' => $business->id,
            'startAt'     => Carbon::parse('2015-12-08 23:59:59 UTC'),
            'duration'    => 90,
            ]);

        $this->assertEquals($appointment->start_at->timezone($business->timezone)->toDateString(), $appointment->date);
    }

    /////////////
    // HELPERS //
    /////////////

    private function makeUser()
    {
        $user = factory(User::class)->make();
        $user->email = 'guest@example.org';
        $user->password = bcrypt('demoguest');

        return $user;
    }

    private function makeAppointment(Business $business, User $issuer, Contact $contact, $overrides = [])
    {
        $appointment = factory(Appointment::class)->make($overrides);
        $appointment->contact()->associate($contact);
        $appointment->issuer()->associate($issuer);
        $appointment->business()->associate($business);

        return $appointment;
    }

    private function makeContact(User $user = null)
    {
        $contact = factory(Contact::class)->make();
        if ($user) {
            $contact->user()->associate($user);
        }

        return $contact;
    }

    private function makeBusiness(User $owner, $overrides = [])
    {
        $business = factory(Business::class)->make($overrides);
        $business->save();
        $business->owners()->attach($owner);

        return $business;
    }
}
