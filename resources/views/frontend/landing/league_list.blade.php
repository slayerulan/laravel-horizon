<div class="ods_block">
   <div class="ods_block_heading">
	  <a href="javascript:void(0);">{{ __('user_label.Top Leagues') }}</a>
   </div>
   <div class="leagues_list_block">
	   @forelse ($sport_details->current_top_leagues['league_country'] as $league_id => $country_name)
		   <div class="leagues">
		   		<input id="top_league_{{ $league_id }}" class="league_list_checkbox" name="leagues[]" value="{{ $league_id }}" type="checkbox" style="display: none;">
		   		<label for="top_league_{{ $league_id }}" class="show_match" data-toggle="tooltip" title="{{$country_name.' : '.$sport_details->current_top_leagues['league_details'][$league_id] }}" data-placement="top">{{$country_name.' : '.$sport_details->current_top_leagues['league_details'][$league_id] }}</label>
		   	</div>
	   @empty
		   <div class="no_data_found leagues">{{ __('user_label.No League Found') }}</div>
	   @endforelse
   </div>
</div>
<div class="ods_block">
   <div class="ods_block_heading">
	  <a href="javascript:void(0);">{{ __('user_label.All Leagues') }}</a>
   </div>
   <div class="leagues_list_block">
	   @forelse ($sport_details->current_leagues['league_country'] as $league_id => $country_name)
		   <div class="leagues">
		   		<input id="league_{{ $league_id }}" class="league_list_checkbox" name="leagues[]" value="{{ $league_id }}" type="checkbox" style="display: none;">
		   		<label for="league_{{ $league_id }}" class="show_match" data-toggle="tooltip" title="{{$country_name.' : '.$sport_details->current_leagues['league_details'][$league_id] }}" data-placement="top">{{$country_name.' : '.$sport_details->current_leagues['league_details'][$league_id] }}</label>
		   </div>
	   @empty
		   <div class="no_data_found leagues">{{ __('user_label.No League Found') }}</div>
	   @endforelse
   </div>
</div>
@if (count($sport_details->current_top_leagues['league_details']) || count($sport_details->current_leagues['league_details']))
	<!-- <div class="col-md-12 profile_update">
		<div class="form-group text-center">
			<input id="league_form_submit" class="btn btn-default btn-custom form-btn" value="{{ __('label.Show Matches') }}" type="submit">
		</div>
	</div> -->
@endif
{!! smallLoader() !!}
