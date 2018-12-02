@extends('frontend.layout.layout')
@section('main_body')
<div class="col-md-12">
<div class="brb-heading">{{ __('registration.Edit Profile') }}</div>
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
   <form action="{{ route('front-post-profile') }}" method="POST" enctype="multipart/form-data">
      {{ csrf_field() }}
      <div class="row">
         <div class="col-md-6">
            <div class="profile_image">
               <img src="{{ asset(Session::get('user_details')['profile_image']) }}" class="img-responsive" />
            </div>
            <div class="form-group choose_img">
               <label for="full_name">{{ __('registration.Choose Profile Image') }}</label>
               <input name="profile_image" type="file" value="{{ old('profile_image') }}"/>
            </div>
         </div>
         <div class="col-md-6">
            <div class="form-group">
               <label for="full_name">{{ __('registration.Enter Full Name') }}</label>
               <input class="form-control custom-text-input validate[required]" type="text" placeholder="" name="full_name" value="{{ old('full_name',$user_details->full_name) }}">
            </div>
            <div class="form-group">
               <label for="full_name">{{ __('registration.Enter Email') }}</label>
               <input class="form-control custom-text-input check_unique validate[required,custom[email]]" type="email" data-id="{{ $user_details->id }}" name="email" value="{{ old('email',$user_details->email) }}">
               <div id="error_email"></div>
            </div>
            <div class="form-group">
               <label for="full_name">{{ __('registration.Enter Mobile no.') }}</label>
               <input class="form-control custom-text-input validate[required,maxSize[10]]" type="text" placeholder="" name="mobile" value="{{ old('mobile',$user_details->mobile) }}">
            </div>
            <div class="form-group">
               <label for="full_name">{{ __('registration.Select Gender') }}</label>
               <input type="radio" name="gender" id="male" class="with-gap validate[required]" @if('male' == old('gender',$user_details->user_profile->sex)) checked @endif value="male">
               <label for="male">{{ __('registration.Male') }}</label>
               <input type="radio" name="gender" id="female" class="with-gap validate[required]" @if('female' == old('gender',$user_details->user_profile->sex)) checked @endif value="female">
               <label for="female" class="m-l-20">{{ __('registration.Female') }}</label>
            </div>
         </div>
      </div>
      <div class="row">
         <div class="col-md-6">
            <div class="form-group">
               <label for="full_name">{{ __('registration.Select Country') }}</label>
               <select name="country" class="form-control show-tick validate[required]" data-live-search="true" tabindex="-98">
                  <option value="">-- {{ __('registration.Select') }} --</option>
                  @foreach($country as $country_data)
                  <option value="{{ $country_data['id'] }}" @if($country_data['id'] == old('country',$user_details->user_profile->country_id)) selected @endif >{{ $country_data['name'] }}</option>
                  @endforeach
               </select>
            </div>
            <div class="form-group">
               <label for="full_name">{{ __('registration.Select Language') }}</label>
               <select name="language" class="form-control show-tick validate[required]" data-live-search="true" tabindex="-98">
                  <option value="">-- {{ __('registration.Select') }} --</option>
                  @foreach($language as $language_data)
                  <option @if($language_data['id'] == old('language',$user_details->user_profile->language_id)) selected @endif value="{{ $language_data['id'] }}">{{ $language_data['language'] }}</option>
                  @endforeach
               </select>
            </div>
            <div class="form-group">
               <label for="full_name">{{ __('registration.Select Currency') }}</label>
               <select name="currency" class="form-control show-tick validate[required]" data-live-search="true" tabindex="-98">
                  <option value="">-- {{ __('registration.Select') }} --</option>
                  @foreach($currency as $currency_data)
                  <option @if($currency_data['id'] == old('currency',$user_details->user_profile->currency_id)) selected @endif value="{{ $currency_data['id'] }}">{{ $currency_data['curency_name'] }}</option>
                  @endforeach
               </select>
            </div>
         </div>
         <div class="col-md-6">
            <div class="form-group form-float">
               <label for="full_name">{{__('registration.Enter Address')}}</label>
               <div class="form-line">
                  <textarea name="address" cols="30" rows="8" placeholder="" class="form-control no-resize validate[required]" value="">{{ old('address',$user_details->user_profile->address) }}</textarea>
               </div>
            </div>
         </div>
         <div class="col-md-12 profile_update">
            <div class="form-group text-center">
               <input class="btn btn-default btn-custom form-btn" type="submit" value="{{ __('registration.Update Button') }}">
            </div>
         </div>
      </div>
   </form>
</div>
</div>
@endsection
