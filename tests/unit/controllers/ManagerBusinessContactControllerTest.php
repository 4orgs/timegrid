<?php

use App\Models\Business;
use App\Models\Contact;
use App\Models\Service;
use App\Models\User;
use App\Models\Vacancy;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ManagerBusinessContactControllerTest extends TestCase
{
    use DatabaseTransactions;
    use CreateBusiness, CreateUser, CreateContact, CreateAppointment, CreateService, CreateVacancy;

    /**
     * @covers   App\Http\Controllers\Manager\BusinessContactController::index
     * @covers   App\Http\Controllers\Manager\BusinessContactController::create
     * @covers   App\Http\Controllers\Manager\BusinessContactController::store
     * @covers   App\Http\Controllers\Manager\BusinessContactController::show
     * @test
     */
    public function it_adds_a_contact_to_addressbook()
    {
        // Given a fixture of
        $this->arrangeFixture();

        $contact = $this->createContact([
            'firstname' => 'John',
            'lastname' => 'Doe'
            ]);

        // And I am authenticated as the business owner
        $this->actingAs($this->issuer);

        // And I visit the business contact list section and fill the form
        $this->visit(route('manager.business.contact.index', $this->business))
             ->click('Add a contact')
             ->type($contact->firstname, 'firstname')
             ->type($contact->lastname, 'lastname')
             ->press('Save');

        // Then I see the contact registered
        $this->assertResponseOk();
        $this->see('Contact registered successfully')
             ->see("{$contact->firstname} {$contact->lastname}");
    }

    /**
     * @covers   App\Http\Controllers\Manager\BusinessContactController::index
     * @covers   App\Http\Controllers\Manager\BusinessContactController::edit
     * @covers   App\Http\Controllers\Manager\BusinessContactController::update
     * @test
     */
    public function it_edits_a_contact_of_addressbook()
    {
        // Given a fixture of
        $this->arrangeFixture();

        $contact = $this->createContact([
            'firstname' => 'John',
            'lastname'  => 'Doe', 'nin' => '1133224455',
            ]);
        $this->business->contacts()->save($contact);

        // And I am authenticated as the business owner
        $this->actingAs($this->issuer);

        // And I visit the business contact edit form
        $this->visit(route('manager.business.contact.edit', ['business' => $this->business->slug, 'contact' => $contact->id]))
             ->see($contact->firstname)
             ->see($contact->lastname)
             ->see($contact->nin);

        // And I change the name and lastname
        $this->type('NewName', 'firstname')
             ->type('NewLastName', 'lastname')
             ->press('Update');

        // Then I see the contact updated on the list
        $this->assertResponseOk();
        $this->see('Updated successfully')
             ->see('NewName')
             ->see('NewLastName');
    }

    /**
     * @covers \App\Http\Controllers\Manager\BusinessContactController::destroy
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

        $response = $this->call('DELETE', route('manager.business.contact.destroy', [$this->business, $contact]));

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertCount(0, $this->business->fresh()->contacts);
    }

    /**
     * @covers \App\Http\Controllers\Manager\BusinessContactController::destroy
     * @test
     */
    public function it_denies_detaching_a_contact_from_business_to_unauthorized_user()
    {
        // Given a fixture of
        $this->arrangeFixture();

        $unauthorizedUser = $this->createUser();

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
        $this->actingAs($unauthorizedUser);
        $this->withoutMiddleware();

        $this->assertCount(1, $this->business->fresh()->contacts);

        $response = $this->call('DELETE', route('manager.business.contact.destroy', [$this->business, $contact]));

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertCount(1, $this->business->fresh()->contacts);
    }

    /**
     * @covers   App\Http\Controllers\Manager\BusinessContactController::index
     * @covers   App\Http\Controllers\Manager\BusinessContactController::create
     * @covers   App\Http\Controllers\Manager\BusinessContactController::store
     * @covers   App\Http\Controllers\Manager\BusinessContactController::show
     * @test
     */
    public function it_adds_a_contact_to_addressbook_that_links_to_existing_user()
    {
        // Given a fixture of
        $this->arrangeFixture();
        $existingUser = $this->createUser([
            'name' => 'John',
            'email' => 'johndoe@example.org'
            ]);

        $contact = $this->createContact([
            'firstname' => 'John',
            'lastname' => 'Doe',
            'email' => 'johndoe@example.org'
            ]);

        // And I am authenticated as the business owner
        $this->actingAs($this->issuer);

        // And I visit the business contact list section and fill the form
        $this->visit(route('manager.business.contact.index', $this->business))
             ->click('Add a contact')
             ->type($contact->firstname, 'firstname')
             ->type($contact->lastname, 'lastname')
             ->type($contact->email, 'email')
             ->press('Save');

        // Then I see the contact registered
        $this->assertResponseOk();
        $this->see('Contact registered successfully')
             ->see("{$contact->firstname} {$contact->lastname}");
        $this->assertEquals($contact->email, $existingUser->contacts()->first()->email);
    }

    /**
     * @covers   App\Http\Controllers\Manager\BusinessContactController::store
     * @covers   App\Http\Controllers\Manager\BusinessContactController::show
     * @test
     */
    public function it_adds_a_contact_to_addressbook_that_matches_an_existing_contact()
    {
        // Given a fixture of
        $this->arrangeFixture();
        $existingUser = $this->createUser([
            'name' => 'John',
            'email' => 'johndoe@example.org'
            ]);

        $existingContact = $this->createContact([
            'firstname' => 'John',
            'lastname'  => 'Doe',
            'email'     => 'johndoe@example.org',
            'nin'       => '123456789',
        ]);
        // And the existing contact belongs to the business addressbok
        $this->business->contacts()->attach($existingContact);

        $contact = $this->createContact([
            'firstname' => 'John',
            'lastname'  => 'Doe',
            'email'     => 'johndoe@example.org',
            'nin'       => '123456789',
        ]);

        // And I am authenticated as the business owner
        $this->actingAs($this->issuer);

        // And I visit the business contact list section and fill the form
        $this->visit(route('manager.business.contact.index', $this->business))
             ->click('Add a contact')
             ->type($contact->firstname, 'firstname')
             ->type($contact->lastname, 'lastname')
             ->type($contact->email, 'email')
             ->type($contact->nin, 'nin')
             ->press('Save');

        // Then I see the existing contact found
        $this->assertResponseOk();
        $this->see('We found this existing contact')
             ->see("{$contact->firstname} {$contact->lastname}");
        $this->assertEquals($contact->email, $existingContact->email);
        $this->assertEquals($contact->nin, $existingContact->nin);
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

        // a Business owned by Me (User)
        $this->issuer = $this->createUser();

        $this->business = $this->createBusiness();
        $this->business->owners()->save($this->issuer);

        // And the Business provides a Service
        $this->service = $this->makeService();
        $this->business->services()->save($this->service);

        // And the Service has Vacancies to be reserved
        $this->vacancy = $this->makeVacancy();
        $this->vacancy->service()->associate($this->service);
        $this->business->vacancies()->save($this->vacancy);
    }
}
