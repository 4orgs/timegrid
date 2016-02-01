<?php

use Timegridio\Concierge\Models\Appointment;
use Timegridio\Concierge\Models\Business;
use Timegridio\Concierge\Models\Contact;
use Timegridio\Concierge\Models\Service;
use App\Models\User;
use Timegridio\Concierge\Models\Vacancy;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class VacancyTest extends TestCase
{
    use DatabaseTransactions;

    protected $business;

    /**
     * @covers  \Timegridio\Concierge\Models\Vacancy::isHoldingAnyFor
     * @test
     */
    public function it_verifies_a_vacancy_holds_appointment_for_a_user()
    {
        /* Setup Stubs */
        $issuer = factory(User::class)->create();

        $business = factory(Business::class)->create();
        $business->owners()->save($issuer);

        $service = factory(Service::class)->make();
        $business->services()->save($service);

        $vacancy = factory(Vacancy::class)->make();
        $vacancy->service()->associate($service);
        $business->vacancies()->save($vacancy);

        $contact = factory(Contact::class)->create();
        $contact->user()->associate($issuer);
        $contact->save();
        $business->contacts()->save($contact);

        $appointment = factory(Appointment::class)->make();
        $appointment->business()->associate($business);
        $appointment->service()->associate($service);
        $appointment->contact()->associate($contact);
        $appointment->vacancy()->associate($vacancy);
        $appointment->save();

        /* Perform Test */
        $this->assertTrue($vacancy->isHoldingAnyFor($issuer->id));
    }

    /**
     * @covers            \Timegridio\Concierge\Models\Vacancy::isHoldingAnyFor
     * @test
     */
    public function it_verifies_a_vacancy_doesnt_hold_appointment_for_a_user()
    {
        /* Setup Stubs */
        $issuer = factory(User::class)->create();

        $business = factory(Business::class)->create();
        $business->owners()->save($issuer);

        $service = factory(Service::class)->make();
        $business->services()->save($service);

        $vacancy = factory(Vacancy::class)->make();
        $vacancy->service()->associate($service);
        $business->vacancies()->save($vacancy);

        $contact = factory(Contact::class)->create();
        $business->contacts()->save($contact);

        $appointment = factory(Appointment::class)->make();
        $appointment->business()->associate($business);
        $appointment->service()->associate($service);
        $appointment->contact()->associate($contact);
        $appointment->vacancy()->associate($vacancy);
        $appointment->save();

        /* Perform Test */
        $this->assertFalse($vacancy->isHoldingAnyFor($issuer->id));
    }
}
