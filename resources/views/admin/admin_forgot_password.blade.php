@include('admin/layout/login_header')

<body class="fp-page">
    <div class="fp-box">
        <div class="logo">
            <a><img src="{{ asset( 'storage/'.$site_logo ) }}" alt="logo" /></a>
        </div>
        <div class="card">
            <div class="body">
                <form id="forgot_password" method="POST" action="{{route('admin-post-forgot-password')}}">
                <input type="hidden" name="_token" value="{{ csrf_token() }}"/>
                @if (session('alert_msg'))
                <div class="alert alert-{{ session('alert_class') }} alert-dismissible">
                     <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">x</span></button>
                     <strong>{{ session('alert_msg') }}</strong>
                </div>
                @endif
                    <div class="msg">
                        Enter your email address that you used to register. We'll send you an email with your username and a
                        link to reset your password.
                    </div>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="material-icons">email</i>
                        </span>
                        <div class="form-line">
                            <input type="email" class="form-control" name="email" placeholder="Email" required autofocus>
                        </div>
                    </div>

                    <button class="btn btn-block btn-lg bg-pink waves-effect" type="submit">RESET MY PASSWORD</button>

                    <div class="row m-t-20 m-b--5 align-center">
                        <a href="{{route('admin-login')}}">Log In!</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

@include('admin/layout/login_footer')
