<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
*/

///////////////
// API CALLS //
///////////////

Route::group(['prefix' => 'api', 'middleware' => ['auth']], function () {

    Route::controller('booking', 'BookingController', ['postAction' => 'api.booking.action']);

    Route::group(['prefix' => 'services'], function () {

        Route::get('list/{business}', function ($business) {
            return $business->services()->lists('name', 'id');
        });

        Route::get('duration/{service}', function ($service) {
            return $service->duration;
        });
    });
});

//////////////////
// ROOT CONTEXT //
//////////////////

Route::group([
    'prefix'=> 'root',
    'as' => 'root.',
    'namespace' => 'Root',
    'middleware' => ['auth', 'role:root']
    ],
    function () {
        Route::get('dashboard', ['as' => 'dashboard', 'uses' => 'RootController@getIndex']);

        Route::get('sudo/{userId}', ['as' => 'sudo', 'uses' => 'RootController@getSudo'])->where('userId', '\d*');
    });

///////////////////////
// LANGUAGE SWITCHER //
///////////////////////

Route::get('lang/{lang}', ['as'=>'lang.switch', 'uses'=>'LanguageController@switchLang']);

//////////////////
// REGULAR AUTH //
//////////////////

Route::controllers([
    'auth' => 'Auth\AuthController',
    'password' => 'Auth\PasswordController',
]);

/////////////////
// SOCIAL AUTH //
/////////////////

Route::get('social/login/redirect/{provider}', [
    'as' => 'social.login',
    'uses' => 'Auth\OAuthController@redirectToProvider'
    ]);

Route::get('social/login/{provider}', 'Auth\OAuthController@handleProviderCallback');

///////////////////////////
// PRIVATE HOME / WIZARD //
///////////////////////////

Route::get('home', ['as' => 'home', 'uses' => 'User\WizardController@getWizard']);

//////////////////
// USER CONTEXT //
//////////////////

Route::group(['prefix' => 'user', 'as' => 'user.', 'namespace' => 'User', 'middleware' => ['auth']], function () {

    //////////////
    // BOOKINGS //
    //////////////
    Route::group(['prefix' => 'booking', 'as' => 'booking.'], function () {

        Route::post('store', ['as' => 'store', 'uses' => 'AgendaController@postStore']);
        Route::get('bookings', ['as' => 'list', 'uses' => 'AgendaController@getIndex']);
        Route::get('book/{business}', ['as' => 'book', 'uses' => 'AgendaController@getAvailability']);
    });

    ////////////////
    // BUSINESSES //
    ////////////////
    Route::group(['prefix' => 'businesses', 'as' => 'businesses.'], function () {

        Route::get('home/{business}', ['as' => 'home', 'uses' => 'BusinessController@getHome']);
        Route::get('list', ['as' => 'list', 'uses' => 'BusinessController@getList']);
        Route::get('subscriptions', ['as' => 'subscriptions', 'uses' => 'BusinessController@getSubscriptions']);
    });

    ////////////
    // WIZARD //
    ////////////
    Route::group(['prefix' => 'wizard', 'as' => 'wizard.'], function () {
        Route::get('terms', ['as' => 'terms',   'uses' => 'WizardController@getTerms']);
        Route::get('wizard', ['as' => 'welcome', 'uses' => 'WizardController@getWelcome']);
        Route::get('pricing', ['as' => 'pricing', 'uses' => 'WizardController@getPricing']);
    });

});

/////////////////////////////////////////////////////////////
// USER RESOURCES                                          //
// ToDo: Needs to get moved into group as explicit routing //
/////////////////////////////////////////////////////////////

Route::group(['as' => 'user.', 'namespace' => 'User', 'middleware' => ['auth']], function () {

    Route::resource('business.contact', 'BusinessContactController');
});

/////////////////////
// MANAGER CONTEXT //
/////////////////////

Route::group(['prefix' => '{business}', 'namespace' => 'Manager', 'middleware' => ['auth']], function () {

    Route::get('preferences', [
        'as' => 'manager.business.preferences',
        'uses' => 'BusinessPreferencesController@getPreferences'
        ]);

    Route::post('preferences', [
        'as' => 'manager.business.preferences',
        'uses' => 'BusinessPreferencesController@postPreferences'
        ]);

    Route::get('agenda', ['as' => 'manager.business.agenda.index', 'uses' => 'BusinessAgendaController@getIndex']);

    Route::get('list', ['as' => 'manager.business.index', 'uses' => 'BusinessController@index']);
    Route::get('dashboard', ['as' => 'manager.business.show', 'uses' => 'BusinessController@show']);
    Route::get('create', ['as' => 'manager.business.create', 'uses' => 'BusinessController@create']);
    Route::post('', ['as' => 'manager.business.store', 'uses' => 'BusinessController@store']);
    Route::get('edit', ['as' => 'manager.business.edit', 'uses' => 'BusinessController@edit']);
    Route::put('', ['as' => 'manager.business.update', 'uses' => 'BusinessController@update']);
    Route::delete('', ['as' => 'manager.business.destroy', 'uses' => 'BusinessController@destroy']);

    Route::post('search', ['uses' => 'BusinessController@postSearch']);

    Route::get('contacts/import', [
        'as' => 'manager.business.contact.import',
        'uses' => 'BusinessContactImportExportController@getImport'
        ]);
    
    Route::post('contacts/import', [
        'as' => 'manager.business.contact.import',
        'uses' => 'BusinessContactImportExportController@postImport'
        ]);

    Route::get('contact', ['as' => 'manager.business.contact.index', 'uses' => 'BusinessContactController@index']);
    Route::get('contact/create', ['as' => 'manager.business.contact.create', 'uses' => 'BusinessContactController@create']);
    Route::post('contact', ['as' => 'manager.business.contact.store', 'uses' => 'BusinessContactController@store']);
    Route::get('contact/{contact}', ['as' => 'manager.business.contact.show', 'uses' => 'BusinessContactController@show']);
    Route::get('contact/{contact}/edit', ['as' => 'manager.business.contact.edit', 'uses' => 'BusinessContactController@edit']);
    Route::put('contact/{contact}', ['as' => 'manager.business.contact.update', 'uses' => 'BusinessContactController@update']);
    Route::delete('contact/{contact}', ['as' => 'manager.business.contact.destroy', 'uses' => 'BusinessContactController@destroy']);

    Route::get('service', ['as' => 'manager.business.service.index', 'uses' => 'BusinessServiceController@index']);
    Route::get('service/create', ['as' => 'manager.business.service.create', 'uses' => 'BusinessServiceController@create']);
    Route::post('service', ['as' => 'manager.business.service.store', 'uses' => 'BusinessServiceController@store']);
    Route::get('service/{service}', ['as' => 'manager.business.service.show', 'uses' => 'BusinessServiceController@show']);
    Route::get('service/{service}/edit', ['as' => 'manager.business.service.edit', 'uses' => 'BusinessServiceController@edit']);
    Route::put('service/{service}', ['as' => 'manager.business.service.update', 'uses' => 'BusinessServiceController@update']);
    Route::delete('service/{service}', ['as' => 'manager.business.service.destroy', 'uses' => 'BusinessServiceController@destroy']);
        
    Route::get('vacancy/create', ['as' => 'manager.business.vacancy.create', 'uses' => 'BusinessVacancyController@create']);
    Route::post('vacancy', ['as' => 'manager.business.vacancy.store', 'uses' => 'BusinessVacancyController@store']);

});

Route::get('{business}', ['as' => 'guest.business.home', 'uses' => 'Guest\BusinessController@getHome']);

/////////////////
// PUBLIC HOME //
/////////////////

Route::get('/', 'WelcomeController@index');

///////////////////////
// BUSINESS SELECTOR //
///////////////////////

#Route::get('{business_slug}', function ($business_slug) {
#    
#    if ($business_slug->isEmpty()) {
#        Flash::warning(trans('user.businesses.list.alert.not_found'));
#        return Redirect::route('user.businesses.list');
#    }
#
#    $context = Auth::check() ? 'user' : 'guest';
#
#    return Redirect::route("{$context}.business.home", $business_slug->first()->id);
#
#})->where('business_slug', '[^_]+.*'); /* Underscore starter is reserved for debugging facilities */
