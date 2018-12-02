@extends('frontend.layout.layout')
@section('main_body')
<div class="col-md-9">
   	<div class="Main_ods">
      	<!-- <div class="main_heading">Featured</div> -->
	  	@include('frontend.landing.slider')
	  	<div class="tab_one_content"><!-- Prematch Tab -->
	  		<div id="league_list_holder">
				@include('frontend.matches.match_list')
			</div>
	  	</div>
	  	<div id="live_match_list" class="tab2_form" style="display:none;" >
				@include('frontend.matches.live_match_list')
		</div>
   	</div>
</div>
@endsection
