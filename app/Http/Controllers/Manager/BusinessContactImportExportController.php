<?php

namespace App\Http\Controllers\Manager;

use Flash;
use Request;
use Notifynder;
use App\Models\Contact;
use App\Models\Business;
use App\Http\Controllers\Controller;

class BusinessContactImportExportController extends Controller
{
    /**
     * get Import form
     *
     * @param  Business $business Business to import Contacts to
     * @param  Request  $request
     * @return Response           Rendered Import form view
     */
    public function getImport(Business $business, Request $request)
    {
        $this->log->info("BusinessContactImportExportController: getImport: businessId:{$business->id}");
        return view('manager.contacts.import', compact('business'));
    }

    /**
     * post Import
     *
     * @param  Business $business Business to import Contacts to
     * @param  Request  $request  Submitted form data
     * @return Response           Redirect to Business addressbook
     */
    public function postImport(Business $business, Request $request)
    {
        $this->log->info("BusinessContactImportExportController: postImport: businessId:{$business->id}");
        $csv = $this->csvToArray(Request::get('data'));
        
        foreach ($csv as $import) {
            $import = array_map(function ($item) { return $item == 'NULL' ? null : $item; }, $import);

            if ($import['birthdate'] !== null) {
                $date = \DateTime::createFromFormat('Ymd', $import['birthdate']);
                $import['birthdate'] = $date->format('m/d/Y');
            }

            $notes = $import['notes'];
            unset($import['notes']);
            $contact = Contact::create($import);
            $business->contacts()->attach($contact, ['notes' => $notes]);
            $business->save();
        }

        $count = count($csv);
        $this->log->info("BusinessContactImportExportController: Imported $count contacts to businessId:{$business->id}");

        Notifynder::category('user.importedContacts')
                   ->from('App\Models\User', auth()->user()->id)
                   ->to('App\Models\Business', $business->id)
                   ->url('http://localhost')
                   ->extra(compact('count'))
                   ->send();

        Flash::success(trans('manager.contacts.msg.import.success'));
        return redirect()->route('manager.business.contact.index', [$business]);
    }

    /**
     * TODO: Should probably be moved as helper
     *
     * csvToArray
     *
     *      Converts submitted CSV string data into an Array
     *
     * @param  string $data      CSV string of Contacts
     * @return array             Converted CSV into Array
     */
    private function csvToArray($data='')
    {
        $this->log->info("BusinessContactImportExportController: csvToArray");

        $rows = array_map('str_getcsv', explode("\n", $data));
        $header = array_shift($rows);
        $csv = array();
        foreach ($rows as $row) {
            $csv[] = array_combine($header, $row);
        }
        return $csv;
    }
}
