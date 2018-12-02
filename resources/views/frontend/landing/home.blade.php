@extends('frontend.layout.layout')
@section('main_body')
	<div class="col-md-9">
	   	<div class="Main_ods">
	      	{{--<div class="main_heading">Featured</div>--}}	      	
		  	@include('frontend.landing.slider')
			<div class="tab1_form"><!-- Prematch Tab -->
		  		<div class="tab_list">
					<ul class="home_tabs owl-carousel owl-theme">
						@foreach($sports_bar as $sport)
							<li class="home_sport_tab <?php echo $sport->sport_name;?> <?php if($sport_active == $sport->sport_slug){echo 'active';}?>" data-sport="{{$sport->sport_slug}}">
								<i class="icon"></i>
								<span>{{ __('sports.'.$sport->sport_name) }}</span>
							</li>
						@endforeach
					</ul>
				</div>
				<div id="home_match_list">
					@include('frontend.matches.home_page_listing')
				</div>
				<div id="small_loader" class="loder_cover "><img src="{{url('frontend/images/loader.gif')}}" ></div>
		  	</div>
			<div class="tab2_form" style="display:none;" >
				@include('frontend.matches.live_match_list')
			</div>
			<div id="small_loader" class="loder_cover "><img src="{{url('frontend/images/loader.gif')}}" ></div>
			
	   	</div>
	</div>

@endsection
@section('script')
	<script type="text/javascript">
		$(document.body).on('click','.home_sport_tab',function(){
			if ($(this).hasClass('active') != true) {
				var sport = $(this).attr('data-sport');
				$('.home_sport_tab').removeClass('active');
				$(this).addClass('active');
				$(document.body).find('#small_loader').addClass('show');
				$(document.body).find('#small_loader').fadeIn();
				$.ajax({
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					type : 'POST',
					data : {'sport': sport},
					url  : "{{ route('front-post-home-match') }} ",
					success: function(data){
						$('#home_match_list').html(data);
						$(document.body).find('#small_loader').fadeOut();
						$(document.body).find('#small_loader').removeClass('show');
						$(document.body).find('[data-toggle="tooltip"]').tooltip();
					}
				});
			}
		});
		$(document.body).on('click','.show_match', function() {
			var id = $(this).attr('for');
			$(id).prop('checked', true);
			setTimeout(function(){ $('#league_list_form').submit(); }, 40);
		});
	</script>

  
@endsection