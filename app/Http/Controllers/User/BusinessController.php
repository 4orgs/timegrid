<?php namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Session;
use Redirect;
use App\Business;
use Flash;

class BusinessController extends Controller {

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware('auth');
	}

	public function getHome()
	{
		$business = Business::find( Session::get('selected.business_id') );
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

	public function getSelect($business_slug)
	{
		try {
			$business_id = Business::where('slug', $business_slug)->first()->id;	
		} catch (Exception $e) {
			return 'ERROR';
		}
		Session::set('selected.business_id', $business_id);
		return Redirect::route('user.businesses.home');
	}

	public function getSuscriptions()
	{
		$contacts = \Auth::user()->contacts;
		return view('user.businesses.suscriptions', compact('contacts'));
	}
}
