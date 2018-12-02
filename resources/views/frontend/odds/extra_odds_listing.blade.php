@forelse ($market_details as  $each_market)
	<div class="ods_block">
		<div class="ods_block_heading box_show_hide_odds">
			<i class="icon_class arrow_up"></i>
			<a href="javascript:void(0);">{{$each_market->name}}
			</a>
		</div>
		<div class="show_hide_div_odds">
			@forelse ($extra_odds[$each_market->id][0] as $extra_val => $each_extra_set_odds)
				@php
					$count	= count($each_extra_set_odds);
				@endphp
				<div class="ods_list {{$count > 3 ? 'many_odds' : '' }}">
					@forelse ($each_extra_set_odds as  $each_odd)
						@php
							$each_odd->market_extra_value	=	getMarketExtraValue($each_market->market_group, $each_odd->odds_name, $each_odd->market_extra_value, $each_market->name);
						@endphp
						<div class="claub_time">
							<a href="javascript:void(0);" class="each_odd {{ session('unique_bet_key') && in_array($each_odd->id,session('unique_bet_key')) ? 'active' : '' }}" id="each_odd_{{ $each_odd->id }}" data-id= "{{ $each_odd->id }}">

								<div class="mobile home_away">
									{{ ($each_odd->odds_name === '1') ? 'Home' : (($each_odd->odds_name === '2') ? 'Away' : $each_odd->odds_name) }}
									{{ $each_market->has_extra == 1 ? ' ( '. $each_odd->market_extra_value .' )' : '' }}
								</div>

								<label class="each_market_name">{{ (($each_odd->odds_name === '1') ? 'Home' : (($each_odd->odds_name === '2') ? 'Away' : $each_odd->odds_name)) }}
								{{ $each_market->has_extra == 1 ? ' ( '. $each_odd->market_extra_value .' )' : '' }}</label>
								<span class="pull-right">{{ oddsValue($each_odd->odds_value) }}</span>
							</a>
						</div>
					@empty
					@endforelse
				</div>
			@empty
			@endforelse
		</div>
	</div>
@empty
	<div class="ods_block">
		<div class="ods_list">
			<div class="no_data_found">{{ __('user_label.No Odds Found') }}</div>
		</div>
	</div>
@endforelse
