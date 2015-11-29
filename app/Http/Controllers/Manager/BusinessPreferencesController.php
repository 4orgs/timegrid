<?php

namespace App\Http\Controllers\Manager;

use Illuminate\Http\Request;

use Gate;
use App\Business;
use App\Http\Requests;
use Laracasts\Flash\Flash;
use App\Http\Controllers\Controller;
use Fenos\Notifynder\Facades\Notifynder;

class BusinessPreferencesController extends Controller
{
    ////////////////////////////////////////////////////
    // Business Preferences                           //
    // TODO: Should be moved into separate controller //
    ////////////////////////////////////////////////////

    /**
     * get Preferences
     *
     * @param  Business      $business Business to edit preferences
     * @return Response      Rendered settings form
     */
    public function getPreferences(Business $business)
    {
        if (Gate::denies('managePreferences', $business)) {
            abort(403);
        }

        $parameters = config()->get('preferences.App\Business');
        $preferences = $business->preferences;
        return view('manager.businesses.preferences.edit', compact('business', 'preferences', 'parameters'));
    }

    /**
     * post Preferences
     *
     * @param  Business    $business Business to update preferences
     * @return Response    Redirect
     */
    public function postPreferences(Business $business, Request $request)
    {
        $this->log->info("Manager\BusinessController: postPreferences: businessId:{$business->id}");

        if (Gate::denies('managePreferences', $business)) {
            abort(403);
        }

        $parameters = config()->get('preferences.App\Business');
        $parametersKeys = array_flip(array_keys($parameters));
        $preferences = $request->all();
        $preferences = array_intersect_key($preferences, $parametersKeys);
        
        foreach ($preferences as $key => $value) {
            $this->log->info("Manager\BusinessController: " .
                      "postPreferences: businessId:{$business->id} key:$key value:$value " .
                      "type:{$parameters[$key]['type']}");

            $business->pref($key, $value, $parameters[$key]['type']);
        }

        $business_name = $business->name;
        Notifynder::category('user.updatedBusinessPreferences')
                   ->from('App\User', auth()->user()->id)
                   ->to('App\Business', $business->id)
                   ->url('http://localhost')
                   ->extra(compact('business_name'))
                   ->send();

        Flash::success(trans('manager.businesses.msg.preferences.success'));
        return redirect()->route('manager.business.show', $business);
    }
}
