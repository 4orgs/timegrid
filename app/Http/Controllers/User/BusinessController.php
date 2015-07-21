<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Session;
use Redirect;
use App\Business;
use Flash;

class BusinessController extends Controller
{
    public function getHome(Business $business)
    {
        return view('user.businesses.show', compact('business'));
    }

    public function getList()
    {
        $businesses = Business::all();
        return view('user.businesses.index', compact('businesses'));
    }

    public function getSelect(Business $business)
    {
        Session::set('selected.business', $business);
        return $this->getHome($business);
    }

    public function getSuscriptions()
    {
        $contacts = \Auth::user()->contacts;
        return view('user.businesses.suscriptions', compact('contacts'));
    }
}
