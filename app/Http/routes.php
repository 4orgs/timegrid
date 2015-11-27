<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
*/

Route::group(['prefix' => 'api', 'middleware' => ['auth']], function () {

    Route::controller('booking', 'BookingController', [
        'postAction' => 'api.booking.action',
    ]);

    Route::group(['prefix' => 'services'], function () {
        
        Route::get('list/{business}', function ($business) {
            return $business->services()->lists('name', 'id');
        });
        
        Route::get('duration/{service}', function ($service) {
            return $service->duration;
        });
    });
});

Route::group(['prefix' => 'user', 'namespace' => 'User', 'middleware' => ['auth']], function () {

    Route::group(['prefix' => 'booking'], function () {
        Route::get('book/{business}', ['as' => 'user.booking.book', 'uses' => 'AgendaController@getAvailability']);
        Route::get('bookings', ['as' => 'user.booking.list', 'uses' => 'AgendaController@getIndex']);
        Route::post('store', ['as' => 'user.booking.store', 'uses' => 'AgendaController@postStore']);
    });

    Route::group(['prefix' => 'businesses'], function () {
        Route::get('home/{business}', ['as' => 'user.businesses.home', 'uses' => 'BusinessController@getHome']);
        Route::get('list', ['as' => 'user.businesses.list', 'uses' => 'BusinessController@getList']);
        Route::get('subscriptions', ['as' => 'user.businesses.subscriptions', 'uses' => 'BusinessController@getSubscriptions']);
    });

    Route::controller('wizard', 'WizardController', [
        'getWelcome' => 'wizard.welcome',
        'getPricing' => 'wizard.pricing',
        'getTerms'   => 'wizard.terms',
    ]);

    Route::resource('business.contact', 'BusinessContactController');
});

Route::group(['prefix' => 'manager', 'namespace' => 'Manager', 'middleware' => ['auth']], function () {

    Route::controller('appointment', 'BusinessAgendaController', [
        'postAction' => 'manager.business.agenda.action',
    ]);
    Route::controller('agenda/{business}', 'BusinessAgendaController', [
        'getIndex' => 'manager.business.agenda.index',
    ]);
    Route::post('search', function () {
        if (Session::get('selected.business')) {
            $search = new App\SearchEngine(Request::input('criteria'));
            $search->setBusinessScope([Session::get('selected.business')->id])->run();
            return view('manager.search.index')->with(['results' => $search->results()]);
        }
        return Redirect::route('user.businesses.list');
    });

    Route::get('business/{business}/preferences', ['as' => 'manager.business.preferences', 'uses' => 'BusinessController@getPreferences']);
    Route::post('business/{business}/preferences', ['as' => 'manager.business.preferences', 'uses' => 'BusinessController@postPreferences']);
    Route::resource('business', 'BusinessController');
    Route::get('business/{business}/contact/import', ['as' => 'manager.business.contact.import', 'uses' => 'BusinessContactImportExportController@getImport']);
    Route::post('business/{business}/contact/import', ['as' => 'manager.business.contact.import', 'uses' => 'BusinessContactImportExportController@postImport']);
    Route::resource('business.contact', 'BusinessContactController');
    Route::resource('business.service', 'BusinessServiceController');
    Route::resource('business.vacancy', 'BusinessVacancyController');
});

Route::group([ 'prefix'=> 'root', 'middleware' => ['auth', 'acl'], 'is'=> 'root'], function () {
    Route::controller('dashboard', 'RootController', [
        'getIndex' => 'root.dashboard',
    ]);
    Route::get('sudo/{userId}', function ($userId) {
        Auth::loginUsingId($userId);
        Log::warning("[!] ROOT SUDO userId:$userId");
        Flash::warning('!!! ADVICE THIS FOR IS AUTHORIZED USE ONLY !!!');
        return Redirect::route('user.businesses.list');
    })->where('userId', '\d*');
});

Route::get('lang/{lang}', ['as'=>'lang.switch', 'uses'=>'LanguageController@switchLang']);

Route::controllers([
    'auth' => 'Auth\AuthController',
    'password' => 'Auth\PasswordController',
]);

Route::get('social/login/redirect/{provider}', ['uses' => 'Auth\OAuthController@redirectToProvider', 'as' => 'social.login']);
Route::get('social/login/{provider}', 'Auth\OAuthController@handleProviderCallback');

Route::get('home', ['as' => 'home', 'uses' => 'User\WizardController@getHome']);

Route::get('/', 'WelcomeController@index');

Route::get('{business_slug}', function ($business_slug) {
    if ($business_slug->isEmpty()) {
        Flash::warning(trans('user.businesses.list.alert.not_found'));
        return Redirect::route('user.businesses.list');
    } else {
        return Redirect::route('user.businesses.home', $business_slug->first()->id);
    }
})->where('business_slug', '[^_]+.*'); /* Underscore starter is reserved for debugging facilities */
