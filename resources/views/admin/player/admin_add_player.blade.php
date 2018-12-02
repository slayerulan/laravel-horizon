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
                            <h2>Add Player</h2>
                        </div>
                        <div class="body">
                            <form id="user-data" action="{{route('admin-player-management-add-player')}}" method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}"/>

                                <div class="form-group form-float">
                                    <div class="form-line">
                                        <input type="text" class="form-control" name="full_name" value="{{ old('full_name') }}">
                                        <label class="form-label">Full Name</label>
                                    </div>
                                </div>
                                <div class="form-group form-float">
                                    <div class="form-line">
                                        <input type="text" class="form-control" name="username" id="username"  value="{{ old('username') }}" required>
                                        <label class="form-label">Username</label>
                                    </div>
                                </div>
                                <div class="form-group form-float">
                                    <div class="form-line">
                                        <input type="email" class="form-control" name="email" value="{{ old('email') }}">
                                        <label class="form-label">Email</label>
                                    </div>
                                </div>
                                <div class="form-group form-float">
                                    <div class="form-line">
                                        <input type="tel" class="form-control" name="mobile" id="mobile" value="{{ old('mobile') }}">
                                        <label class="form-label">Mobile</label>
                                    </div>
                                </div>

                                <div class="form-group form-float">
                                    <div class="form-line">
                                        <textarea name="address" cols="30" rows="5" class="form-control no-resize" value="">{{ old('address') }}</textarea>
                                        <label class="form-label">Address</label>
                                    </div>
                                </div>

                                <div class="body">
                                    <div class="row clearfix">

                                      <div class="form-group col-md-12">
                                          <label class="form-label">Gender</label> : <br>
                                            <div class="gender">
                                                <div>
                                                  <input type="radio" name="gender" id="male" class="with-gap" checked value="male">
                                                  <label for="male">Male</label>
                                                </div>

                                                <div>
                                                  <input type="radio" name="gender" id="female" class="with-gap" value="female">
                                                  <label for="female">Female</label>
                                                </div>
                                            </div>

                                            <div class="row clearfix">
                                                <div class="user-info col-sm-6">
                                                    <label class="form-label">Profile Image</label>
                                                    <div class="image">
                                                        <input name="profile_image" type="file" value="{{ old('profile_image') }}"/>
                                                    </div>
                                                </div>
                                            </div>
                                      </div>

                                        <div class="col-sm-6">
                                            <label class="form-label">Agent</label>
                                            <select name="agent_id" id="agent_id" class="form-control show-tick selectpicker" data-live-search="true" tabindex="-98">
                                                <option value="">-- Please select --</option>
                                                @foreach($agents as $agents_data)
                                                  @if (old('agent_id') == $agents_data['id'])
                                                          <option value="{{ $agents_data['id'] }}" selected>{{ $agents_data['username'] }}</option>
                                                  @else
                                                        <option value="{{ $agents_data['id'] }}">{{ $agents_data['username'] }}</option>
                                                  @endif
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-sm-6">
                                                <label class="form-label">Country</label>
                                                <select name="country" class="form-control show-tick" data-live-search="true" tabindex="-98">
                                                    <option value="">-- Please select --</option>
                                                    @foreach($country as $country_data)
                                                      @if (old('country') == $country_data['id'])
                                                            <option value="{{ $country_data['id'] }}" selected>{{ $country_data['name'] }}</option>
                                                      @else
                                                            <option value="{{ $country_data['id'] }}">{{ $country_data['name'] }}</option>
                                                      @endif
                                                    @endforeach
                                                </select>
                                        </div>
                                    </div>

                                    <div class="row clearfix">
                                        <div class="col-sm-6">
                                            <label class="form-label">Language</label>
                                            <select name="language" class="form-control show-tick" data-live-search="true" tabindex="-98">
                                                <option value="">-- Please select --</option>
                                                @foreach($language as $language_data)
                                                      @if (old('language') == $language_data['id'])
                                                            <option value="{{ $language_data['id'] }}" selected>{{ $language_data['language'] }}</option>
                                                      @else
                                                            <option value="{{ $language_data['id'] }}">{{ $language_data['language'] }}</option>
                                                      @endif
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-sm-6">
                                            <label class="form-label">Currency</label>
                                            <select name="currency" class="form-control show-tick" data-live-search="true" tabindex="-98">
                                                <option value="">-- Please select --</option>
                                                @foreach($currency as $currency_data)
                                                    @if (old('currency') == $currency_data['id'])
                                                          <option value="{{ $currency_data['id'] }}" selected>{{ $currency_data['curency_name'] }}</option>
                                                    @else
                                                          <option value="{{ $currency_data['id'] }}">{{ $currency_data['curency_name'] }}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-sm-6">
                                            <label class="form-label">Bet Rules</label>
                                            <select name="bet_rule_id" id="bet_rule_id" class="form-control show-tick" data-live-search="true" tabindex="-98">
                                                <option value="">-- Please select --</option>
                                                @foreach($bet_rules as $bet_rules_data)
                                                    @if (old('bet_rule_id') == $bet_rules_data->id)
                                                          <option value="{{ $bet_rules_data->id }}" selected>{{ $bet_rules_data->title }}</option>
                                                    @else
                                                          <option value="{{ $bet_rules_data->id }}">{{ $bet_rules_data->title }}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>


                                </div>

                                <button class="btn btn-primary waves-effect" type="submit">Submit</button>
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
