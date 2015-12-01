<?php

namespace App\Http\Controllers\Manager;

use Gate;
use App\Models\Contact;
use App\Models\Business;
use Laracasts\Flash\Flash;
use App\Http\Controllers\Controller;
use App\Http\Requests\ContactFormRequest;

class BusinessContactController extends Controller
{
    /**
     * index of Contacts for Business
     *
     * @param  Business $business Business that holds the Contacts
     * @return Response           Rendered view of Contact addressbook
     */
    public function index(Business $business)
    {
        $this->log->info(__METHOD__);
        $this->log->info(sprintf("  businessId:%s", $business->id));

        if (Gate::denies('manageContacts', $business)) {
            abort(403);
        }

        return view('manager.contacts.index', compact('business'));
    }

    /**
     * create Contact
     *
     * @param  Business           $business Business that will hold the Contact
     * @param  ContactFormRequest $request  Contact form Request
     * @return Response                     Rendered form for Contact creation
     */
    public function create(Business $business, ContactFormRequest $request)
    {
        $this->log->info(__METHOD__);
        $this->log->info(sprintf("  businessId:%s", $business->id));

        if (Gate::denies('manageContacts', $business)) {
            abort(403);
        }

        return view('manager.contacts.create', compact('business'));
    }

    /**
     * store Contact
     *
     * @param  Business           $business Business that will hold the Contact
     * @param  ContactFormRequest $request  Contact form Request
     * @return Response                     Rendered view or Redirect
     */
    public function store(Business $business, ContactFormRequest $request)
    {
        $this->log->info(__METHOD__);
        $this->log->info(sprintf("  businessId:%s", $business->id));

        if (Gate::denies('manageContacts', $business)) {
            abort(403);
        }

        //////////////////
        // FOR REFACTOR //
        //////////////////

        if (trim($request->input('nin'))) {
            $existing_contacts = Contact::whereNotNull('nin')->where(['nin' => $request->input('nin')])->get();
            
            foreach ($existing_contacts as $existing_contact) {
                $this->log->info("  [ADVICE] Found existing contactId:{$existing_contact->id}");
                
                if ($existing_contact->isSubscribedTo($business)) {
                    $this->log->info("  [ADVICE] Existing contactId:{$existing_contact->id} is already linked to businessId:{$business->id}");
                    Flash::warning(trans('manager.contacts.msg.store.warning_showing_existing_contact'));
                    return redirect()->route('manager.business.contact.show', [$business, $existing_contact]);
                }
            }
        }

        $contact = Contact::create($request->except('notes', '_token'));
        $business->contacts()->attach($contact);
        $business->save();
        $this->log->info("  Contact created contactId:{$contact->id}");

        $contact->business($business)->pivot->update(['notes' => $request->get('notes')]);

        Flash::success(trans('manager.contacts.msg.store.success'));
        return redirect()->route('manager.business.contact.show', [$business, $contact]);
    }

    /**
     * show Contact
     *
     * @param  Business           $business Business holding the Contact
     * @param  Contact            $contact  Contact to show
     * @param  ContactFormRequest $request  Contact form Request
     * @return Response                     Rendered view of Contact show
     */
    public function show(Business $business, Contact $contact)
    {
        $this->log->info(__METHOD__);
        $this->log->info(sprintf("  businessId:%s contactId:%s", 
                                    $business->id,
                                    $contact->id
                                ));

        if (Gate::denies('manageContacts', $business)) {
            abort(403);
        }

        return view('manager.contacts.show', compact('business', 'contact'));
    }

    /**
     * edit Contact
     *
     * @param  Business           $business Business holding the Contact
     * @param  Contact            $contact  Contact to edit
     * @return Response                     Rendered view of edit form
     */
    public function edit(Business $business, Contact $contact)
    {
        $this->log->info(__METHOD__);
        $this->log->info(sprintf("  businessId:%s contactId:%s", 
                                    $business->id,
                                    $contact->id
                                ));

        if (Gate::denies('manageContacts', $business)) {
            abort(403);
        }

        return view('manager.contacts.edit', compact('business', 'contact'));
    }

    /**
     * update Contact
     *
     * @param  Business           $business Business holding the Contact
     * @param  Contact            $contact  Contact to update
     * @param  ContactFormRequest $request  Contact form Request
     * @return Response                     Redirect to updated Contact show
     */
    public function update(Business $business, Contact $contact, ContactFormRequest $request)
    {
        $this->log->info(__METHOD__);
        $this->log->info(sprintf("  businessId:%s contactId:%s", 
                                    $business->id,
                                    $contact->id
                                ));

        if (Gate::denies('manageContacts', $business)) {
            abort(403);
        }

        //////////////////
        // FOR REFACTOR //
        //////////////////

        $contact->update([
            'firstname'       => $request->get('firstname'),
            'lastname'        => $request->get('lastname'),
            'email'           => $request->get('email'),
            'nin'             => $request->get('nin'),
            'gender'          => $request->get('gender'),
            'birthdate'       => $request->get('birthdate'),
            'mobile'          => $request->get('mobile'),
            'mobile_country'  => $request->get('mobile_country'),
        ]);

        $contact->business($business)->pivot->update(['notes' => $request->get('notes')]);

        Flash::success(trans('manager.contacts.msg.update.success'));
        return redirect()->route('manager.business.contact.show', [$business, $contact]);
    }

    /**
     * destroy Contact
     *
     * @param  Business           $business Business holding the Contact
     * @param  Contact            $contact  Contact to destroy
     * @return Response                     Redirect back to Business dashboard
     */
    public function destroy(Business $business, Contact $contact)
    {
        $this->log->info(__METHOD__);
        $this->log->info(sprintf("  businessId:%s contactId:%s", 
                                    $business->id,
                                    $contact->id
                                ));

        //////////////////
        // FOR REFACTOR //
        //////////////////

        $contact->businesses()->detach($business->id);

        if (Gate::denies('manageContacts', $business)) {
            abort(403);
        }

        Flash::success(trans('manager.contacts.msg.destroy.success'));
        return redirect()->route('manager.business.contact.index', $business);
    }
}
