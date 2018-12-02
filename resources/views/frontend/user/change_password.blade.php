@extends('frontend.layout.layout')
	@section('main_body')
		<div class="body-rounded-bx loginbox">
			<div class="brb-heading">{{ __('label.Change Password') }}</div>
			<div class="form-container">
				@if (session('alert_msg'))
				   <div class="alert alert-{{ session('alert_class') }} alert-dismissible">
						<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">x</span></button>
						<strong>{{ session('alert_msg') }}</strong>
				   </div>
			   @endif
				<form action="{{ route('front-post-change-password') }}" method="POST" >
					{{ csrf_field() }}
					<div class="form-group">
						<input class="form-control custom-text-input validate[required,minSize[8]]" id="old_password" type="password" placeholder="{{ __('registration.Enter Old Password') }}" name="old_password"  value="">
					</div>

					<div class="form-group">
						<input class="form-control custom-text-input validate[required,minSize[8]]" id="password" type="password" placeholder="{{ __('registration.Enter Password') }}" name="password"  value="">
					</div>

					<div class="form-group">
						<input class="form-control custom-text-input validate[required ,equals[password]]" type="password" placeholder="{{ __('registration.Enter Password Again') }}" name="confirm_password" value="">
					</div>

					<div class="form-group text-center">
						<input class="btn btn-default btn-custom form-btn" type="submit" value="{{ __('label.Change Password') }}">
					</div>

				</form>
			</div>
		</div>
	@endsection
