<?php

use App\Models\Business;
use App\Models\User;
use App\Presenters\BusinessPresenter;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class BusinessTest extends TestCase
{
    use DatabaseTransactions;
    use CreateUser, CreateBusiness;

    /**
     * @covers \App\Models\Business::__construct
     * @test
     */
    public function it_creates_a_business()
    {
        $business = $this->createBusiness();

        $this->assertInstanceOf(Business::class, $business);
    }

    /**
     * @covers \App\Models\Business::__construct
     * @covers \App\Models\Business::save
     * @test
     */
    public function it_creates_a_business_that_appears_in_db()
    {
        $business = $this->createBusiness();

        $this->seeInDatabase('businesses', ['slug' => $business->slug]);
    }

    /**
     * @covers \App\Models\Business::__construct
     * @covers \App\Models\Business::setSlugAttribute
     * @covers \App\Models\Business::save
     * @test@
     */
    public function it_generates_slug_from_name()
    {
        $business = $this->createBusiness();

        $slug = str_slug($business->name);

        $this->assertEquals($slug, $business->slug);
    }

    /**
     * @covers \App\Models\Business::getPresenterClass
     * @test
     */
    public function it_gets_business_presenter()
    {
        $business = $this->createBusiness();

        $businessPresenter = $business->getPresenterClass();

        $this->assertSame(BusinessPresenter::class, $businessPresenter);
    }

    /**
     * @covers \App\Models\Business::setPhoneAttribute
     * @test
     */
    public function it_sets_empty_phone_attribute()
    {
        $business = $this->createBusiness(['phone' => '']);

        $this->assertNull($business->phone);
    }

    /**
     * @covers \App\Models\Business::setPostalAddressAttribute
     * @test
     */
    public function it_sets_empty_postal_address_attribute()
    {
        $business = $this->createBusiness(['postal_address' => '']);

        $this->assertNull($business->postal_address);
    }

    /**
     * @covers \App\Models\Business::owner
     * @test
     */
    public function it_gets_the_business_owner()
    {
        $owner = $this->createUser();

        $business = $this->createBusiness();
        $business->owners()->save($owner);

        $this->assertInstanceOf(User::class, $business->owner());
        $this->assertEquals($owner->name, $business->owner()->name);
    }

    /**
     * @covers \App\Models\Business::owners
     * @test
     */
    public function it_gets_the_business_owners()
    {
        $owner1 = $this->createUser();
        $owner2 = $this->createUser();

        $business = $this->createBusiness();

        $business->owners()->save($owner1);
        $business->owners()->save($owner2);

        $this->assertInstanceOf(Collection::class, $business->owners);
        $this->assertCount(2, $business->owners);
    }
}
