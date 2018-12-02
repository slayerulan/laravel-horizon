<div class="panel with-nav-tabs panel-primary">
	<div class="panel-heading">
			<ul class="nav nav-tabs">
				@php
					$single_bet_hint = '';
					$combo_bet_hint = '';
					if(Session::get('conf_user_details') !== null) {
						$currency = Session::get('conf_user_details')->currency;
						$bet_rules = getBetRule();
						if(isset($bet_rules->straight_bet_min_limit->{$currency})) {
							$single_bet_hint.= __('label.Minimum Stake Amount') .' : '. $bet_rules->straight_bet_min_limit->{$currency} .'<br />';
						}
						if(isset($bet_rules->straight_bet_max_limit->{$currency})) {
							$single_bet_hint.= __('label.Maximum Stake Amount') .' : '. $bet_rules->straight_bet_max_limit->{$currency} .'<br />';
						}
						if(isset($bet_rules->maximum_straight_bet_payout->{$currency})){
							$single_bet_hint.= __('label.Maximum Payout') .' : '. $bet_rules->maximum_straight_bet_payout->{$currency} .'<br />';
						}

						if(isset($bet_rules->parlay_min_limit->{$currency})) {
							$combo_bet_hint.= __('label.Minimum Stake Amount') .' : '. $bet_rules->parlay_min_limit->{$currency} .'<br />';
						}
						if(isset($bet_rules->parlay_max_limit->{$currency})) {	
							$combo_bet_hint.= __('label.Maximum Stake Amount') .' : '. $bet_rules->parlay_max_limit->{$currency} .'<br />';
						}
						$combo_bet_hint.= __('label.Maximum Number of Bets') .' : '. $bet_rules->maximum_number_of_bets_per_parlay .'<br />';
						$combo_bet_hint.= __('label.Minimum Number of Bets') .' : '. $bet_rules->minimum_number_of_bets_per_parlay .'<br />';
						if(isset($bet_rules->maximum_parlay_payout->{$currency})){
							$combo_bet_hint.= __('label.Maximum Payout') .' : '. $bet_rules->maximum_parlay_payout->{$currency} ;
						}
					}
				@endphp
				<li class="active">
                    <a href="#tab1primary" data-toggle="tab">{{ __('label.Single') }} 
                    	@if(!empty($single_bet_hint))
                    		<div class="tooltips"> <i class="fa fa-info-circle" aria-hidden="true"></i> <span>{!! $single_bet_hint !!}</span> </div>
                    	@endif
                    </a>
					<!--a href="#tab1primary" data-toggle="tab">{{ __('label.Single') }} <i class="fa fa-info-circle" aria-hidden="true" title="{!! $single_bet_hint !!}" data-toggle="tooltip" data-placement="right"></i></a-->
				</li>
				<li>
                    <a href="#tab2primary" data-toggle="tab">{{ __('label.Combo') }} 
                    	@if(!empty($combo_bet_hint))
                    		<div class="tooltips"> <i class="fa fa-info-circle" aria-hidden="true"></i> <span>{!! $combo_bet_hint !!}</span> </div>
                    	@endif
                    </a>

					<!--a href="#tab2primary" data-toggle="tab">{{ __('label.Combo') }} <i class="fa fa-info-circle" aria-hidden="true" title="{!! $combo_bet_hint !!}" data-toggle="tooltip" data-placement="left"></i></a-->
				</li>
			</ul>
	</div>
	<div class="panel-body bet_slip_body">
		<div class="tab-content">
		    <div class="tab-pane fade in active" id="tab1primary">
		        @if (session('pre_match_selected_bet'))
					@php
						$total_bet = count(session('pre_match_selected_bet'));
					@endphp
					<div class="bets">
			            @foreach (session('pre_match_selected_bet') as $key => $bet_details)
				            <div class="selected_bet each_single_bet bet_slip_{{ $key }}">
				                <div class="pull-left selected_bat_between">
				                    <div class="match_between">{{ $bet_details['home_team'].' '. __('label.vs').' '.$bet_details['away_team'] }}</div>
				                </div>
				                <div class="pull-right">
				                    <span class="remove">
				                    	<i class="remove_all_icon remove_bet" onclick="unsetBet({{ $key }})">X</i>
				                    </span>
				                </div>
				                <div class="BetInfo">{{ $bet_details['market_name'] }}
									@if (!empty($bet_details['extra_value']))
										{{ $bet_details['extra_value'] }}
									@endif
									 | {{ (($bet_details['bet_for'] === '1') ? 'Home' : (($bet_details['bet_for'] === '2') ? 'Away' : $bet_details['bet_for'])) }} @ {{ oddsValue($bet_details['odds_value']) }}
								</div>
				                <div class="stack_est_win">
				                    <div class="stake_amt pull-left">{{ __('label.Stake') }}
				                        <input name="pre_stake_amount[{{ $key }}]" id="pre_stake_amount_{{ $key }}" calculate-data-bind="pre_prize_amount[{{ $key }}]" data-value="{{ $bet_details['odds_value'] }}" class="only_number_input single_stake_amount stake_field" value="" type="text">
				                    </div>
				                    <div class="est_amt pull-left">{{ __('label.Est return') }}
				                        <input name="pre_prize_amount[{{ $key }}]" calculate-data-bind="pre_stake_amount[{{ $key }}]" data-value="{{ $bet_details['odds_value'] }}" class="only_number_input single_prize_amount prize_field"  type="text">
				                    </div>
				                </div>
				            </div>
			            @endforeach
					</div>
					<div class="stake_per_bet">
			            <div class="pull-right"> {{ __('label.Stake per bet') }}
			                <input value="" class="only_number_input stake_per_single_bet" type="text">
			            </div>
			        </div>
			        <div class="stake_bet">
			            <div class="total_bet">
							<div class="total_amt_of_bet">{{ __('label.Total') }} {{ $total_bet }} {{ __('label.'.str_plural('Bet', $total_bet)) }} :</div>
			                <div class="total_amt"> <span id="single_total_stake"></span> {{-- config('app.currency') --}}</div>
			            </div>
			            <input type="hidden" name="maximum_straight_bet_payout" id="maximum_straight_bet_payout" value="@if(isset($bet_rules) && isset($bet_rules->maximum_straight_bet_payout->{$currency})){{ $bet_rules->maximum_straight_bet_payout->{$currency} }}@endif">
			            <div class="total_bet">
			                <div class="total_amt_of_bet">{{ __('label.Est return') }} :</div>
			                <div class="total_amt"> <span id="single_total_prize"></span> {{-- config('app.currency') --}}</div>
			            </div>
			            <div>
			            	<input type="checkbox" name="except_single_odds_changes" value="1">
			            	Accept odds changes
			            </div>
			            <button href="javascript:void(0);" id="place_single_bet" class="palce_bet">{{ __('label.Place Bet') }}</button>
			        </div>
			        @if(Session::get('conf_user_details') !== null)
				        <div class="info-bet">
				        	@if(isset($bet_rules->straight_bet_min_limit->{$currency}))
					        	<p>{{ __('label.Minimum Stake Amount') .' : '.$currency.' '. $bet_rules->straight_bet_min_limit->{$currency} }}</p>
					        @endif
					        @if(isset($bet_rules->straight_bet_max_limit->{$currency}))
								<p>{{ __('label.Maximum Stake Amount') .' : '.$currency.' '. $bet_rules->straight_bet_max_limit->{$currency} }}</p>
							@endif
							@if(isset($bet_rules->maximum_straight_bet_payout->{$currency}))
								<p>{{ __('label.Maximum Payout') .' : '.$currency.' '. $bet_rules->maximum_straight_bet_payout->{$currency} }}</p>
							@endif
						</div>
					@endif
				@else
					<div class="stake_bet">
			            <div class="total_bet">
			                <div class="total_amt_of_bet">{{ __('label.No bet in the slip') }}</div>
		            	</div>
	            	</div>
		        @endif

		    </div>
		    <div class="tab-pane fade" id="tab2primary">
				@if (session('pre_match_selected_bet'))
					@php
						$match_id_wise_count 		= 	array_count_values(array_pluck(session('pre_match_selected_bet'),'match_id'));
						$odds_total_value			=	1;
						$max_bet_for_single_match	=	max($match_id_wise_count);
					@endphp
					<div class="bets">
			            @foreach (session('pre_match_selected_bet') as $key => $bet_details)
							@php
								$odds_total_value	*=	$bet_details['odds_value'];
							@endphp
				            <div class="selected_bet bet_slip_{{ $key }} {{ $match_id_wise_count[$bet_details['match_id']] > 1 ? 'error' : '' }}">
				                <div class="pull-left selected_bat_between">
				                    <div class="match_between">{{ $bet_details['home_team'].' '. __('label.vs').' '.$bet_details['away_team'] }}</div>
				                </div>
				                <div class="pull-right">
				                    <span class="remove">
				                    	<i class="remove_all_icon"  onclick="unsetBet({{ $key }})">X</i>
				                    </span>
				                </div>
				                <div class="BetInfo">{{ $bet_details['market_name'] }}
									@if (!empty($bet_slip_data['extra_value']))
										{{ $bet_details['extra_value'] }}
									@endif
									 | {{ (($bet_details['bet_for'] === '1') ? 'Home' : (($bet_details['bet_for'] === '2') ? 'Away' : $bet_details['bet_for'])) }} @ {{ oddsValue($bet_details['odds_value']) }}
								</div>
				            </div>
				            <input name="combo_odds_values[{{ $key }}]" value="{{ $bet_details['odds_value'] }}" type="hidden">
			            @endforeach
					</div>
					<div class="stake_per_bet">
						<div class="stake_amt pull-left">{{ __('label.Stake') }}
							<input id="combo_stake_amount" name="combo_stake_amount" data-bind="combo_stake_amount" calculate-data-bind="combo_prize_amount" data-value="{{ $odds_total_value }}" class="only_number_input stake_field" type="text">
						</div>
						<input type="hidden" name="maximum_parlay_payout" id="maximum_parlay_payout" value="@if(isset($bet_rules) && isset($bet_rules->maximum_parlay_payout->{$currency})){{ $bet_rules->maximum_parlay_payout->{$currency} }}@endif">
			            <div class="stake_amt pull-right"> {{ __('label.Est return') }}
			                <input name="combo_prize_amount" data-bind="combo_prize_amount" calculate-data-bind="combo_stake_amount" data-value="{{ $odds_total_value }}" class="only_number_input prize_field"type="text">
			            </div>
			        </div>
			        <div class="stake_bet">
			            <div class="total_bet">
			                <div class="total_amt_of_bet" id="total_num_of_bet" data-totalbet="{{ $total_bet }}">{{ __('label.Total') }} {{ $total_bet }} {{ __('label.'.str_plural('Bet', $total_bet)) }} :</div>
			                <div class="total_amt"> <span class="combo_stake_amount"></span> {{-- config('app.currency') --}}</div>
			            </div>
			            <div class="total_bet">
			                <div class="total_amt_of_bet">{{ __('label.Est return') }} :</div>
			                <div class="total_amt"><span class="combo_prize_amount"></span> {{-- config('app.currency') --}} </div>
			            </div>
						@if (isset($bet_rules) && $max_bet_for_single_match == 1 && $bet_rules->maximum_number_of_bets_per_parlay >= $total_bet && $bet_rules->minimum_number_of_bets_per_parlay <= $total_bet)
							<div>
				            	<input type="checkbox" name="except_combo_odds_changes" value="1">
				            	Accept odds changes
				            </div>
							<button href="javascript:void(0);" id="place_combo_bet" class="palce_bet">{{ __('label.Place Bet') }}</button>
						@endif
			        </div>
			        @if(Session::get('conf_user_details') !== null)
						<div class="info-bet">
							@if(isset($bet_rules->parlay_min_limit->{$currency}))
								<p>{{ __('label.Minimum Stake Amount') .' : '.$currency.' '. $bet_rules->parlay_min_limit->{$currency} }}</p>
							@endif
							@if(isset($bet_rules->parlay_max_limit->{$currency}))
								<p>{{ __('label.Maximum Stake Amount') .' : '.$currency.' '. $bet_rules->parlay_max_limit->{$currency} }}</p>
							@endif
							@if(isset($bet_rules->maximum_number_of_bets_per_parlay))
								<p>{{ __('label.Maximum Number of Bets') .' : '. $bet_rules->maximum_number_of_bets_per_parlay }}</p>
							@endif
							@if(isset($bet_rules->minimum_number_of_bets_per_parlay))
								<p>{{ __('label.Minimum Number of Bets') .' : '. $bet_rules->minimum_number_of_bets_per_parlay }}</p>
							@endif
							@if(isset($bet_rules->maximum_parlay_payout->{$currency}))
								<p>{{ __('label.Maximum Payout') .' : '.$currency.' '. $bet_rules->maximum_parlay_payout->{$currency} }}</p>
							@endif
						</div>
					@endif
				@else
					<div class="stake_bet">
			            <div class="total_bet">
			                <div class="total_amt_of_bet">{{ __('label.No bet in the slip') }}</div>
		            	</div>
	            	</div>
		        @endif
		    </div>
		</div>
	</div>
</div>
