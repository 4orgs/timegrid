@extends('app')

@section('content')
<div class="container">
    <div class="row">

        {!! Alert::info(trans('manager.vacancies.edit.instructions')) !!}

        <div class="panel panel-default">

            <div class="panel-heading">{{ trans('manager.vacancies.edit.title') }}</div>

            <div class="panel-body">
                @include('_errors')

                @if($advanced)
                    {!! Form::open(['method' => 'post', 'route' => ['manager.business.vacancy.storeBatch', $business]]) !!}
                    @include('manager.businesses.vacancies._form_advanced', ['submitLabel' => trans('manager.businesses.vacancies.btn.update')])
                    {!! Form::close() !!}                
                @else
                    {!! Form::open(['method' => 'post', 'route' => ['manager.business.vacancy.store', $business]]) !!}
                    @include('manager.businesses.vacancies._form', ['submitLabel' => trans('manager.businesses.vacancies.btn.update')])
                    {!! Form::close() !!}
                @endif
            </div>

        </div>
    </div>
</div>
@endsection