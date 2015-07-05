@extends('app')

@section('css')
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">
@endsection

@section('content')
{!! Form::open(array('route' => 'user.booking.store', 'class' => 'form')) !!}
{!! Form::hidden('business_id', Session::get('selected.business_id'), array('required') ) !!}
<div class="container">
    <div class="row">
        <div class="form-group col-sm-4">
            {!! Form::label( trans('user.appointments.form.date.label') ) !!}
            {!! Form::date('_date', null, 
                array('required', 
                      'class'=>'form-control',
                      'id'=>'date',
                      'min'=> date('Y-m-d'),
                      'placeholder'=> trans('user.appointments.form.date.label') )) !!}
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

@section('footer_scripts')
    <script src="//code.jquery.com/jquery-1.10.2.js"></script>
    <script src="//code.jquery.com/ui/1.11.2/jquery-ui.js"></script>
    <script>
    $(function() {
      $( "#date" ).datepicker( { dateFormat: 'yy-mm-dd'} );
    });
    </script>
@endsection