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
                            <h2>EDIT TICKET</h2>
                        </div>
                        <div class="body">
                            <form id="user-data" action="{{route('admin-support-ticket-management-edit-ticket')}}" method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}"/>
                                <input type="hidden" name="id" value="{{ $ticketData->id }}"/>

                                <div class="form-group form-float">
                                    <div class="form-line">
                                        <input type="text" class="form-control" name="ticket_number" value="{{ $ticketData->ticket_number }}" readonly required>
                                        <label class="form-label">Ticket Number</label>
                                    </div>
                                </div>

                                <div class="form-group form-float">
                                    <div class="form-line">
                                        <input type="text" class="form-control" name="title" value="{{ $ticketData->title }}" readonly required>
                                        <label class="form-label">Title</label>
                                    </div>
                                </div>

                                <div class="form-group form-float">
                                    <div class="form-line">
                                        <textarea name="message" cols="30" rows="5" class="form-control no-resize" readonly required>{{ $ticketData->message }}</textarea>
                                        <label class="form-label">Message</label>
                                    </div>
                                </div>

                                <div class="body">
                                    <div class="row clearfix">

                                        <div class="col-sm-6">
                                                <label class="form-label">Player</label>
                                                <select name="player_id" id="player_id" class="form-control show-tick selectpicker" data-live-search="true" tabindex="-98" disabled>
                                                    @foreach($player as $player_data)
                                                    <option value="{{ $player_data->id }}" selected  >{{ $player_data->username }}</option>
                                                    @endforeach
                                                </select>
                                        </div>
                                        <div class="col-sm-6">
                                                <label class="form-label">Allocate To</label>
                                                <select name="allocate_to" id="allocate_to" class="form-control show-tick selectpicker" data-live-search="true" tabindex="-98" required>
                                                    <option value="">-- Please select --</option>
                                                    @foreach($allUsers as $allUsers_data)
                                                    <option value="{{ $allUsers_data['id'] }}" @if($allUsers_data['id']==$ticketData->allocate_to) selected @endif >{{ $allUsers_data['username'] }}</option>
                                                    @endforeach
                                                </select>
                                        </div>

                                        <div class="col-sm-6">
                                            <label class="form-label">Ticket Department</label>
                                            <select name="st_department_id" id="st_department_id" class="form-control show-tick" data-live-search="true" tabindex="-98" required>
                                                <option value="">-- Please select --</option>
                                                @foreach($department as $department_data)
                                                    <option value="{{ $department_data->id }}" @if($department_data->id==$ticketData->st_department_id) selected @endif >{{ $department_data->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-sm-6">
                                            <label class="form-label">Ticket Type</label>
                                            <select name="st_type_id" id="st_type_id" class="form-control show-tick" data-live-search="true" tabindex="-98" required>
                                                <option value="">-- Please select --</option>
                                                @foreach($ticketType as $ticketType_data)
                                                    <option value="{{ $ticketType_data->id }}" @if($ticketType_data->id==$ticketData->st_type_id) selected @endif >{{ $ticketType_data->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-sm-6">
                                            <label class="form-label">Ticket Priority</label>
                                            <select name="st_priority_id" id="st_priority_id" class="form-control show-tick" data-live-search="true" tabindex="-98" required>
                                                <option value="">-- Please select --</option>
                                                @foreach($priority as $priority_data)
                                                    <option value="{{ $priority_data->id }}" @if($priority_data->id==$ticketData->st_priority_id) selected @endif >{{ $priority_data->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-sm-6">
                                            <label class="form-label">Ticket Status Type</label>
                                            <select name="st_status_type_id" id="st_status_type_id" class="form-control show-tick" data-live-search="true" tabindex="-98" required>
                                                <option value="">-- Please select --</option>
                                                @foreach($statusType as $statusType_data)
                                                    <option value="{{ $statusType_data->id }}" @if($statusType_data->id==$ticketData->st_status_type_id) selected @endif >{{ $statusType_data->name }}</option>
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
