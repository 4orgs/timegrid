<?php

namespace App\Http\Controllers\Manager;

use Gate;
use GeoIP;
use App\Category;
use App\Business;
use Laracasts\Flash\Flash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Fenos\Notifynder\Facades\Notifynder;
use App\Http\Requests\BusinessFormRequest;
use App\Http\Requests\BusinessPreferencesFormRequest;

class BusinessController extends Controller
{
    /**
     * index
     *
     * @return Response Rendered view for Businesses listing
     */
    public function index()
    {
        $this->log->info('Manager\BusinessController: index');
        $businesses = auth()->user()->businesses;
        if ($businesses->count()==1) {
            $this->log->info('Manager\BusinessController: index: Only one business to show');
            $business = $businesses->first();
            return Redirect::route('manager.business.show', $business);
        }
        return view('manager.businesses.index', compact('businesses'));
    }

    /**
     * create Business
     *
     * @return Response Rendered view of Business creation form
     */
    public function create()
    {
        $plan = Request::query('plan') ?: 'free';
        $this->log->info("Manager\BusinessController: create: plan:$plan");
        Flash::success(trans('manager.businesses.msg.create.success', ['plan' => trans("pricing.plan.$plan.name")]));

        $location = GeoIP::getLocation();
        $timezone = $location['timezone'];
        $this->log->info("Manager\BusinessController: create: timezone:$timezone location:".serialize($location));

        $categories = Category::lists('slug', 'id')->transform(
            function ($item, $key) {
                return trans('app.business.category.'.$item);
            }
        );
        return view('manager.businesses.create', compact('timezone', 'categories', 'plan'));
    }

    /**
     * store Business
     *
     * @param  BusinessFormRequest $request Business form Request
     * @return Response                     Redirect
     */
    public function store(BusinessFormRequest $request)
    {
        $this->log->info('Manager\BusinessController: store');
        $existingBusiness = Business::withTrashed()->where(['slug' => Request::input('slug')])->first();

        if ($existingBusiness === null) {
            $business = new Business(Request::all());
            $category = Category::find(Request::get('category'));
            $business->strategy = $category->strategy;
            $business->category()->associate($category);
            $business->save();
            auth()->user()->businesses()->attach($business);
            auth()->user()->save();

            $businessName = $business->name;
            Notifynder::category('user.registeredBusiness')
                       ->from('App\User', auth()->user()->id)
                       ->to('App\Business', $business->id)
                       ->url('http://localhost')
                       ->extra(compact('businessName'))
                       ->send();

            Flash::success(trans('manager.businesses.msg.store.success'));
            return Redirect::route('manager.business.service.create', $business);
        }

        $this->log->info("Manager\BusinessController: store: [ADVICE] Found existing businessId:{$existingBusiness->id}");
        if (auth()->user()->isOwner($existingBusiness)) {
            $this->log->info("Manager\BusinessController: store: [ADVICE] Restoring owned businessId:{$existingBusiness->id}");
            $existingBusiness->restore();
            Flash::success(trans('manager.businesses.msg.store.restored_trashed'));
        } else {
            $this->log->info("Manager\BusinessController: store: "
                    . "[ADVICE] Business already taken businessId:{$existingBusiness->id}");
            Flash::error(trans('manager.businesses.msg.store.business_already_exists'));
        }
        return Redirect::route('manager.business.index');
    }

    /**
     * show Business
     *
     * @param  Business            $business Business to show
     * @param  BusinessFormRequest $request  Business form Request
     * @return Response                      Rendered view for Business show
     */
    public function show(Business $business, BusinessFormRequest $request)
    {
        $this->log->info("Manager\BusinessController: show: businessId:{$business->id}");

        if (Gate::denies('show', $business)) {
            abort(403);
        }

        Session::set('selected.business', $business);
        $notifications = $business->getNotificationsNotRead(100);
        $business->readAllNotifications();
        return view('manager.businesses.show', compact('business', 'notifications'));
    }

    /**
     * edit Business
     *
     * @param  Business            $business Business to edit
     * @param  BusinessFormRequest $request  Business form Request
     * @return Response                      Rendered view of Business edit form
     */
    public function edit(Business $business)
    {
        $this->log->info("Manager\BusinessController: edit: businessId:{$business->id}");

        if (Gate::denies('update', $business)) {
            abort(403);
        }

        $location = GeoIP::getLocation();
        $timezone = in_array($business->timezone, \DateTimeZone::listIdentifiers()) ? $business->timezone : $timezone = $location['timezone'];
        $categories = Category::lists('slug', 'id')->transform(
            function ($item, $key) {
                return trans('app.business.category.'.$item);
            }
        );
        $category = $business->category_id;
        $this->log->info("Manager\BusinessController: edit: businessId:{$business->id} timezone:$timezone category:$category location:".serialize($location));
        return view('manager.businesses.edit', compact('business', 'category', 'categories', 'timezone'));
    }

    /**
     * update Business
     *
     * @param  Business            $business Business to update
     * @param  BusinessFormRequest $request  Business form Request
     * @return Response                      Redirect
     */
    public function update(Business $business, BusinessFormRequest $request)
    {
        $this->log->info("Manager\BusinessController: update: businessId:{$business->id}");

        if (Gate::denies('update', $business)) {
            abort(403);
        }

        $category = Category::find(Request::get('category'));
        $business->category()->associate($category);

        $business->update([
            'name' => $request->get('name'),
            'slug' => $request->get('slug'),
            'description' => $request->get('description'),
            'timezone' => $request->get('timezone'),
            'postal_address' => $request->get('postal_address'),
            'phone' => $request->get('phone'),
            'social_facebook' => $request->get('social_facebook'),
            'strategy' => $request->get('strategy')
        ]);

        Flash::success(trans('manager.businesses.msg.update.success'));
        return Redirect::route('manager.business.show', array($business->id));
    }

    /**
     * destroy Business
     *
     * @param  Business            $business Business to destroy
     * @param  BusinessFormRequest $request  Business form Request
     * @return Response                      Redirect to Businesses index
     */
    public function destroy(Business $business, BusinessFormRequest $request)
    {
        $this->log->info("Manager\BusinessController: destroy: businessId:{$business->id}");

        if (Gate::denies('destroy', $business)) {
            abort(403);
        }

        $business->delete();

        Flash::success(trans('manager.businesses.msg.destroy.success'));
        return Redirect::route('manager.business.index');
    }

    ////////////////////////////////////////////////////
    // Business Preferences                           //
    // TODO: Should be moved into separate controller //
    ////////////////////////////////////////////////////

    /**
     * get Preferences
     *
     * @param  Business                       $business Business to edit preferences
     * @param  BusinessPreferencesFormRequest $request  Request
     * @return Response                                 Rendered settings form
     */
    public function getPreferences(Business $business, BusinessPreferencesFormRequest $request)
    {
        $parameters = \Config::get('preferences.App\Business');
        $preferences = $business->preferences;
        return view('manager.businesses.preferences.edit', compact('business', 'preferences', 'parameters'));
    }

    /**
     * post Preferences
     *
     * @param  Business                       $business Business to update preferences
     * @param  BusinessPreferencesFormRequest $request  Request
     * @return Response                                 Redirect
     */
    public function postPreferences(Business $business, BusinessPreferencesFormRequest $request)
    {
        $this->log->info("Manager\BusinessController: postPreferences: businessId:{$business->id}");
        $parameters = \Config::get('preferences.App\Business');
        $parametersKeys = array_flip(array_keys($parameters));
        $preferences = $request->all();
        $preferences = array_intersect_key($preferences, $parametersKeys);
        
        foreach ($preferences as $key => $value) {
            $this->log->info("Manager\BusinessController: " .
                      "postPreferences: businessId:{$business->id} key:$key value:$value " .
                      "type:{$parameters[$key]['type']}");

            $business->pref($key, $value, $parameters[$key]['type']);
        }

        $businessName = $business->name;
        Notifynder::category('user.updatedBusinessPreferences')
                   ->from('App\User', auth()->user()->id)
                   ->to('App\Business', $business->id)
                   ->url('http://localhost')
                   ->extra(compact('businessName'))
                   ->send();

        Flash::success(trans('manager.businesses.msg.preferences.success'));
        return Redirect::route('manager.business.show', $business);
    }
}
