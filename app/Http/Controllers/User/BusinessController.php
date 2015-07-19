<?php namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Session;
use Redirect;
use App\Business;
use Flash;

class BusinessController extends Controller
{
    public function getHome()
    {
        if (!Session::get('selected.business')) {
            return Redirect::route('user.wizard');
        }
        $business = Business::find(Session::get('selected.business')->id);
        if (empty($business)) {
            Flash::warning(trans('user.business.msg.please_select_a_business'));
            return Redirect::route('user.businesses.list');
        }
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
        return Redirect::route('user.businesses.home');
    }

    public function getSuscriptions()
    {
        $contacts = \Auth::user()->contacts;
        return view('user.businesses.suscriptions', compact('contacts'));
    }
}
