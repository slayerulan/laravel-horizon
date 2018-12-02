@extends('frontend.layout.layout')
@section('main_body')
<div class="col-md-9">
   	<div class="Main_ods">
      	<!-- <div class="main_heading">Featured</div> -->
	  	@include('frontend.landing.slider')
	  	<div class="tab_one_content"><!-- Prematch Tab -->
	  		@include('frontend.landing.sport_list')
			<form id="league_list_form" method="POST" action="{{ route('front-sports-post-show-matches', $sport_details->slug ) }}">
				{{ csrf_field() }}
				<div class="main_heading Highlights">
					<i class="{{ $sport_details->slug }}_sports_icon"></i>{{ __('sports.'.$sport_details->name) }}
					<select name="time_range" class="sort_by_time pull-right">
						<option value="{{ config('bet_settings.maximum_hour',24*7) }}">{{ __('label.All') }} </option>
						<option value="3">{{ __('label.3 hours') }} </option>
						<option value="6">{{ __('label.6 hours') }} </option>
						<option value="12">{{ __('label.12 hours') }} </option>
						<option value="24">{{ __('label.24 hours') }} </option>
						<option value="72">{{ __('label.72 hours') }} </option>
						
					</select>
				</div>
				<div id="league_list_holder">
					@include('frontend.landing.league_list')
				</div>
			</form>
	  	</div>
	  	<div id="live_match_list" class="tab2_form" style="display:none;" >
				@include('frontend.matches.live_match_list')
		</div>
   	</div>
</div>
@endsection
@section('script')
	<script type="text/javascript">
		$(document.body).find('[data-toggle="tooltip"]').tooltip();
		$(document.body).on('change','.sort_by_time',function(){
			var url			=	window.location.pathname;;
			var time_range	=	$(this).val();
			if(time_range.length){
				$(document.body).find('#small_loader').addClass('show');
				$(document.body).find('#small_loader').fadeIn();
				$.ajax({
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					type : 'POST',
					data : {'url':url, 'time_range': time_range},
					url  : "{{ route('front-sports-get-league-by-time') }} ",
					success: function(data){
						$('#league_list_holder').html(data);
						$(document.body).find('[data-toggle="tooltip"]').tooltip();
					}
				});
			}
		});
		// $(document.body).on('click','#league_form_submit', function(e) {
		// 	e.preventDefault();
		// 	var selected_league =	$('input[name="leagues[]"]:checked').length;
		// 	if(selected_league > {{ config('bet_settings.maximum_league_selection',10) }}){
		// 		showError('{{ __('alert_info.you can select maximum '.config('bet_settings.maximum_league_selection',10) .' leagues') }}');
		// 	} else if(selected_league == 0) {
		// 		showError('{{ __('alert_info.No League Selected') }}');
		// 	} else {
		// 		$('#league_list_form').submit();
		// 	}
		// });

		$(document.body).on('click','.show_match', function() {
			var id = $(this).attr('for');
			$(id).prop('checked', true);
			setTimeout(function(){ $('#league_list_form').submit(); }, 40);
		});
	</script>
@endsection
