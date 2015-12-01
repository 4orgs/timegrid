@extends('app')

@section('content')
<div class="container">
    <div class="row">

    {!! Alert::info(trans('manager.services.create.instructions')) !!}

            <div class="panel panel-default">
                <div class="panel-heading">{{ trans('manager.services.create.title') }}</div>

                <div class="panel-body">
                    @include('_errors')

                    {!! Form::model(new App\Models\Service, ['route' => ['manager.business.service.store', $business]]) !!}
                        @include('manager.businesses.services._form',['submitLabel' => trans('manager.services.btn.store')])
                    {!! Form::close() !!}
                </div>
            </div>
    </div>
</div>
@endsection