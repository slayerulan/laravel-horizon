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
                            <h2>Edit Odds Juice</h2>
                        </div>
                        <div class="body">
                            <form id="user-data" action="{{route('admin-sports-book-management-odds-editing-update')}}" method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}"/>
                                <input type="hidden" name="id" value="{{ $oddsEditingData->id }}" />
                                <input type="hidden" name="editing_type" value="{{ $oddsEditingData->type }}" />

                                <div class="body">
                                    <div class="row clearfix">
                                        <div class="col-sm-6">
                                              <label class="form-label">Type</label>
                                              <select name="type" id="type" class="form-control show-tick selectpicker" data-live-search="true" tabindex="-98" disabled>
                                                  <option value="Sport" @if($oddsEditingData->type=='Sport') selected @endif >Sport</option>
                                                  <option value="Bookmaker" @if($oddsEditingData->type=='Bookmaker') selected @endif >Bookmaker</option>
                                              </select>
                                        </div>

                                        @if($oddsEditingData->type=='Sport')
                                        <input type="hidden" name="sports_id" value="{{ $oddsEditingData->sport_id }}" />
                                        <div class="col-sm-6" id="sport_div">
                                              <label class="form-label">Sport</label>
                                              <select name="sport_id" id="sport_id" class="form-control show-tick selectpicker" data-live-search="true" tabindex="-98" disabled>
                                                  <option value="">Please select sport</option>
                                                  @foreach($sports as $sports_data)
                                                    <option value="{{ $sports_data->id }}" @if($oddsEditingData->sport_id==$sports_data->id) selected @endif >{{ $sports_data->name }}</option>
                                                  @endforeach
                                              </select>
                                        </div>
                                        @endif

                                        @if($oddsEditingData->type=='Bookmaker')
                                            <input type="hidden" name="bookmakers_id" value="{{ $oddsEditingData->bookmaker_id }}" />
                                            <div class="col-sm-6" id="bookmaker_div">
                                                <label class="form-label">Bookmaker</label>
                                                <select name="bookmaker_id" id="bookmaker_id" class="form-control show-tick selectpicker" data-live-search="true" tabindex="-98" disabled>
                                                    <option value="">Please select bookmaker</option>
                                                    @foreach($bookmakers as $bookmakers_data)
                                                      <option value="{{ $bookmakers_data->id }}" @if($oddsEditingData->bookmaker_id==$bookmakers_data->id) selected @endif >{{ $bookmakers_data->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="row clearfix">
                                        <div class="col-sm-6">
                                              <label class="form-label">Agent</label>
                                              <select name="user_id" id="user_id" class="form-control show-tick selectpicker" data-live-search="true" tabindex="-98" disabled>
                                                  <option value="">Please select agent</option>
                                                  @foreach($users[0] as $users_data)
                                                    <option value="{{ $users_data['id'] }}" @if($oddsEditingData->user_id==$users_data['id']) selected @endif >{{ $users_data['username'] }}</option>
                                                  @endforeach
                                              </select>
                                        </div>



                                        <div class="col-sm-6">
                                                <label for="percentage">Percentage</label>
                                                  <div class="form-group">
                                                    <div class="form-line" style="padding:0 12px;">
                                                        <input type="text" name="percentage" id="percentage" class="form-control" value="{{ $oddsEditingData->percentage }}" placeholder="Enter percentage(numeric)" required>
                                                    </div>
                                                  </div>
                                          </div>

                                    </div>

                                    <div class="row clearfix">
                                        <div class="col-sm-6">
                                              <label class="form-label">Action Type</label>
                                              <select name="action_type" id="action_type" class="form-control show-tick selectpicker" data-live-search="true" tabindex="-98" required>
                                                  <option value="">Please select action</option>
                                                  <option value="increase" @if($oddsEditingData->action_type=='increase') selected @endif >Increase</option>
                                                  <option value="decrease" @if($oddsEditingData->action_type=='decrease') selected @endif >Decrease</option>
                                              </select>
                                        </div>

                                        @if($show_default_field == false && $oddsEditingData->is_default == 'yes')
                                        @else
                                            <div class="col-sm-6">
                                                <label class="form-label">Status</label>
                                                <select name="status" id="status" class="form-control show-tick selectpicker" data-live-search="true" tabindex="-98" required>
                                                      <option value="">Please select status</option>
                                                      <option value="active" @if($oddsEditingData->status=='active') selected @endif >Active</option>
                                                      <option value="inactive" @if($oddsEditingData->status=='inactive') selected @endif >Inactive</option>
                                                </select>
                                            </div>
                                        @endif
                                    </div>
                                    @if($show_default_field == true)
                                        <div class="row clearfix">
                                            <div class="col-sm-6">
                                                <label class="form-label">Is Default</label>
                                                <select name="is_default" id="is_default" class="form-control show-tick selectpicker" data-live-search="true" tabindex="-98" required>
                                                    <option value="no">No</option>
                                                    <option value="yes" <?php if(isset($oddsEditingData->is_default)){if($oddsEditingData->is_default == 'yes'){echo 'selected';}}?>>Yes</option>
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
     if(type == 'sport')
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
