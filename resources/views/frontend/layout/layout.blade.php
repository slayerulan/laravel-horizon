@include('frontend.layout.header_link')
@include('frontend.layout.header')
	<div class="main-d-body logmb regp">
		<div id="app-vue" class="container">
			<div class="row">
				@if(isset($sports_bar))
					@include('frontend.layout.left_bar')
				@endif
				@yield('main_body')
			</div>
			@include('frontend.betSlip.bet_slip')
		</div>
	</div>
@include('frontend.layout.footer')
