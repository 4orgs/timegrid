<?php

namespace App;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Contact;
use App\Models\Service;
use App\Models\Business;

interface BookingStrategyInterface
{
    public function generateAppointment(
        User $issuer,
        Business $business,
        Contact $contact,
        Service $service,
        Carbon $datetime,
        $comments = null
    );
}
