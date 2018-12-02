@include('admin/layout/login_header')

<body class="login-page">
    <div class="login-box">
        <div class="logo">
            <a><img class="img-responsive" src="{{ asset( 'storage/'.$site_logo ) }}" alt="logo" /></a>
        </div>
        <div class="card">
            <div class="body">
                <form id="sign_in" method="POST" action="{{route('admin-post-login')}}">
                <input type="hidden" name="_token" value="{{ csrf_token() }}"/>

                    @if (session('alert_msg'))
                    <div class="alert alert-{{ session('alert_class') }} alert-dismissible">
                         <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">x</span></button>
                         <strong>{{ session('alert_msg') }}</strong>
                    </div>
                    @endif

                    <div class="msg">Log in to start your session</div>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="material-icons">email</i>
                        </span>
                        <div class="form-line">
                            <input type="email" class="form-control" name="email" placeholder="Email Address" required="" aria-required="true" aria-invalid="true">
                        </div>
                    </div>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="material-icons">lock</i>
                        </span>
                        <div class="form-line">
                            <input type="password" class="form-control" name="password" placeholder="Password" autocomplete="off" required>
                        </div>
                    </div>
                    <div class="row">
                        <!--div class="col-xs-8 p-t-5">
                            <input type="checkbox" name="rememberme" id="rememberme" class="filled-in chk-col-pink">
                            <label for="rememberme">Remember Me</label>
                        </div-->
                        <div class="col-xs-4">
                            <button class="btn btn-block bg-pink waves-effect" type="submit">LOG IN</button>
                        </div>
                    </div>
                    <div class="row m-t-15 m-b--20">
                        <div class="col-xs-12 align-right">
                            <a href="{{route('admin-get-forgot-password')}}">Forgot Password?</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

@include('admin/layout/login_footer')
