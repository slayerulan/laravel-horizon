<div class="all_leauge">{{ __('user_label.Featured Matches of') }} {{ __('sports.'.$sport_details->name) }} >></div>
@include('frontend.matches.match_list')
<div class="all_leauge">{{ __('sports.'.$sport_details->name) }} {{ __('user_label.Leagues') }} >></div>
<form id="league_list_form" method="POST" action="{{ route('front-sports-post-show-matches', $sport_details->slug ) }}">
	{{ csrf_field() }}
	@include('frontend.landing.league_list')
</form>