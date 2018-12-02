<body ontouchstart="" class="{{ config('app.lang_name')[config('app.locale')]  }}">
    <!-- Header Start -->
    <div class="header loggedin">
        <div class="container">
            <div class="row">
                <div class="col-sm-3 logocol">
                    <div class="logo">
						<a href="{{ route('front-home') }}"><img src="{{ asset('frontend/images/logo.png') }}" alt="Logo" class="img-responsive"></a>
                    </div>
                </div>
                <div class="col-sm-9 col-xs-8 fr">
                    <div class="dflx top-txtr apipg">
                        <a href="{{CONFLUX_URL}}" class="conflux-lg"><img src="{{ asset('frontend/images/CONFLUX.png') }}" class="img-responsive"/></a>
						@if(Session::get('conf_user_details') !== null)
							<div class="wallet-balance">
								<span class="wb-label">{{ __('label.Wallet') }}
									<span class="mobhidetxt">{{ __('label.Balance') }}</span>:
								</span>
								<div class="wb-amount">
									<span>{{ Session::get('conf_user_details')->currency }}</span>
									<strong id="wallet_balance">{{ Session::get('user_details')['balance'] }}</strong>
								</div>
							</div>
						@endisset

						@if(Session::get('conf_user_details') !== null)
	                        <div class="user-profile-main">
	                            <div class="user-profile-div">
	                                <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">
	                                    <img src="{{ asset('frontend/images/user-profile-img-bk.png') }}" width="34" alt="User Profile">
	                                    <span class="user-name">
	                                    	{{Session::get('conf_user_details')->playerName}}
	                                    </span>
	                                    <span class="caret"></span>
	                                </button>
	                                <ul class="dropdown-menu">
	                                    <!-- <li>
	                                        <a href="{{ route('front-get-profile') }}">{{ __('registration.Profile')}}</a>
	                                    </li>
	                                    <li>
	                                        <a href="{{ route('front-get-change-password') }}">{{ __('registration.Change Password')}}</a>
	                                    </li> -->
	                                    <li>
	                                        <a href="{{ route('front-get-transactions') }}">{{ __('registration.My Transactions')}}</a>
	                                    </li>
                                        <li class="inline-badge">
	                                        <a href="{{ route('front-get-support-ticket') }}">{{ __('registration.Support Ticket')}}</a>
	                                        <span class="badge" id="unread_ticket_header"></span>
                                        </li>
	                                    <li>
											<form class="" action="{{route('front-post-logout')}}" method="post">
												{{ csrf_field() }}
												<input type="submit" class="" value="{{ __('registration.Log Out')}}" />
											</form>
	                                    </li>
	                                </ul>
	                            </div>
	                        </div>
						@else
							{{-- <ul class="login-signup">
								<li>
									<a href="{{ route('front-get-login') }}"><img width="34" src="{{ asset('frontend/images/user-profile-img.png') }}" alt="User Profile"> {{ __('registration.Login') }}</a>
								</li>
								<li>
									<a href="{{ route('front-get-registration') }}"> <img src="{{ asset('frontend/images/registration.png') }}" alt="User Profile"> {{ __('registration.Sign Up') }}</a>
								</li>
							</ul> --}}
						@endif
                        <div class="sidemenu">
                            <nav id="myNavmenu" class="navmenu navmenu-default navmenu-custom navmenu-fixed-left offcanvas" role="navigation">
                                <div class="closeicon">
                                    <img src="{{ asset('frontend/images/close.svg') }}">
                                </div>

                                <a href="#" class="conflux"><img src="{{ asset('frontend/images/CONFLUX.png') }}" class="img-responsive"/></a>
                                <div class="mob-langselector">
                                    <div class="mob-langselector-hd">{{ __('label.Select Language') }}</div>
                                    <ul>
                                        <li>
                                            <a href="#">English</a>
                                        </li>
                                        <li>
                                            <a href="#">Vietnamese</a>
                                        </li>
                                        <li>
                                            <a href="#">Korean</a>
                                        </li>
                                        <li>
                                            <a href="#">Thai</a>
                                        </li>
                                        <li>
                                            <a href="#">Mandarin</a>
                                        </li>
                                        <li>
                                            <a href="#">Japanese</a>
                                        </li>
                                        <li>
                                            <a href="#">Filipino</a>
                                        </li>
                                    </ul>
                                </div>
								@if(Session::get('user_details') !== null)
                                <div class="mob-langselector">
                                    <div class="mob-langselector-hd">{{ __('label.User Profile') }}</div>
                                    <ul>
										<li>
	                                        <a href="{{ route('front-get-profile') }}">{{ __('registration.Profile')}}</a>
	                                    </li>
	                                    <li>
	                                        <a href="{{ route('front-get-change-password') }}">{{ __('registration.Change Password')}}</a>
	                                    </li>
	                                    <li>
	                                        <a href="{{ route('front-get-transactions') }}">{{ __('registration.My Transactions')}}</a>
	                                    </li>
	                                    <li>
	                                        <a href="{{ route('front-get-support-ticket') }}">{{ __('registration.Support Ticket')}}</a>
	                                    </li>
                                        <li>
											<form class="" action="{{route('front-post-logout')}}" method="post">
												{{ csrf_field() }}
												<input type="submit" class="" value="{{ __('registration.Log Out')}}" />
											</form>
                                        </li>
                                    </ul>
                                </div>
							@else
								{{--<div class="mob-langselector">
                                    <div class="mob-langselector-hd">{{ __('label.User Profile') }}</div>
                                    <ul>
                                        <li>
                                            <a href="{{ route('front-get-login') }}">{{ __('registration.Login')}}</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('front-get-registration') }}">{{ __('registration.Sign Up')}}</a>
                                        </li>
                                    </ul>
                                </div>--}}
							@endif

                            </nav>
                            <button type="button" class="navbar-toggle" data-toggle="offcanvas" data-target="#myNavmenu" data-canvas="body">
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                            </button>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->
	<nav class="navbar navbar-default navbar-static-top">



		
	        <div class="container">
	            <div class="navbar-header">
	                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
	                    <span class="sr-only">Toggle navigation</span>
	                    <span class="icon-bar"></span>
	                    <span class="icon-bar"></span>
	                    <span class="icon-bar"></span>
	                </button>
	                <span class="bet_nav">Bet Navigation</span>
	            </div>
	            <div id="navbar" class="navbar-collapse collapse">
	                <ul class="nav navbar-nav">
	                	<li class="@if(isset($menu) && $menu == 'Home')active @endif">
	                        <a href="{{ route('front-home') }}">{{ __('user_label.Home') }}</a>
	                    </li>
	                    <li class="@if(isset($menu) && $menu == 'Pre-Match-Betting')active @endif">
	                        <a href="{{ route('front-sports-get-Pre-Match-Betting') }}">{{ __('user_label.Prematch Betting') }}</a>
	                    </li>
	                    <li onclick="showAllCountry(this)"  class="@if(isset($menu) && $menu == 'Live-Betting')active @endif">
	                        <a id="loggedin-live" class="loggedin-live" href="javascript:void(0);">{{ __('user_label.Live Betting') }}</a>
	                    </li>
	                    @if(Session::get('user_details') !== null)
					        <li class="dropdown @if(isset($menu) && $menu == 'History')active @endif">
							  	<a href="#" class="dropdown-toggle" data-toggle="dropdown">{{ __('user_label.History') }} <span class="caret"></span></a>
							  	<ul class="dropdown-menu" role="menu">
				                	<li><a href="{{ route('front-history-bet-single') }}">{{ __('user_label.Single Bet History') }}</a></li>
				                	<li><a href="{{ route('front-history-bet-combo') }}">{{ __('user_label.Combo Bet History') }}</a></li>
				              	</ul>
				            </li>
			            @endif
	                    {{--<li>
	                        <a href="#">Pending Bets</a>
	                    </li>--}}
	                </ul>
	                <ul class="nav navbar-nav pull-right nav-right">
	                    <li class="dropdown langselector">
				            <a class="dropdown-toggle" type="button" data-toggle="dropdown">
				                <img src="{{ asset('frontend/images/eng-flag.jpg') }}">
				                <span class="country-name">
				                	{{ config('app.lang_name')[config('app.locale')]  }}
				                </span>
				                <span class="caret"></span>
				            </a>
				            <ul class="dropdown-menu">
								@forelse ($languages as $key => $value)
									<li>
				                        <a class="change_language" data-lang="{{ $value->slug }}" href="javascript:void(0);">{{ $value->language }}</a>
				                    </li>
								@empty
								@endforelse
				            </ul>
				        </li>
				        <li class="dropdown oddsValueType">
				            <a class="dropdown-toggle" type="button" data-toggle="dropdown">
				                <span class="country-name">
				                	{{ __('user_label.'.Session::get('odds_value_type')) }}
				                </span>
				                <span class="caret"></span>
				            </a>
				            <ul class="dropdown-menu">
								<li>
				                    <a class="change_odds_type" data-odds-type="Decimal" href="javascript:void(0);">{{ __('user_label.Decimal') }}</a>
				                </li>
				                <li>
				                    <a class="change_odds_type" data-odds-type="American" href="javascript:void(0);">{{ __('user_label.American') }}</a>
				                </li>
				            </ul>
				        </li>
	                </ul>
	            </div>
	            <!--/.nav-collapse -->
	        </div>
		
		
	</nav>
