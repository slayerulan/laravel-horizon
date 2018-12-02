@extends('frontend.layout.layout')
	@section('main_body')
		<div class="body-rounded-bx loginbox">
			<div class="brb-heading">{{ __('registration.Register') }}</div>
			<div class="form-container">
				@if ($errors->any())
			     <div class="alert alert-danger form_validation_error">
			      <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">x</span></button>
			      <ul class="error_list">
			       @foreach ($errors->all() as $error)
			           <li><i class="fa fa-error"></i> {{ $error }}</li>
			          @endforeach
			      </ul>
			     </div>
			     @endif
				 @if (session('alert_msg'))
		 			<div class="alert alert-{{ session('alert_class') }} alert-dismissible">
		 				 <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">x</span></button>
		 				 <strong>{{ session('alert_msg') }}</strong>
		 			</div>
		 		@endif
				 <form action="{{ route('front-post-registration') }}" method="POST" enctype="multipart/form-data">
					{{ csrf_field() }}
					<div class="form-group">
						<input class="form-control custom-text-input validate[required]" type="text" placeholder="{{ __('registration.Enter Full Name') }}" name="full_name" value="{{ old('full_name') }}">
					</div>
					<div class="form-group">
						<input class="form-control custom-text-input check_unique validate[required]" type="text" placeholder="{{ __('registration.Enter Username') }}" name="username" value="{{ old('username') }}">
						<div class="error_message" id="error_username"></div>
					</div>
					<div class="form-group">
						<input class="form-control custom-text-input validate[required,minSize[8]]" id="password" type="password" placeholder="{{ __('registration.Enter Password') }}" name="password"  value="">
					</div>
					<div class="form-group">
						<input class="form-control custom-text-input validate[required ,equals[password]]" type="password" placeholder="{{ __('registration.Renter Password') }}" name="confirm_password" value="">
					</div>
					<div class="form-group">
						<input class="form-control custom-text-input check_unique validate[required,custom[email]]" type="email" placeholder="{{ __('registration.Enter Email') }}" name="email" value="{{ old('email') }}">
						<div class="error_message" id="error_email"></div>
					</div>
					<div class="form-group">
						<input class="form-control custom-text-input validate[required,maxSize[10]]" type="text" placeholder="{{ __('registration.Enter Mobile no.') }}" name="mobile" value="{{ old('mobile') }}">
					</div>
					<div class="form-group">
						<input type="radio" name="gender" id="male" class="with-gap validate[required]" checked value="male">
						<label for="male">{{ __('registration.Male') }}</label>

						<input type="radio" name="gender" id="female" class="with-gap validate[required]" value="female">
						<label for="female" class="m-l-20">{{ __('registration.Female') }}</label>
					</div>
					<div class="form-group form-float">
						<div class="form-line">
							<textarea name="address" cols="30" rows="5" placeholder="{{__('registration.Address')}}" class="form-control no-resize validate[required]" value="">{{ old('address') }}</textarea>
						</div>
					</div>
					<div class="form-group">
						<input class="form-control custom-text-input" name="basic_agent" type="text" placeholder="{{ __('registration.Enter Agent Code') }}" value="{{ old('basic_agent') }}">
					</div>
					<div class="form-group">
						<select name="country" class="form-control show-tick validate[required]" data-live-search="true" tabindex="-98">
							<option value="">-- {{ __('registration.Please Select Country') }} --</option>
							@foreach($country as $country_data)
							<option value="{{ $country_data['id'] }}" @if($country_data['id'] == old('country')) selected @endif >{{ $country_data['name'] }}</option>
							@endforeach
						</select>
					</div>

					<div class="form-group">
						<select name="language" class="form-control show-tick validate[required]" data-live-search="true" tabindex="-98">
							<option value="">-- {{ __('registration.Please Select Language') }} --</option>
							@foreach($language as $language_data)
							<option @if($language_data['id'] == old('language')) selected @endif value="{{ $language_data['id'] }}">{{ $language_data['language'] }}</option>
							@endforeach
						</select>
					</div>
					<div class="form-group">
						<select name="currency" class="form-control show-tick validate[required]" data-live-search="true" tabindex="-98">
							<option value="">-- {{ __('registration.Please Select Currency') }} --</option>
							@foreach($currency as $currency_data)
							<option @if($currency_data['id'] == old('currency')) selected @endif value="{{ $currency_data['id'] }}">{{ $currency_data['curency_name'] }}</option>
							@endforeach
						</select>
					</div>
					<div class="form-group">
						<input name="profile_image" type="file" value="{{ old('profile_image') }}"/>
					</div>

					<div class="form-group text-center">
						<input class="btn btn-default btn-custom form-btn" type="submit" value="{{ __('registration.Register Button') }}">
					</div>

					<div class="form-group last text-center">
						{{ __('registration.Already a member?') }} <a class="lightblue" href="{{ route('front-get-login') }}">{{ __('registration.LogIn Now!') }}</a>
					</div>

				</form>
			</div>
		</div>
	@endsection
