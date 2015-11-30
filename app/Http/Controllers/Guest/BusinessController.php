<?php

namespace App\Http\Controllers\Guest;

use App\Business;
use App\Http\Controllers\Controller;

class BusinessController extends Controller
{
    /**
     * get Home
     *
     * @param  Business $business Business to display
     * @return Response           Rendered view for desired Business
     */
    public function getHome(Business $business)
    {
        $this->log->info("BusinessController@getHome: businessId:{$business->id} businessSlug:({$business->slug})");

        return view('guest.businesses.show', compact('business'));
    }

    /**
     * get List
     *
     * @return Response Rendered view of all existing Businesses
     */
    public function getList()
    {
        $this->log->info('BusinessController@getList');
        
        $businesses = Business::all();
        return view('guest.businesses.index', compact('businesses'));
    }
}
