<div class="tab_list">
	<ul class="home_tabs owl-carousel owl-theme">
		@foreach($sports_slide as $sport)
			<li class="<?php echo $sport->sport_name;?> <?php if($active_sport == $sport->sport_slug){echo 'active';}?>">
				<a href="{{ route('front-sports-get-all-league', ['sport_slug' => $sport->sport_slug ]) }}">
					<i class="icon"></i>
					<span>{{ __('sports.'.$sport->sport_name) }}</span>
				</a>
			</li>
		@endforeach
	</ul>
</div>
<script>
	// $(document).ready(function() {
	//   var owl = $('.owl-carousel');
	//   owl.owlCarousel({
	//   	dots: false,
	//   	items: 2,
	// 	loop: true,
	// 	nav: false,
	// 	margin: 5,
	// 	responsiveClass:true,
	//     responsive:{
	//         0:{
	// 	            items:1.5,
	// 	        },
	// 	        350:{
	// 	            items:1.75,
	// 	        },
	// 	        400:{
	// 	            items:2,
	// 	        },
	// 	        450:{
	// 	            items:2.25,
	// 	        },
	// 	        500:{
	// 	            items:2.50,
	// 	        },
	// 	        550:{
	// 	            items:2.75,
	// 	        },
	// 	        600:{
	// 	            items:3,
	// 	        },
	// 	        700:{
	// 	            items:3.5,
	// 	        },
	// 	        1000:{
	// 	            items:3.75,
	// 	        },
	// 	        1200:{
	// 	            items:4,
	// 	        }
	//     }
	//   });
	//   owl.on('mousewheel', '.owl-stage', function(e) {
	// 	if (e.deltaY > 0) {
	// 	  owl.trigger('next.owl');
	// 	}
	// 	else {
	// 	  owl.trigger('prev.owl');
	// 	}
	// 	e.preventDefault();
	//   });
	// })
</script>