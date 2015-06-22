@extends('app')

@section('content')

<div class="container">
	<div class="row">
		<div class="col-md-10 col-md-offset-1">
			<div class="panel panel-default">
				<div class="panel-heading">{{ trans('app.home.biz.title') }}</div>

				<div class="panel-body">
					@include('flash::message')
				
					<table class="table table-condensed">
					@foreach ($businesses as $business)
						<tr>
							<td>{!! Button::danger()->withIcon(Icon::trash())->withAttributes(['data-method'=>'DELETE', 'data-confirm'=>'Delete?'])->asLinkTo(action('BusinessesController@destroy', $business)) !!}</td>
							<td>{!! Button::primary($business->slug)->asLinkTo(action('BusinessesController@show', $business)) !!}</td>
							<td>{{ $business->name }}</td>
							<td>{{ $business->description }}</td>
						</tr>
					@endforeach
					</table>
				</div>

				<div class="panel-footer">
					{!! DropdownButton::normal(trans('app.home.btn.actions'))->withContents([
								['url' => action('BusinessesController@index'),  'label' => trans('app.home.btn.manage_business')],
                ['url' => action('BusinessesController@create'), 'label' => trans('app.home.btn.manage_create')],
          ]) !!}
				</div>

			</div>
		</div>
	</div>
</div>

@endsection

@section('scripts')
<script type="text/javascript">
/*
<a href="posts/2" data-method="delete"> <---- We want to send an HTTP DELETE request

- Or, request confirmation in the process -

<a href="posts/2" data-method="delete" data-confirm="Are you sure?">
*/
 
(function() {
 
  var laravel = {
    initialize: function() {
      this.methodLinks = $('a[data-method]');
 
      this.registerEvents();
    },
 
    registerEvents: function() {
      this.methodLinks.on('click', this.handleMethod);
    },
 
    handleMethod: function(e) {
      var link = $(this);
      var httpMethod = link.data('method').toUpperCase();
      var form;
 
      // If the data-method attribute is not PUT or DELETE,
      // then we don't know what to do. Just ignore.
      if ( $.inArray(httpMethod, ['PUT', 'DELETE']) === - 1 ) {
        return;
      }
 
      // Allow user to optionally provide data-confirm="Are you sure?"
      if ( link.data('confirm') ) {
        if ( ! laravel.verifyConfirm(link) ) {
          return false;
        }
      }
 
      form = laravel.createForm(link);
      form.submit();
 
      e.preventDefault();
    },
 
    verifyConfirm: function(link) {
      return confirm(link.data('confirm'));
    },
 
    createForm: function(link) {
      var form = 
      $('<form>', {
        'method': 'POST',
        'action': link.attr('href')
      });
 
      var token = 
      $('<input>', {
        'type': 'hidden',
        'name': '_token',
          'value': '{{{ csrf_token() }}}' // hmmmm...
        });
 
      var hiddenInput =
      $('<input>', {
        'name': '_method',
        'type': 'hidden',
        'value': link.data('method')
      });
 
      return form.append(token, hiddenInput)
                 .appendTo('body');
    }
  };
 
  laravel.initialize();
 
})();
</script>
@endsection