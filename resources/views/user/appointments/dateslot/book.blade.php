@extends('app')

@section('content')
{!! Form::open(array('route' => 'user.booking.store', 'class' => 'form')) !!}
{!! Form::hidden('business_id', Session::get('selected.business_id'), array('required') ) !!}
<div class="container">
    <div class="row">
      @include('user.appointments.dateslot._timetable', ['vacancies' => $business->vacancies->groupBy('date')])
    </div>
</div>

<div id="extra" class="container hidden">
    <div class="row">
        <div class="form-group col-sm-4">
            {!! Form::hidden('_date', null,
                array('required',
                      'class'=>'form-control',
                      'id'=>'date',
                      'min'=> date('Y-m-d'),
                      'placeholder'=> trans('user.appointments.form.date.label') )) !!}
        </div>
    </div>
    <div class="row">
        <div class="form-group col-sm-11">
            {!! Form::hidden('service_id', '',
                array('required',
                      'id'=>'service',
                      'class'=>'form-control',
                      'placeholder'=> trans('user.appointments.form.service.label') )) !!}
        </div>
    </div>
    <div class="row">
        <div class="form-group col-sm-11">
            {!! Form::label( trans('user.appointments.form.comments.label') ) !!}
            {!! Form::text('comments', 'test',
                array('required',
                      'class'=>'form-control',
                      'placeholder'=> trans('user.appointments.form.comments.label') )) !!}
        </div>
    </div>
    <div class="row">
        <div class="form-group col-sm-11">
            {!! Button::primary(trans('manager.contacts.btn.store'))->submit() !!}
        </div>
    </div>
</div>
{!! Form::close() !!}
@endsection
