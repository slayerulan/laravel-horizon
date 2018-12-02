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
                            <h2>EDIT MASTER AGENT</h2>
                        </div>
                        <div class="body">
                            <form id="user-data" action="{{route('admin-agent-management-master-agent-edit-master-agent')}}" method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}"/>
                                <input type="hidden" name="user_id" value="{{ $user_data[0]->user_id }}" />

                                <div class="form-group form-float">
                                    <div class="form-line">
                                        <input type="text" class="form-control" name="full_name" value="{{ $user_data[0]->full_name }}" required>
                                        <label class="form-label">Full Name</label>
                                    </div>
                                </div>
                                <div class="form-group form-float">
                                    <div class="form-line">
                                        <input type="text" class="form-control" name="username" id="username"  value="{{ $user_data[0]->username }}" readonly>
                                        <label class="form-label">Username</label>
                                    </div>
                                </div>
                                <div class="form-group form-float">
                                    <div class="form-line">
                                        <input type="email" class="form-control" name="email" value="{{ $user_data[0]->email }}" required>
                                        <label class="form-label">Email</label>
                                    </div>
                                </div>
                                <div class="form-group form-float">
                                    <div class="form-line">
                                        <input type="tel" class="form-control" name="mobile" id="mobile" value="{{ $user_data[0]->mobile }}">
                                        <label class="form-label">Mobile</label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <input type="radio" name="gender" id="male" class="with-gap" value="male" @if($user_data[0]->sex=='male') checked @endif >
                                    <label for="male">Male</label>

                                    <input type="radio" name="gender" id="female" class="with-gap" value="female" @if($user_data[0]->sex=='female') checked @endif >
                                    <label for="female" class="m-l-20">Female</label>
                                </div>


                                <div class="form-group form-float">
                                    <div class="form-line">
                                        <textarea name="address" cols="30" rows="5" class="form-control no-resize">{{ $user_data[0]->address }}</textarea>
                                        <label class="form-label">Address</label>
                                    </div>
                                </div>

                                <div class="body">
                                        <div class="row clearfix">
                                            <div class="col-sm-6">
                                                <label class="form-label">Country</label>
                                                <select name="country" class="form-control show-tick" data-live-search="true" tabindex="-98" required>
                                                    <option value="">-- Please select --</option>
                                                    @foreach($country as $country_data)
                                                    <option value="{{ $country_data['id'] }}" @if($user_data[0]->country_id==$country_data['id']) selected @endif >{{ $country_data['name'] }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="col-sm-6">
                                                <label class="form-label">Language</label>
                                                <select name="language" class="form-control show-tick" data-live-search="true" tabindex="-98" required>
                                                    <option value="">-- Please select --</option>
                                                    @foreach($language as $language_data)
                                                    <option value="{{ $language_data['id'] }}" @if($user_data[0]->language_id==$language_data['id']) selected @endif >{{ $language_data['language'] }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="row clearfix">
                                            <div class="col-sm-6">
                                                <label class="form-label">Currency</label>
                                                <select name="currency" class="form-control show-tick" data-live-search="true" tabindex="-98" required>
                                                    <option value="">-- Please select --</option>
                                                    @foreach($currency as $currency_data)
                                                    <option value="{{ $currency_data['id'] }}" @if($user_data[0]->currency_id==$currency_data['id']) selected @endif >{{ $currency_data['curency_name'] }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="user-info col-sm-6">
                                                <label class="form-label">Profile Image</label>
                                                <div class="image">
                                                    @if($user_data[0]->profile_image)
                                                        <img width="48" height="48" src="{{ asset( 'storage/'.$user_data[0]->profile_image ) }}" alt="pf" />
                                                    @endif
                                                    <input name="profile_image" type="file"/>
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
