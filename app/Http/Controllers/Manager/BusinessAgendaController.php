<?php

namespace App\Http\Controllers\Manager;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Redirect;
use Log;
use App\Business;
use App\Appointment;

class BusinessAgendaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function getIndex(Business $business)
    {
        $appointments = $business->bookings()->orderBy('start_at')->get();
        return view('manager.businesses.appointments.'.$business->strategy.'.index', compact('appointments'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function postAction(Request $request)
    {
        $businessId = $request->input('business');
        $appointmentId = $request->input('appointment');
        $action = $request->input('action');
        Log::info("postAction.request:[action:$action, business:$businessId, appointment:$appointmentId]");

        $appointment = Appointment::find($appointmentId);

        switch ($action) {
            case 'annulate':
                $appointment->doAnnulate();
                break;
            case 'confirm':
                $appointment->doConfirm();
                break;
            case 'serve':
                $appointment->doServe();
                break;
            default:
                # code...
                break;
        }
        Log::info("postAction.response:[newStatus:{$appointment->status}, htmlrow:{$appointment->widget()->fullrow()}]");
        return response()->json(['code' => 'OK', 'status' => $appointment->status, 'htmlrow' => $appointment->widget()->fullrow() ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
