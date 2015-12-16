<?php

namespace App\Models;

use App\Presenters\AppointmentPresenter;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use McCool\LaravelAutoPresenter\HasPresenter;

class Appointment extends EloquentModel implements HasPresenter
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['issuer_id', 'contact_id', 'business_id', 'service_id', 'start_at', 'finish_at', 'duration',
        'comments'];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id', 'hash', 'status', 'vacancy_id'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['start_at', 'finish_at'];

    /**
     * Appointment Hard Status Constants.
     */
    const STATUS_RESERVED = 'R';
    const STATUS_CONFIRMED = 'C';
    const STATUS_ANNULATED = 'A';
    const STATUS_SERVED = 'S';

    ///////////////
    // PRESENTER //
    ///////////////

    /**
     * get presenter.
     *
     * @return AppointmentPresenter Presenter class
     */
    public function getPresenterClass()
    {
        return AppointmentPresenter::class;
    }

    /**
     * Save the model to the database.
     *
     * @param array $options
     *
     * @return bool
     */
    public function save(array $options = [])
    {
        $this->doHash();

        return parent::save($options);
    }

    ///////////////////
    // Relationships //
    ///////////////////

    /**
     * Issuer.
     *
     * @return Collection $user User who has created the appointment
     */
    public function issuer()
    {
        return $this->belongsTo('App\Models\User');
    }

    /**
     * Contact.
     *
     * @return Collection $contact Contact for whom the appointment is made
     */
    public function contact()
    {
        return $this->belongsTo('App\Models\Contact');
    }

    /**
     * Business.
     *
     * @return Collection $business Business for which the appointment is made
     */
    public function business()
    {
        return $this->belongsTo('App\Models\Business');
    }

    /**
     * Service.
     *
     * @return Collection $service Service for which the contact is set for appointment
     */
    public function service()
    {
        return $this->belongsTo('App\Models\Service');
    }

    /**
     * Vacancy.
     *
     * @return Collection $vacancy Vacancy that holds the appointment
     */
    public function vacancy()
    {
        return $this->belongsTo('App\Models\Vacancy');
    }

    ///////////
    // Other //
    ///////////

    /**
     * User.
     *
     * @return [type] [description]
     */
    public function user()
    {
        return $this->contact->user;
    }

    /**
     * Duplicates.
     *
     * @return bool Determines if the new Appointment will hash crash with an existing Appointment
     */
    public function duplicates()
    {
        return !self::where('hash', $this->hash)->get()->isEmpty();
    }

    ///////////////
    // Accessors //
    ///////////////

    public function getHashAttribute()
    {
        return isset($this->attributes['hash']) ? $this->attributes['hash'] : $this->doHash();
    }

    /**
     * FinishAt.
     *
     * @return Carbon Calculates the start_at time plus duration in minutes
     */
    public function getFinishAtAttribute()
    {
        if ($this->attributes['finish_at'] !== null) {
            return $this->attributes['finish_at'];
        }

        if (is_numeric($this->duration)) {
            return $this->start_at->addMinutes($this->duration);
        }

        return $this->start_at;
    }

    /**
     * TimeZone.
     *
     * @return string The TimeZone set for Business
     */
    public function getTZAttribute()
    {
        return $this->business->timezone;
    }

    /**
     * Get the human readable status name.
     *
     * @return string The name of the current Appointment status
     */
    public function getStatusLabelAttribute()
    {
        $labels = [
            Self::STATUS_RESERVED  => 'reserved',
            Self::STATUS_CONFIRMED => 'confirmed',
            Self::STATUS_ANNULATED => 'annulated',
            Self::STATUS_SERVED    => 'served',
            ];

        return array_key_exists($this->status, $labels) ? $labels[$this->status] : '';
    }

    /**
     * Date.
     *
     * @return string Formatted Date string from the start_at attribute in UTC
     */
    public function getDateAttribute()
    {
        return $this->start_at->timezone('UTC')->toDateString();
    }

    //////////////
    // Mutators //
    //////////////

    /**
     * do Hash.
     *
     * @return string MD5 hash for unique id
     */
    public function doHash()
    {
        return $this->attributes['hash'] = md5(
            $this->start_at.'/'.
            $this->contact_id.'/'.
            $this->business_id.'/'.
            $this->service_id
        );
    }

    /**
     * Set start_at attribute.
     *
     * @param Carbon $datetime The Appointment starting datetime
     */
    public function setStartAtAttribute(Carbon $datetime)
    {
        $this->attributes['start_at'] = $datetime;
    }

    /**
     * Set finish_at attribute.
     *
     * @param Carbon $datetime The Appointment finishing datetime
     */
    public function setFinishAtAttribute(Carbon $datetime)
    {
        $this->attributes['finish_at'] = $datetime;
    }

    /**
     * Set Comments.
     *
     * @param string $comments User comments for the Business owner on the Appointment
     */
    public function setCommentsAttribute($comments)
    {
        $this->attributes['comments'] = trim($comments) ?: null;
    }

    /////////////////
    // HARD STATUS //
    /////////////////

    /**
     * is Reserved.
     *
     * @return bool Determination if the Appointment is in reserved status
     */
    public function isReserved()
    {
        return $this->status == Self::STATUS_RESERVED;
    }

    ///////////////////////////
    // Calculated attributes //
    ///////////////////////////

    /**
     * Appointment Status Workflow.
     *
     * Hard Status: Those concrete values stored in DB
     * Soft Status: Those values calculated from stored values in DB
     *
     * Suggested transitions (Binding is not mandatory)
     *     Reserved -> Confirmed -> Served
     *     Reserved -> Served
     *     Reserved -> Annulated
     *     Reserved -> Confirmed -> Annulated
     *
     * Soft Status
     *     (Active)   [ Reserved  | Confirmed ]
     *     (InActive) [ Annulated | Served    ]
     */

    /**
     * is Active.
     *
     * @return bool Determination if the Appointment is in an active status
     */
    public function isActive()
    {
        return $this->status == Self::STATUS_CONFIRMED || $this->status == Self::STATUS_RESERVED;
    }

    /**
     * is Pending.
     *
     * @return bool is Active AND is Future
     */
    public function isPending()
    {
        return $this->isActive() && $this->isFuture();
    }

    /**
     * is Future.
     *
     * @return bool The start_at datetime is future from the current system datetime
     */
    public function isFuture()
    {
        return !$this->isDue();
    }

    /**
     * is Due.
     *
     * @return bool The start_at datetime is past from the current system datetime
     */
    public function isDue()
    {
        return $this->start_at->isPast();
    }

    ////////////
    // Scopes //
    ////////////

    /////////////////////////
    // Hard Status Scoping //
    /////////////////////////

    /**
     * Scope to Unarchived Appointments.
     *
     * @param Illuminate\Database\Query $query
     *
     * @return Illuminate\Database\Query Scoped query
     */
    public function scopeUnarchived($query)
    {
        return $query
            ->where(function ($query) {
                $query->whereIn('status', [Self::STATUS_RESERVED, Self::STATUS_CONFIRMED])
                    ->where('start_at', '<=', Carbon::parse('today midnight')->timezone('UTC'))
                    ->orWhere(function ($query) {
                        $query->where('start_at', '>=', Carbon::parse('today midnight')->timezone('UTC'));
                    });
            });
    }

    /**
     * Scope to Served Appointments.
     *
     * @param Illuminate\Database\Query $query
     *
     * @return Illuminate\Database\Query Scoped query
     */
    public function scopeServed($query)
    {
        return $query->where('status', '=', Self::STATUS_SERVED);
    }

    /**
     * Scope to Annulated Appointments.
     *
     * @param Illuminate\Database\Query $query
     *
     * @return Illuminate\Database\Query Scoped query
     */
    public function scopeAnnulated($query)
    {
        return $query->where('status', '=', Self::STATUS_ANNULATED);
    }

    /////////////////////////
    // Soft Status Scoping //
    /////////////////////////

    /**
     * Scope to not Served Appointments.
     *
     * @param Illuminate\Database\Query $query
     *
     * @return Illuminate\Database\Query Scoped query
     */
    public function scopeUnServed($query)
    {
        return $query->where('status', '<>', Self::STATUS_SERVED);
    }

    /**
     * Scope to Active Appointments.
     *
     * @param Illuminate\Database\Query $query
     *
     * @return Illuminate\Database\Query Scoped query
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', [Self::STATUS_RESERVED, Self::STATUS_CONFIRMED]);
    }

    /////////////
    // Sorting //
    /////////////

    /**
     * Oldest first.
     *
     * @param Illuminate\Database\Query $query
     *
     * @return Illuminate\Database\Query Scoped query
     */
    public function scopeOldest($query)
    {
        return $query->orderBy('start_at', 'ASC');
    }

    /**
     * Of Business.
     *
     * @param Illuminate\Database\Query $query
     * @param int                       $businessId An inquired business to validate against
     *
     * @return Illuminate\Database\Query The appointments belonging to the inquired Business as holder
     */
    public function scopeOfBusiness($query, $businessId)
    {
        return $query->where('business_id', '=', $businessId);
    }

    /**
     * Of Date.
     *
     * @param Illuminate\Database\Query $query
     * @param Carbon                    $date  An inquired date to validate against
     *
     * @return Illuminate\Database\Query The scoped appointments for the inquired date
     */
    public function scopeOfDate($query, Carbon $date)
    {
        return $query->whereRaw('date(`start_at`) = ?', [$date->timezone('UTC')->toDateString()]);
    }

    /**
     * Scope only future appointments.
     *
     * @param Illuminate\Database\Query $query
     *
     * @return Illuminate\Database\Query The appointments scoped for future date
     */
    public function scopeFuture($query)
    {
        return $query->where('start_at', '>=', Carbon::parse('today midnight')->timezone('UTC'));
    }

    /**
     * Scope only till date.
     *
     * @param Illuminate\Database\Query $query
     * @param Carbon                    $date  Inquired range end date
     *
     * @return Illuminate\Database\Query Scoped appointments up to the inquired date
     */
    public function scopeTillDate($query, Carbon $date)
    {
        return $query->where('start_at', '<=', $date->timezone('UTC'));
    }

    /**
     * Between Dates.
     *
     * @param Illuminate\Database\Query $query
     * @param Carbon                    $startAt
     * @param Carbon                    $finishAt
     *
     * @return Illuminate\Database\Query The scoped appointments held between the inquired dates
     */
    public function scopeAffectingInterval($query, Carbon $startAt, Carbon $finishAt)
    {
        return $query->where(function ($query) use ($startAt, $finishAt) {
            $query->whereRaw('date(`finish_at`) >= ?', [$startAt->timezone('UTC')->toDateString()])
                  ->orWhereRaw('date(`start_at`) <= ?', [$finishAt->timezone('UTC')->toDateString()]);
        });
    }

    //////////////////////////
    // Soft Status Checkers //
    //////////////////////////

    /**
     * User is target contact of the appointment.
     *
     * @param  int  $userId
     * @return boolean
     */
    public function isTarget($userId)
    {
        return $this->contact->isProfileOf($userId);
    }

    /**
     * User is issuer of the appointment.
     *
     * @param  int  $userId
     * @return boolean
     */
    public function isIssuer($userId)
    {
        return $this->issuer->id == $userId;
    }

    /**
     * User is owner of business.
     *
     * @param  int  $userId
     * @return boolean
     */
    public function isOwner($userId)
    {
        return $this->business->owners->contains($userId);
    }

    /**
     * can be annulated by user.
     *
     * @param  int $userId
     * @return boolean
     */
    public function canAnnulate($userId)
    {
        return $this->isOwner($userId) ||
            ($this->isIssuer($userId) && $this->isOnTimeToAnnulate()) ||
            ($this->isTarget($userId) && $this->isOnTimeToAnnulate());
    }

    /**
     * Determine if it is still possible to annulate according business policy.
     *
     * @return boolean
     */
    public function isOnTimeToAnnulate()
    {
        $graceHours = $this->business->pref('appointment_annulation_pre_hs');

        $diff = $this->start_at->diffInHours(Carbon::now());
        
        return intval($diff) >= intval($graceHours);
    }

    /**
     * can Serve.
     *
     * @param  int $userId
     * @return boolean
     */
    public function canServe($userId)
    {
        return $this->isOwner($userId);
    }

    /**
     * can confirm.
     *
     * @param  int $userId
     * @return boolean
     */
    public function canConfirm($userId)
    {
        return $this->isIssuer($userId) || $this->isOwner($userId);
    }

    /**
     * is Serveable by user.
     * @param  int  $userId
     * @return boolean
     */
    public function isServeableBy($userId)
    {
        return $this->isServeable() && $this->canServe($userId);
    }

    /**
     * is Confirmable By user.
     * @param  int  $userId
     * @return boolean
     */
    public function isConfirmableBy($userId)
    {
        return $this->isConfirmable() && $this->shouldConfirmBy($userId) && $this->canConfirm($userId);
    }

    /**
     * is Annulable By user.
     * @param  int  $userId
     * @return boolean
     */
    public function isAnnulableBy($userId)
    {
        return $this->isAnnulable() && $this->canAnnulate($userId);
    }

    /**
     * Determine if the queried userId may confirm the appointment or not.
     *
     * @param  int $userId
     * @return boolean
     */
    public function shouldConfirmBy($userId)
    {
        return ($this->isSelfIssued() && $this->isOwner($userId)) ||
               ($this->isOwner($this->issuer->id) && $this->isIssuer($userId));
    }

    /**
     * Determine if the target contact user is the same of the appointment issuer user.
     *
     * @return boolean
     */
    public function isSelfIssued()
    {
        if (!$this->issuer) {
            return false;
        }
        if (!$this->contact) {
            return false;
        }
        if (!$this->contact->user) {
            return false;
        }

        return $this->issuer->id == $this->contact->user->id;
    }

    /**
     * is Serveable.
     *
     * @return bool The Serve action can be performed
     */
    public function isServeable()
    {
        return $this->isActive() && $this->isDue();
    }

    /**
     * is Confirmable.
     *
     * @return bool The Confirm action can be performed
     */
    public function isConfirmable()
    {
        return $this->status == self::STATUS_RESERVED && $this->isFuture();
    }

    /**
     * is Annulable.
     *
     * @return bool The Annulate action can be performed
     */
    public function isAnnulable()
    {
        return $this->isActive();
    }

    /////////////////////////
    // Hard Status Actions //
    /////////////////////////

    /**
     * Check and perform Confirm action.
     *
     * @return Appointment The changed Appointment
     */
    public function doReserve()
    {
        if ($this->status === null) {
            $this->status = self::STATUS_RESERVED;
        }

        return $this;
    }

    /**
     * Check and perform Confirm action.
     *
     * @return Appointment The changed Appointment
     */
    public function doConfirm()
    {
        if ($this->isConfirmable()) {
            $this->status = self::STATUS_CONFIRMED;

            return $this->save();
        }

        return $this;
    }

    /**
     * Check and perform Annulate action.
     *
     * @return Appointment The changed Appointment
     */
    public function doAnnulate()
    {
        if ($this->isAnnulable()) {
            $this->status = self::STATUS_ANNULATED;

            return $this->save();
        }

        return $this;
    }

    /**
     * Check and perform Serve action.
     *
     * @return Appointment The changed Appointment
     */
    public function doServe()
    {
        if ($this->isServeable()) {
            $this->status = self::STATUS_SERVED;

            return $this->save();
        }

        return $this;
    }
}
