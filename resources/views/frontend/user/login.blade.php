@extends('frontend.layout.layout')
	@section('main_body')
		<div class="body-rounded-bx loginbox">
			<div class="brb-heading">{{ __('label.Login') }}</div>
			<div class="form-container">
				@if (session('alert_msg'))
				   <div class="alert alert-{{ session('alert_class') }} alert-dismissible">
						<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">x</span></button>
						<strong>{{ session('alert_msg') }}</strong>
				   </div>
			   @endif
				<form action="{{ route('front-post-login') }}" method="POST" >
					{{ csrf_field() }}
					<div class="form-group">
						<input class="form-control custom-text-input validate[required]" type="text" name="username" placeholder="{{ __('login.Username') }}">
					</div>

					<div class="form-group">
						<input class="form-control custom-text-input validate[required]" type="password" name="password" placeholder="{{ __('login.Password') }}">
					</div>

					<div class="form-group text-center">
						<input class="btn btn-default btn-custom form-btn" type="submit" value="{{ __('label.Login') }}">
					</div>

					<div class="form-group text-center">
						<a class="forget_password_button" href="javascript:void(0);">{{ __('login.Forgot your password?') }}</a>
					</div>

					<div class="form-group last text-center">
						{{ __('login.Not a Member?') }} <a class="lightblue" href="{{ route('front-get-registration') }}">{{ __('login.Register Now!') }}</a>
					</div>

				</form>
			</div>
		</div>
	@endsection
@section('script')
<script type="text/javascript">
$('.forget_password_button').click(function() {
	$.confirm({
		title: '{{__('login.Forgot your password?')}}',
		type: 'blue',
		theme:'material',
		content: '' +
		'<form action="" class="formName">' +
		'<div class="form-group">' +
		'<input type="text" placeholder="{{__('registration.Enter Email')}}" class="send_email form-control" required />' +
		'</div>' +
		'</form>',
		buttons: {
			formSubmit: {
				text: 'Submit',
				btnClass: 'btn-blue',
				action: function () {
					var send_email = this.$content.find('.send_email').val();
					var regex = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
					if(!send_email || !regex.test(send_email)){
						showError('{{__('login.Enter valid Email')}}');
						return false;
					}
				   $.ajax({
			   		headers: {
			   		        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			   		},
			   		type : 'POST',
					async : false,
			   		data : {'email':send_email},
			      		url  : "{{ route('front-send-forgot-password-link')}}",
			      		success: function(data){
							if(parseInt(data) < 1){
								showError('{{__('login.Invalid email address. Try again')}}');
							}else {
								showSuccess('{{__('login.An email has been sent to your account!')}}');
							}
			      		}
			      	});
				}
			},
			cancel: function () {
				//close
			},
		},
	});
});
</script>
@endsection
