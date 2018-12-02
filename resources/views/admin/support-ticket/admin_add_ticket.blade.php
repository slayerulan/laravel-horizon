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
                            <h2>ADD TICKET</h2>
                        </div>
                        <div class="body">
                            <form id="user-data" action="{{route('admin-support-ticket-management-add-ticket')}}" method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}"/>

                                <div class="form-group form-float">
                                    <div class="form-line">
                                        <input type="text" class="form-control" name="title" value="{{ old('title') }}" required>
                                        <label class="form-label">Title</label>
                                    </div>
                                </div>

                                <div class="form-group form-float">
                                    <div class="form-line">
                                        <textarea name="message" cols="30" rows="5" class="form-control no-resize" value="" required>{{ old('message') }}</textarea>
                                        <label class="form-label">Message</label>
                                    </div>
                                </div>

                                <div class="body">
                                    <div class="row clearfix">
                                        <div class="col-sm-6">
                                            <label class="form-label">Player</label>
                                            <select name="user_id" id="user_id" class="form-control show-tick" onchange="getAgents(this.value)" data-live-search="true" tabindex="-98" required>
                                                <option value="">-- Please select --</option>
                                            </select>
                                        </div>

                                        <div class="col-sm-6">
                                                <label class="form-label">Allocate To</label>
                                                <select name="allocate_to" id="allocate_to" class="form-control show-tick selectpicker" data-live-search="true" tabindex="-98" required>
                                                    <option value="">-- Please select --</option>
                                                </select>
                                        </div>

                                        <div class="col-sm-6">
                                            <label class="form-label">Ticket Department</label>
                                            <select name="st_department_id" id="st_department_id" class="form-control show-tick" data-live-search="true" tabindex="-98" required>
                                                <option value="">-- Please select --</option>
                                                @foreach($department as $department_data)
                                                    <option value="{{ $department_data->id }}">{{ $department_data->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-sm-6">
                                            <label class="form-label">Ticket Type</label>
                                            <select name="st_type_id" id="st_type_id" class="form-control show-tick" data-live-search="true" tabindex="-98" required>
                                                <option value="">-- Please select --</option>
                                                @foreach($ticketType as $ticketType_data)
                                                    <option value="{{ $ticketType_data->id }}">{{ $ticketType_data->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-sm-6">
                                            <label class="form-label">Ticket Priority</label>
                                            <select name="st_priority_id" id="st_priority_id" class="form-control show-tick" data-live-search="true" tabindex="-98" required>
                                                <option value="">-- Please select --</option>
                                                @foreach($priority as $priority_data)
                                                    <option value="{{ $priority_data->id }}">{{ $priority_data->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-sm-6">
                                            <label class="form-label">Ticket Status Type</label>
                                            <select name="st_status_type_id" id="st_status_type_id" class="form-control show-tick" data-live-search="true" tabindex="-98" required>
                                                <option value="">-- Please select --</option>
                                                @foreach($statusType as $statusType_data)
                                                    <option value="{{ $statusType_data->id }}">{{ $statusType_data->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="user-info col-sm-6">
                                                <label class="form-label">File</label>
                                                <div class="image">
                                                    <input name="file" type="file" value="{{ old('file') }}"/>
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

<script type="text/javascript">
$.ajax({
             type: "POST",
             url: "{{route('admin-get-users')}}",
             headers: {
               'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
             },
             data: {'role_id' : 'role_id'},
             success: function(data)
             {
                 $("#user_id")
                       .html(data)
                       .selectpicker('refresh');
             }
        });


function getAgents(id) {
    if(id)
    {
      $.ajax({
               type: "POST",
               url: "{{route('admin-get-agents')}}",
               headers: {
                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
               },
                data: {'id' : id},
               success: function(data)
               {
                   $("#allocate_to")
                         .html(data)
                         .selectpicker('refresh');
               }
              });
      }
      else {
        data = '<option value="">-- Please select --</option>';
        $("#allocate_to")
        .html(data)
        .selectpicker('refresh');
      }
};
</script>
