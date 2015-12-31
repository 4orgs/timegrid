<?php

use App\Models\Appointment;
use App\Models\Business;
use App\Models\Contact;
use App\Models\Service;
use App\Models\User;
use App\Models\Vacancy;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserBusinessContactControllerTest extends TestCase
{
    use DatabaseTransactions;
    use CreateBusiness, CreateUser, CreateContact, CreateAppointment, CreateService;

    /**
     * @covers \App\Http\Controllers\User\BusinessContactController::create
     * @covers \App\Http\Controllers\User\BusinessContactController::store
     * @covers \App\Http\Controllers\User\BusinessContactController::show
     * @test
     */
    public function it_creates_a_contact_subscription()
    {
        // Given a fixture of
        $this->arrangeFixture();
        $contact = $this->createContact([
            'firstname' => 'John',
            'lastname'  => 'Doe',
            ]);

        // And I am authenticated as the business owner
        $this->actingAs($this->issuer);

        // And I visit the business contact list section and fill the form
        $this->visit(route('user.businesses.home', $this->business))
             ->click('Subscribe');

        $this->see('Save')
             ->type($contact->firstname, 'firstname')
             ->type($contact->lastname, 'lastname')
             ->press('Save');

        // Then I see the contact registered
        $this->assertResponseOk();
        $this->see('Successfully saved')
             ->see("{$contact->firstname} {$contact->lastname}")
             ->see('Book appointment');
    }

    /**
     * @covers \App\Http\Controllers\User\BusinessContactController::create
     * @covers \App\Http\Controllers\User\BusinessContactController::store
     * @covers \App\Http\Controllers\User\BusinessContactController::show
     * @test
     */
    public function it_creates_a_contact_subscription_reusing_existing_contact()
    {
        // Given a fixture of
        $this->arrangeFixture();
        $existingContact = $this->createContact([
            'firstname' => 'John',
            'lastname'  => 'Doe',
            'email'     => 'test@example.org',
            ]);
        $this->business->contacts()->save($existingContact);

        $contact = $this->createContact([
            'firstname' => 'John2',
            'lastname'  => 'Doe2',
            ]);

        // And I am authenticated as the business owner
        $this->actingAs($this->issuer);

        // And I visit the business contact list section and fill the form
        $this->visit(route('user.businesses.home', $this->business))
             ->click('Subscribe');

        $this->see('Save')
             ->type($contact->firstname, 'firstname')
             ->type($contact->lastname, 'lastname')
             ->type($existingContact->email, 'email')
             ->press('Save');

        // Then I see the contact registered
        $this->assertResponseOk();
        $this->see('This profile was already registered')
             ->see("{$existingContact->firstname} {$existingContact->lastname}");
        $this->assertEquals(true, $existingContact->businesses->contains($this->business));
    }

    /**
     * @covers \App\Http\Controllers\User\BusinessContactController::create
     * @covers \App\Http\Controllers\User\BusinessContactController::store
     * @covers \App\Http\Controllers\User\BusinessContactController::show
     * @test
     */
    public function it_creates_a_contact_subscription_copying_existing_contact()
    {
        // Given a fixture of
        $this->arrangeFixture();

        // I have a registered contact in Business A (other business)
        $otherBusiness = factory(Business::class)->create();
        $existingContact = $this->createContact([
            'firstname' => 'John',
            'lastname'  => 'Doe',
            'email'     => 'test@example.org',
            ]);
        $existingContact->user()->associate($this->issuer);
        $otherBusiness->contacts()->save($existingContact);

        // And I am authenticated as the business owner
        $this->actingAs($this->issuer);

        $beforeCount = $this->issuer->contacts->count();

        // And I visit the business home to get subscribed
        $this->visit(route('user.businesses.home', $this->business))
             ->click('Subscribe');

        $afterCount = $this->issuer->fresh()->contacts->count();

        // Then I am not requested for form filling and get my contact copied from existing
        $this->assertResponseOk();
        $this->see('Your profile was attached to an existing one')
             ->see("{$existingContact->firstname} {$existingContact->lastname}");
        $this->assertEquals($afterCount, $beforeCount + 1);
    }

    /**
     * @covers \App\Http\Controllers\User\BusinessContactController::edit
     * @covers \App\Http\Controllers\User\BusinessContactController::update
     * @covers \App\Http\Controllers\User\BusinessContactController::show
     * @test
     */
    public function it_edits_a_contact()
    {
        // Given a fixture of
        $this->arrangeFixture();

        // I have a registered contact in Business
        $contact = $this->createContact([
            'firstname' => 'John',
            'lastname'  => 'Doe',
            'nin'       => null,
            'email'     => null,
            ]);
        $contact->user()->associate($this->issuer);
        $this->business->contacts()->save($contact);

        // And I am authenticated as the business owner
        $this->actingAs($this->issuer);

        // And I visit the contact edit form
        // And set a NIN and and submit
        $this->visit(route('user.business.contact.edit', ['business' => $this->business, 'contact' => $contact]))
             ->type('1122334455', 'nin')
             ->press('Update');

        // Then I see the profile is updated with the NIN
        $this->assertResponseOk();
        $this->see('Updated successfully')
             ->see('1122334455');
    }

    /**
     * @covers \App\Http\Controllers\User\BusinessContactController::edit
     * @covers \App\Http\Controllers\User\BusinessContactController::update
     * @covers \App\Http\Controllers\User\BusinessContactController::show
     * @test
     */
    public function it_can_change_nin_of_a_contact()
    {
        // Given a fixture of
        $this->arrangeFixture();

        // I have a registered contact in Business
        $contact = $this->createContact([
            'firstname' => 'John',
            'lastname'  => 'Doe',
            'nin'       => '12345',
            'email'     => null,
            ]);
        $contact->user()->associate($this->issuer);
        $this->business->contacts()->save($contact);

        // And I am authenticated as the business owner
        $this->actingAs($this->issuer);

        // And I visit the contact edit form
        // And set a NIN and and submit
        $newNin = '54321';

        $this->visit(route('user.business.contact.edit', ['business' => $this->business, 'contact' => $contact]))
             ->type($newNin, 'nin')
             ->press('Update');

        // Then I see the profile is updated with the NIN
        $this->assertResponseOk();
        $this->see('Updated successfully')
             ->see($newNin);
    }

    /**
     * @covers \App\Http\Controllers\User\BusinessContactController::destroy
     * @test
     */
    public function it_detaches_a_contact_from_business()
    {
        // Given a fixture of
        $this->arrangeFixture();

        // I have a registered contact in Business
        $contact = $this->createContact([
            'firstname' => 'John',
            'lastname'  => 'Doe',
            'nin'       => '12345',
            'email'     => null,
            ]);
        $contact->user()->associate($this->issuer);
        $this->business->contacts()->save($contact);

        // And I am authenticated as the business owner
        $this->actingAs($this->issuer);
        $this->withoutMiddleware();

        $this->assertCount(1, $this->business->fresh()->contacts);

        $response = $this->call('DELETE', route('user.business.contact.destroy', [$this->business, $contact]));

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertCount(0, $this->business->fresh()->contacts);
    }

    /////////////
    // Fixture //
    /////////////

    /**
     * arrange fixture.
     *
     * @return void
     */
    protected function arrangeFixture()
    {
        // Given there is...
        $this->owner = $this->createUser();
        // a Business owned by Me (User)
        $this->issuer = $this->createUser();

        $this->business = $this->createBusiness();
        $this->business->owners()->save($this->owner);

        // And the Business provides a Service
        $this->service = $this->makeService();
        $this->business->services()->save($this->service);

        // And the Service has Vacancies to be reserved
        $this->vacancy = factory(Vacancy::class)->make();
        $this->vacancy->service()->associate($this->service);
        $this->business->vacancies()->save($this->vacancy);
    }
}
