<?php

namespace App\Services;

use App\Models\User;
use App\Models\Contact;
use App\Models\Business;
use App\Events\NewRegisteredContact;

/*******************************************************************************
 * Contact Service Layer
 ******************************************************************************/
class ContactService
{
    /**
     * [register description]
     *
     * @param  User     $user     [description]
     * @param  Business $business [description]
     * @param  [type]   $data     [description]
     * @return [type]             [description]
     */
    public static function register(User $user, Business $business, $data)
    {
        if (false === $contact = self::getExisting($user, $business, $data['nin'])) {
            $contact = Contact::create($data);
            $business->contacts()->attach($contact);

            logger()->info("Contact created contactId:{$contact->id}");

            if ($data['notes']) {
                $business->contacts()->find($contact->id)->pivot->update(['notes' => $data['notes']]);
            }
        }

        event(new NewRegisteredContact($contact));

        return $contact;
    }

    /**
     * [getExisting description]
     *
     * @param  User     $user     [description]
     * @param  Business $business [description]
     * @param  [type]   $nin      [description]
     * @return [type]             [description]
     */
    public static function getExisting(User $user, Business $business, $nin)
    {
        if (trim($nin) == '') {
            return false;
        }

        $existingContacts = Contact::whereNotNull('nin')->where(['nin' => $nin])->get();

        foreach ($existingContacts as $existingContact) {
            logger()->info("[ADVICE] Found existing contactId:{$existingContact->id}");

            if ($existingContact->isSubscribedTo($business->id)) {
                logger()->info('[ADVICE] Existing contact is already linked to business');
                
                return $existingContact;
            }
        }
        return false;
    }
}
