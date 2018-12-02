<div class="col-md-3 left_spoerts_panel">
	<ul class="tabs">
		<li class="active" rel="tab1">{{ __('user_label.Prematch Betting') }}</li>
		<li rel="tab2">{{ __('user_label.Live Betting') }} </li>
	</ul>
	<div class="tab_container">
		<h3 class="d_active tab_drawer_heading" rel="tab1">{{ __('label.Prematch Betting') }}</h3>
		<div id="tab1" class="tab_content">
			<ul class="game_list">
				@forelse ($sports_bar as $each_sport)
					<li class="{{ str_replace(' ','-',$each_sport->sport_name) }} {{(isset($active_sport) && ($each_sport->sport_slug == $active_sport) ? 'clickable':'' )}}">
						<a href="{{ route('front-sports-get-all-league', ['sport_slug' => $each_sport->sport_slug ]) }}">
							<i class="icon"></i>
							<span>{{ __('sports.'.$each_sport->sport_name) }}</span>
							<span class="match-counter country_list_arrow">{{ count($each_sport->countries) }}</span>
						</a>
						<ul class="country">
							@forelse ($each_sport->countries as  $each_country)
								<li id="{{ $each_sport->sport_slug .'_'.$each_country->name }}">
									<a href="{{ route('front-sports-get-league-by-country', ['sport_slug' => $each_sport->sport_slug, 'country_slug' => $each_country->name ]) }}">{{ __('country.'.$each_country->name) }}
										<span class="match-counter league_list_arrow">{{ $each_country->league_count }}</span>
									</a>
								</li>
							@empty
								<li>
									<a href="javascript:void(0);">{{ __('country.No league found') }}
									</a>
								</li>
							@endforelse
						</ul>
					</li>
				@empty

				@endforelse
			</ul>
		</div>
		<!-- #tab6 -->


		<h3 class="tab_drawer_heading" rel="tab2">{{ __('label.Live Betting') }}</h3>

		<div id="tab2" class="tab_content" v-cloak>

			<ul class="game_list">

				<li class="Soccer">
					<a v-on:click="showByCoutry('',1)" href="javascript:void(0)">
						<i class="icon"></i>
						<span>{{ __('sports.Football') }}</span>
						<span class="match-counter country_list_arrow_live" >@{{ message }}</span>
					</a>
					<ul class="country">

						<li  v-on:click="showByCoutry(value.country,0)" class="Soccer" v-for="value in objects">
							<a href="javascript:void(0)">@{{ value.country }}
							<span class="match-counter league_list_arrow">@{{ value.leagues }}</span>
						</li>

						<li v-if="objects.length == 0">
							<a href="javascript:void(0);">{{ __('country.No league found') }}
							</a>
						</li>

					</ul>
				</li>
			</ul>

		</div>
		<!-- #tab7-->
	</div>

	<div class="clearfix"></div>

</div>
