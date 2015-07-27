<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Business;
use Log;

class BusinessAgendaController extends Controller
{
    /**
     * get Index
     *
     * @param  Business $business Business to get agenda
     * @return Response           Rendered view of Business agenda
     */
    public function getIndex(Business $business)
    {
        $appointments = $business->bookings()->with('contact')->with('business')->with('service')->unserved()->orderBy('start_at')->get();
        return view('manager.businesses.appointments.'.$business->strategy.'.index', compact('business', 'appointments'));
    }
}
