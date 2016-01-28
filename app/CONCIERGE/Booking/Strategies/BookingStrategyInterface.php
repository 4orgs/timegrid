<?php

namespace Concierge\Booking\Strategies;

use App\Models\Appointment;
use App\Models\Business;
use App\Models\Contact;
use App\Models\Service;
use App\Models\User;
use App\Models\Vacancy;
use Carbon\Carbon;
use Illuminate\Support\Collection;

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

    public function hasRoom(Appointment $appointment, Vacancy $vacancy);

    public function removeBookedVacancies(Collection $vacancies);

    public function removeSelfBooked(Collection $vacancies, User $user);
}
