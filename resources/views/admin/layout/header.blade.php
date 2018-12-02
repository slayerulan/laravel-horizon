<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <title>Welcome To Apex</title>
    <!-- Favicon-->
    <link rel="icon" href="{{ asset("/admin/images/favicon.png") }}" type="image/x-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&subset=latin,cyrillic-ext" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" type="text/css">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">



    <!-- Bootstrap Core Css -->
    <link href="{{ asset("/admin/plugins/bootstrap/css/bootstrap.css") }}" rel="stylesheet">

    <!-- Waves Effect Css -->
    <link href="{{ asset("/admin/plugins/node-waves/waves.css") }}" rel="stylesheet" />

    <!-- Animation Css -->
    <link href="{{ asset("/admin/plugins/animate-css/animate.css") }}" rel="stylesheet" />

    <!-- Morris Chart Css-->
    <link href="{{ asset("/admin/plugins/morrisjs/morris.css") }}" rel="stylesheet" />

    <!-- Bootstrap Select Css -->
    <link href="{{ asset("/admin/plugins/bootstrap-select/css/bootstrap-select.css") }}" rel="stylesheet" />

    <!-- Dropzone Css -->
    <link href="{{ asset("/admin/plugins/dropzone/dropzone.css") }}" rel="stylesheet">

    <!-- Custom Css -->
    <link href="{{ asset("/admin/css/style.css") }}" rel="stylesheet">
    <link href="{{ asset("/admin/css/materialize.css") }}" rel="stylesheet">

    <!-- AdminBSB Themes. You can choose a theme from css/themes instead of get all themes -->
    <link href="{{ asset("/admin/css/themes/all-themes.css") }}" rel="stylesheet" />
</head>

<body class="theme-red">
    <!-- Page Loader -->
    <div class="">
        <div class="loader">
            <div class="preloader">
                <div class="spinner-layer pl-red">
                    <div class="circle-clipper left">
                        <div class="circle"></div>
                    </div>
                    <div class="circle-clipper right">
                        <div class="circle"></div>
                    </div>
                </div>
            </div>
            <p>Please wait...</p>
        </div>
    </div>
    <!-- #END# Page Loader -->
    <!-- Overlay For Sidebars -->
    <div class="overlay"></div>
    <!-- #END# Overlay For Sidebars -->
    <!-- Search Bar -->
    <div class="search-bar">
        <div class="search-icon">
            <i class="material-icons">search</i>
        </div>
        <input type="text" placeholder="START TYPING...">
        <div class="close-search">
            <i class="material-icons">close</i>
        </div>
    </div>
    <!-- #END# Search Bar -->
    <!-- Top Bar -->
    <nav class="navbar">
        <div class="container-fluid">
            <div class="navbar-header">
                <!--a href="javascript:void(0);" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse" aria-expanded="false"></a-->
                <a href="javascript:void(0);" class="bars"></a>
                <a class="navbar-brand" href="{{route('admin-dashboard')}}"><img src="{{ asset( 'storage/'.$site_logo ) }}" class="dashboard-logo" alt="logo" /></a>
            </div>

            <!--div class="collapse navbar-collapse" id="navbar-collapse">
                <ul class="nav navbar-nav navbar-right">
                    <notification v-bind:notifications="notifications"></notification>
                </ul>
            </div-->

            <div class="collapse navbar-collapse" id="navbar-collapse">
                <ul class="nav navbar-nav navbar-right">
                  <li class="dropdown">
                    <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button">
                            <i class="material-icons">notifications</i>
                            <span class="label-count" id="unread_ticket_header"></span>
                    </a>
                    <ul class="dropdown-menu">
                            <li class="header">NOTIFICATIONS</li>
                            <li class="body">
                                <ul class="menu">
                                    <li>
                                        <a href="javascript:void(0);">
                                            <div class="icon-circle bg-light-green">
                                                <i class="material-icons">transfer_within_a_station</i>
                                            </div>
                                            <div class="menu-info">
                                                <h4 id="unread_ticket_message"></h4>
                                            </div>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <li class="footer">
                              @if(Session::get('role_id') == 1)
                                <a href="{{route('admin-support-ticket-management-all-tickets-list')}}">View All Tickets</a>
                              @else
                                <a href="{{route('admin-support-ticket-management-my-tickets-list')}}">View All Tickets</a>
                              @endif
                            </li>
                        </ul>
                  </li>
                </ul>
            </div>

            <!--div class="dropdown langselector">
                    <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">
                        <img src="{{ asset('frontend/images/eng-flag.jpg') }}">
                        <span class="country-name">
                        	{{ config('app.lang_name')[config('app.locale')]  }}
                        </span>
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu lang-select">
    		            @forelse ($languages as $key => $value)
    			              <li>
                            <a class="change_language" data-lang="{{ $value->slug }}" href="javascript:void(0);">{{ $value->language }}</a>
                        </li>
    		            @empty
    		            @endforelse
                    </ul>
           </div-->

        </div>
    </nav>
    <!-- #Top Bar -->
    <section>
        <!-- Left Sidebar -->
        <aside id="leftsidebar" class="sidebar">
            <!-- User Info -->
            <div class="user-info">
                <div class="image">
                    <img width="48" height="48" src="{{ asset(Session::get('admin_details')['profile_image']) }}" alt="pf" />
                </div>
                <div class="info-container">
                    <div class="name" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">

                    @if (session('username'))
                         <strong>Welcome {{ session('username') }}</strong>
                    @endif

                    </div>

                    <div class="btn-group user-helper-dropdown">
                        <i class="material-icons" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">keyboard_arrow_down</i>
                        <ul class="dropdown-menu pull-right">
                            <li><a href="{{route('admin-profile-settings')}}"><i class="material-icons">person</i>Profile</a></li>
                            <li role="seperator" class="divider"></li>
                            <form id="logout" method="POST" action="{{route('admin-logout')}}">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}"/>
                            <button class="waves-effect waves-block admin-logout" type="submit"><i class="material-icons">input</i>Logout</button>
                            </form>
                        </ul>
                    </div>
                </div>
            </div>
