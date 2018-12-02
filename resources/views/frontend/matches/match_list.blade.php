@if(isset($league_wise_match_details))
	@forelse ($league_wise_match_details as  $each_league)
		<div class="ods_block">
			<div class="ods_block_heading box_show_hide">
				<i class="icon_class arrow_up"></i>
				<a href="javascript:void(0);">{{ $each_league[0]->league->country->name .' : '. $each_league[0]->league->name }}
				</a>
			</div>
			<div class="show_hide_div">
				@forelse ($each_league as  $each_match)
					<div class="ods_list ods_head">
						<div class="date_time">
							<i class="{{ $sport_details->slug }}_sports_icon"></i>
							<span>
								{!! str_ireplace(',', '<br>', \Carbon\Carbon::createFromTimestamp($each_match->start_timestamp)->formatLocalized('%b %d,%H:%M')) !!}
							</span>
						</div>
						<div class="mobile mob_title">
							{{ $each_match->home_team['name'] }} (Home) vs {{ $each_match->away_team['name'] }} (Away)
						</div>

						@forelse ($match_wise_odds_details[$each_match->match_id] as $key => $each_odd)
							<div class="{{ $loop->first || $loop->last ? 'claub_time' : 'claub_time' }}">
								<a href="javascript:void(0);" id="each_odd_{{ $each_odd->id }}"  class="each_odd {{ session('unique_bet_key') && in_array($each_odd->id,session('unique_bet_key')) ? 'active' : '' }}" data-id= "{{ $each_odd->id }}" data-type="PM">
									<div class="mobile home_away">
										@if($loop->first)
											Home
										@elseif ($loop->last)
											Away
										@else
											{{$each_odd->odds_name}}
										@endif
									</div>

									<span class="ods_title">
										@if($loop->first)
											{{ $each_match->home_team['name'] }}
										@elseif ($loop->last)
											{{ $each_match->away_team['name'] }}
										@else
											{{$each_odd->odds_name}}
										@endif
									</span>
									<span class="pull-right">{{ oddsValue($each_odd->odds_value) }}</span>
								</a>
							</div>
						@empty

						@endforelse
						<div class="market">
							<a onclick="loadExtraOdds({{ $sport_details->id }}, {{ $each_match->match_id }})" href="javascript:void(0);"><i class="icon_class_{{ $each_match->match_id }} fa fa-plus"></i></a>
						</div>

					</div>
					<div class="each_match_extra_odd"  id="extra_odds_of_{{ $each_match->match_id }}" style="display:none;">
						{!! smallLoader('block', 'small_loader_'.$each_match->match_id) !!}
					</div>
				@empty
					<div class="ods_list">
						<div class="no_data_found">{{ __('user_label.No Match Found') }}</div>
					</div>
				@endforelse
			</div>
		</div>
	@empty

	@endforelse
@else
	<div class="leagues_list_block">
		<div class="leagues">{{ __('user_label.No Match Found') }}</div>
	</div>
@endif