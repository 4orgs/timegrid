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
        $contact = factory(Contact::class)->make(['firstname' => 'John', 'lastname' => 'Doe']);

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
        $contact = factory(Contact::class)->create(['firstname' => 'John', 'lastname' => 'Doe', 'nin' => '1133224455']);
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

#    /**
#     * @covers   App\Http\Controllers\Manager\BusinessContactController::show
#     * @covers   App\Http\Controllers\Manager\BusinessContactController::destroy
#     * @test
#     */
#    public function it_removes_a_contact_from_addressbook()
#    {
#        // Given a fixture of
#        $this->arrangeFixture();
#        $contact = factory(Contact::class)->create(['firstname' => 'DeletemeFirst', 'lastname' => 'DeletemeLast', 'nin' => '1133224455']);
#        $this->business->contacts()->save($contact);
#
#        // And I am authenticated as the business owner
#        $this->actingAs($this->issuer);
#
#        // And I visit the business contact profile
#        $this->visit(route('manager.business.contact.show', ['business' => $this->business->slug, 'contact' => $contact->id]));
#
#        // TODO: DELETE CONTACT BY CLICK
#
#        // Then I see the contact not listed and message of confirmation
#        $this->assertResponseOk();
#        $this->see('Contact deleted')
#             ->dontSee($contact->firstname)
#             ->dontSee($contact->lastname);
#    }

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
        $existingUser = factory(User::class)->create(['name' => 'John', 'email' => 'johndoe@example.org']);

        $contact = factory(Contact::class)->make(['firstname' => 'John', 'lastname' => 'Doe', 'email' => 'johndoe@example.org']);

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
        $existingUser = factory(User::class)->create(['name' => 'John', 'email' => 'johndoe@example.org']);

        $existingContact = factory(Contact::class)->create([
            'firstname' => 'John',
            'lastname'  => 'Doe',
            'email'     => 'johndoe@example.org',
            'nin'       => '123456789',
        ]);
        // And the existing contact belongs to the business addressbok
        $this->business->contacts()->attach($existingContact);

        $contact = factory(Contact::class)->make([
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

    /**
     * arrange fixture.
     *
     * @return void
     */
    protected function arrangeFixture()
    {
        // A business owned by a user (me)
        $this->issuer = factory(User::class)->create();

        $this->business = factory(Business::class)->create();
        $this->business->owners()->save($this->issuer);

        // And the business provides a Service
        $this->service = factory(Service::class)->make();
        $this->business->services()->save($this->service);

        // And Service has vacancies to be reserved
        $this->vacancy = factory(Vacancy::class)->make();
        $this->vacancy->service()->associate($this->service);
        $this->business->vacancies()->save($this->vacancy);
    }
}
