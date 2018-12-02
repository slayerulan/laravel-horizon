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

        @if (session('alert_msg'))
            <div class="alert alert-{{ session('alert_class') }} alert-dismissible">
                 <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">x</span></button>
                 <strong>{{ session('alert_msg') }}</strong>
            </div>
        @endif

        @if ($errors->any())
          <div class="alert alert-danger form_validation_error">
           <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">x</span></button>
           <ul class="error_list">
            @foreach ($errors->all() as $error)
                <li><i class="material-icons">info_outline</i> {{ $error }}</li>
               @endforeach
           </ul>
          </div>
        @endif


<div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <!-- Basic Validation -->
            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="header">
                            <h2>Profile Settings</h2>
                        </div>
                        <div class="body">
                            <form id="user-data" action="{{route('admin-profile-settings')}}" method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}"/>

                                <div class="form-group form-float">
                                    <div class="form-line">
                                        <input type="text" class="form-control" name="full_name" value="<?php echo $user_details[0]->full_name; ?>" >
                                        <label class="form-label">Full Name</label>
                                    </div>
                                </div>

                                <div class="form-group form-float">
                                    <div class="form-line">
                                        <input type="email" class="form-control" name="email" value="<?php echo $user_details[0]->email; ?>" >
                                        <label class="form-label">Email</label>
                                    </div>
                                </div>

                                <div class="form-group form-float">
                                    <div class="form-line">
                                        <input type="password" class="form-control" name="password" value="" >
                                        <label class="form-label">Password</label>
                                    </div>
                                </div>

                                <div class="form-group form-float">
                                    <div class="form-line">
                                        <input type="password" class="form-control" name="password_confirmation" value="" >
                                        <label class="form-label">Confirm Password</label>
                                    </div>
                                </div>

                                <div class="body">
                                        <div class="row clearfix">
                                            <div class="user-info col-sm-6">
                                                <label class="form-label">Profile Image</label>
                                                <div class="image">
                                                    @if($user_details[0]->profile_image)
                                                        <img width="48" height="48" src="{{ asset( 'storage/'.$user_details[0]->profile_image ) }}" alt="pf" />
                                                    @endif
                                                    <input name="profile_image" type="file" value="{{ old('profile_image') }}"/>
                                                </div>
                                            </div>

                                        </div>
                                </div>

                                <button class="btn btn-primary waves-effect" type="submit">SUBMIT</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- #END# Basic Validation -->
        </div>

    <div class="row clearfix">

        <!-- #END# Browser Usage -->
    </div>
</div>
</section>

@include('admin/layout/footer')
