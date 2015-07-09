<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Business extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = ['slug', 'name', 'description', 'timezone', 'strategy'];

    public function owners()
    {
        return $this->belongsToMany(config('auth.model'))->withTimestamps();
    }

    public function owner()
    {
        return $this->belongsToMany(config('auth.model'))->withTimestamps()->first();
    }

    public function contacts()
    {
        return $this->belongsToMany('App\Contact');
    }

    public function services()
    {
        return $this->hasMany('App\Service');
    }

    public function vacancies()
    {
        return $this->hasMany('App\Vacancy');
    }

    public function bookings()
    {
        return $this->hasMany('App\Appointment');
    }
}
