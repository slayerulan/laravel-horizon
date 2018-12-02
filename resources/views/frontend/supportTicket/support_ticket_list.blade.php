@extends('frontend.layout.layout')
	@section('main_body')
	@if (session('alert_msg'))
			<div class="alert alert-{{ session('alert_class') }} alert-dismissible">
				 <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">x</span></button>
				 <strong>{{ session('alert_msg') }}</strong>
			</div>
		@endif
	<div id="msgDiv" class="alert alert-success alert-dismissible" style="display:none;">
             <strong>Successfully Updated</strong>
    </div>
	<!-- Modal -->
  <div class="modal fade" id="creatSupportTicket" role="dialog" style="color: #000!important;">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
		<div class="modal-header">
			<h5 class="modal-title" id="exampleModalLabel">Create Support Ticket</h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<div class="modal-body">
		<form action="{{route('front-post-support-ticket')}}" id="ticketForm" method="post" enctype="multipart/form-data">
		<input type="hidden" name="_token" value="{{ csrf_token() }}"/>
		  <div class="form-group">
			<label for="title" class="col-form-label">Title:</label>
			<input type="text" class="form-control validate[required]" id="title" name="title" value="{{ old('full_name') }}" maxlength="50">
		  </div>
		  <div class="form-group">
			<label for="st_department_id" class="col-form-label">Ticket Department:</label>
			<select  class="form-control validate[required]" id="st_department_id" name="st_department_id" data-live-search="true" tabindex="-98" >
				<option value="">-- Please select --</option>
				@foreach($department as $department_data)
					<option value="{{ $department_data->id }}">{{ $department_data->name }}</option>
				@endforeach
			</select>
		  </div>
		  
		  <div class="form-group">
			<label for="st_priority_id" class="col-form-label">Ticket Priority:</label>
			<select  class="form-control validate[required]" id="st_priority_id" name="st_priority_id" data-live-search="true" tabindex="-98" >
				<option value="">-- Please select --</option>
				@foreach($priority as $priority_data)
					<option value="{{ $priority_data->id }}">{{ $priority_data->name }}</option>
				@endforeach
			</select>
		  </div>
		  
		  <div class="form-group">
			<label for="st_type_id" class="col-form-label">Ticket Type:</label>
			<select  class="form-control validate[required]" id="st_type_id" name="st_type_id" data-live-search="true" tabindex="-98" >
				<option value="">-- Please select --</option>
				@foreach($ticketType as $ticketType_data)
					<option value="{{ $ticketType_data->id }}">{{ $ticketType_data->name }}</option>
				@endforeach
			</select>
		  </div>
		  
		  <div class="form-group">
			<label for="st_status_type_id" class="col-form-label">Ticket Status Type:</label>
			<select  class="form-control validate[required]" id="st_status_type_id" name="st_status_type_id" data-live-search="true" tabindex="-98" >
				<option value="">-- Please select --</option>
				@foreach($statusType as $statusType_data)
					<option value="{{ $statusType_data->id }}">{{ $statusType_data->name }}</option>
				@endforeach
			</select>
		  </div>
		  
		  <div class="form-group">
			<label for="message" class="col-form-label">Message:</label>
			<textarea class="form-control validate[required]" name="message" id="messageForPlayer" ></textarea>
		  </div>
		  
		  <div class="form-group">
			<label class="custom-file">
			  <input type="file" id="file" name="file" class="custom-file-input">
			  <span class="custom-file-control">(only .jpeg / .jpg / .png / .pdf)</span>
			</label>
		  </div>
		
		</div>
		<div class="modal-footer">
		<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
		<input type="submit" value="Create" class="btn btn-primary">
		</div>
		</form>
	</div>
      
    </div>
  </div>
  


    
	<div class="col-md-12" style="background-color: white; padding-top: 15px; padding-bottom: 15px;color: #000!important;">
		<a href="javascript:void(0)" class="btn btn-xs btn-primary" data-toggle="modal" data-target="#creatSupportTicket"><span class="glyphicon glyphicon-plus-sign"></span>Add Ticket</a>
		<br><br>
		<div class="table-responsive datatable_st">
		<table class="crud-table table table-bordered table-striped table-hover dataTable js-exportable">
			<thead>
			  <tr>
				<th>Ticket No.</th>
				<th>Title</th>
				<th>Department</th>
				<th>Priority</th>
				<th>Status</th>
				<th>Type</th>
				<th>Action</th>
				<th>State</th>
				<th>Created at</th>
			  </tr>
			</thead>
			<tbody>
				@foreach ($totalData as $support_ticket_data)
                    <tr>
                        <td>{{ $support_ticket_data->ticket_number }}</td>
                        <td>{{ $support_ticket_data->title }}</td>
						<td>{{ $support_ticket_data->deptName }}</td>
						<td>{{ $support_ticket_data->priorityName }}</td>
						
                        <td class="ticket-select">
                            <select name="change_status" class="change_status form-control input-sm" data-value="{{ $support_ticket_data->id }}">
    							<option>Select</option>
                                @foreach($statusType as $statusType_data)
                                    <option value="{{ $statusType_data->id }}" @if($support_ticket_data->st_status_type_id == $statusType_data->id) selected @endif >{{ $statusType_data->name }}</option>
                                @endforeach
							</select>
                        </td>
                        <td>{{ $support_ticket_data->typeName }}</td>
                        <td>
							<a href="{{url('/').'/show-ticket-details/'.$support_ticket_data->ticket_number }}" class="waves-effect btn btn-info view_messeges" target="_blank" >Show</a>
						</td>
						<td>
						<?php
						if(in_array($support_ticket_data->id,$notSeenIds)){
							echo '<button type="button" class="btn btn-danger">Un-Read</button>';
						}
						else{
							echo '<button type="button" class="btn btn-success">Read</button>';
						}
						?>
						</td>
						<td>{{ $support_ticket_data->created_at }}</td>
                    </tr>
                    @endforeach
			</tbody>
			</table>
            </div>        
    </div>



    @endsection




