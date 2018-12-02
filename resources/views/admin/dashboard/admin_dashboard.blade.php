			@include('admin/layout/header')
            <!-- #User Info -->
            @include('admin/layout/leftmenubar')
            @include('admin/layout/legal')
        </aside>
        <!-- #END# Left Sidebar -->
			@include('admin/layout/rightsidebar')
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="block-header">
                <h2>DASHBOARD</h2>
            </div>

						@if (session('alert_msg'))
                    <div class="alert alert-{{ session('alert_class') }} alert-dismissible">
                         <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">x</span></button>
                         <strong>{{ session('alert_msg') }}</strong>
                    </div>
            @endif

            <!-- Widgets -->
            <div class="row clearfix">
                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box bg-pink hover-expand-effect">
                        <div class="icon">
                            <i class="material-icons">account_circle</i>
                        </div>
                        <div class="content">
                            <div class="text">TOTAL AGENTS</div>
                            <div class="number count-to" data-from="0" data-to="@if($dashboard_data['total_agents']) {{ $dashboard_data['total_agents'] }} @endif" data-speed="1000" data-fresh-interval="20"></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box bg-cyan hover-expand-effect">
                        <div class="icon">
                            <i class="material-icons">people</i>
                        </div>
                        <div class="content">
                            <div class="text">TOTAL PLAYERS</div>
                            <div class="number count-to" data-from="0" data-to="@if($dashboard_data['total_players']) {{ $dashboard_data['total_players'] }} @endif" data-speed="1000" data-fresh-interval="20"></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box bg-light-green hover-expand-effect">
                        <div class="icon">
                            <i class="material-icons">transfer_within_a_station</i>
                        </div>
                        <div class="content">
                            <div class="text">NEW TICKET MESSAGES</div>
                            <div class="number count-to" data-from="0" data-to="@if($dashboard_data['total_tickets']) {{ $dashboard_data['total_tickets'] }} @endif" data-speed="1000" data-fresh-interval="20"></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box bg-orange hover-expand-effect">
                        <div class="icon">
                            <i class="material-icons">touch_app</i>
                        </div>
                        <div class="content">
                            <div class="text">TODAY'S TOTAL BETS</div>
                            <div class="number count-to" data-from="0" data-to="@if($dashboard_data['total_bets']) {{ $dashboard_data['total_bets'] }} @endif" data-speed="1000" data-fresh-interval="20"></div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="row clearfix">

                <!-- #END# Browser Usage -->
            </div>
        </div>
    </section>
@include('admin/layout/footer')
