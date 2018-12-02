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
                            <h2>Add Odds Juice</h2>
                        </div>
                        <div class="body">
                            <form id="user-data" action="{{route('admin-sports-book-management-odds-editing-insert')}}" method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}"/>

                                <div class="body">
                                    <div class="row clearfix">
                                        <div class="col-sm-6">
                                              <label class="form-label">Type</label>
                                              <select name="type" id="type" class="form-control show-tick selectpicker" data-live-search="true" tabindex="-98" required>
                                                  <option value="Sport" selected>Sport</option>
                                                  <option value="Bookmaker">Bookmaker</option>
                                              </select>
                                        </div>

                                        <div class="col-sm-6" id="sport_div">
                                              <label class="form-label">Sport</label>
                                              <select name="sport_id" id="sport_id" class="form-control show-tick selectpicker" data-live-search="true" tabindex="-98" required>
                                                  <option value="">Please select sport</option>
                                                  @foreach($sports as $sports_data)
                                                    <option value="{{ $sports_data->id }}">{{ $sports_data->name }}</option>
                                                  @endforeach
                                              </select>
                                        </div>

                                        <div class="col-sm-6" id="bookmaker_div" style="display:none;">
                                              <label class="form-label">Bookmaker</label>
                                              <select name="bookmaker_id" id="bookmaker_id" class="form-control show-tick selectpicker" data-live-search="true" tabindex="-98">
                                                  <option value="">Please select bookmaker</option>
                                                  @foreach($bookmakers as $bookmakers_data)
                                                    <option value="{{ $bookmakers_data->id }}">{{ $bookmakers_data->name }}</option>
                                                  @endforeach
                                              </select>
                                        </div>
                                    </div>

                                    <div class="row clearfix">
                                        <div class="col-sm-6">
                                              <label class="form-label">Agent</label>
                                              <select name="user_id" id="user_id" class="form-control show-tick selectpicker" data-live-search="true" tabindex="-98">
                                                  <option value="">Please select agent</option>
                                                  @foreach($users[0] as $users_data)
                                                    <option value="{{ $users_data['id'] }}" @if($loggedInUser==$users_data['id']) selected @endif>{{ $users_data['username'] }}</option>
                                                  @endforeach
                                              </select>
                                        </div>

                                        <div class="col-sm-6">
                                              <label for="percentage">Percentage</label>
                                                <div class="form-group">
                                                  <div class="form-line" style="padding:0 12px;">
                                                      <input type="text" name="percentage" id="percentage" class="form-control" placeholder="Enter percentage(numeric)" required>
                                                  </div>
                                                </div>
                                        </div>
                                    </div>

                                    <div class="row clearfix">
                                        <div class="col-sm-6">
                                              <label class="form-label">Action Type</label>
                                              <select name="action_type" id="action_type" class="form-control show-tick selectpicker" data-live-search="true" tabindex="-98" required>
                                                  <option value="">Please select action</option>
                                                  <option value="increase">Increase</option>
                                                  <option value="decrease">Decrease</option>
                                              </select>
                                        </div>

                                        <div class="col-sm-6">
                                            <label class="form-label">Status</label>
                                            <select name="status" id="status" class="form-control show-tick selectpicker" data-live-search="true" tabindex="-98" required>
                                                  <option value="">Please select status</option>
                                                  <option value="active">Active</option>
                                                  <option value="inactive">Inactive</option>
                                            </select>
                                        </div>
                                    </div>
                                    @if($show_default_field == true)
                                        <div class="row clearfix">
                                            <div class="col-sm-6">
                                                  <label class="form-label">Is Default</label>
                                                  <select name="is_default" id="is_default" class="form-control show-tick selectpicker" data-live-search="true" tabindex="-98" required>
                                                      <option value="no">No</option>
                                                      <option value="yes">Yes</option>
                                                  </select>
                                            </div>
                                        </div>
                                    @endif
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

<!----- Code for show hide sport & bookmaker div --->
<script>
$('#type').change(function()
{
	   var type = $(this).val();
     if(type == 'Sport')
     {
       $('#bookmaker_div').hide();
       $('#sport_div').show();
       $("#sport_id").attr('required', true);
       $("#bookmaker_id").attr('required', false);
     }
     else
     {
       $('#sport_div').hide();
       $('#bookmaker_div').show();
       $("#bookmaker_id").attr('required', true);
       $("#sport_id").attr('required', false);
     }
});
</script>
<!-------------------- end --------------->
