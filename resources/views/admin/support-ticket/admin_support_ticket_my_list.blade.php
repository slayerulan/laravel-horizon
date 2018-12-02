@extends('admin.layout.adminlayout')
	@section('content')
	<div id="msgDiv" class="alert alert-success alert-dismissible" style="display:none;">
             <strong>Successfully Updated</strong>
    </div>
    <div class="header"><h2>My Tickets</h2></div>
		@if($add_button)
			<a href="{{$add_button}}" class="btn btn-success add_button pull-right"><i class="material-icons">add_circle_outline</i>Add</a>
			<div class="devider"></div>
		@endif
		<div class="table-responsive">
			<table class=" crud-table table table-bordered table-striped table-hover dataTable js-exportable">
				<thead>
					<tr>
						<th>Ticket Number</th>
                        <th>Title</th>
                        <th>Created On</th>
												<th>Player</th>
                        <th>Allocate To</th>
                        <th>Action</th>
                        <th>View</th>
					</tr>
				</thead>
				<tbody>
					@foreach ($support_ticket['ticket_data'] as $support_ticket_data)
                    <tr>
                        <td>{{ $support_ticket_data['ticket_number'] }}</td>
                        <td>{{ $support_ticket_data['title'] }}</td>
                        <td>{{ $support_ticket_data['created_at'] }}</td>
												<td>{{ $support_ticket_data['player'][0]->username }}</td>
                        <td class="ticket-select">
                            <select name="allocate_to" class="allocate_to form-control input-sm" data-value="{{ $support_ticket_data['id'] }}">
																@foreach($support_ticket_data['agents'] as $agents_data)
                                    <option value="{{ $agents_data['id'] }}" @if($support_ticket_data['allocate_to']==$agents_data['id']) selected @endif >{{ $agents_data['username'] }}</option>
                                @endforeach
														</select>
                        </td>
                        <td class="ticket-select">
                            <select name="change_status" class="change_status form-control input-sm" data-value="{{ $support_ticket_data['id'] }}">
    							<option>Select</option>
                                @foreach($support_ticket['ticket_status_type'] as $ticket_status_type_data)
                                    <option value="{{ $ticket_status_type_data['id'] }}" @if($support_ticket_data['st_status_type_id']==$ticket_status_type_data['id']) selected @endif >{{ $ticket_status_type_data['name'] }}</option>
                                @endforeach
							</select>
                        </td>
                        <td>
							<a href="javascript:void(0)" class="waves-effect btn btn-info view_messeges" data-id="{{ $support_ticket_data['id'] }}"><i class="material-icons">info</i>Show @if($support_ticket_data['unread_msg'] >0)<span class="badge" id="unread_chat_header">{{ $support_ticket_data['unread_msg'] }}</span>@endif</a>
							<a href="{{ url('/').'/apex-site-admin/support-ticket-management/my-tickets/edit-my-ticket/'.$support_ticket_data['id'] }}" class="waves-effect btn btn-warning"><i class="material-icons">create</i>Edit</a>
							<a href="{{ url('/').'/apex-site-admin/support-ticket-management/my-tickets/delete/'.$support_ticket_data['id'] }}" class="waves-effect delete_button btn btn-danger"><i class="material-icons">delete_sweep</i>Delete</a>
						</td>
                    </tr>
                    @endforeach
				</tbody>
			</table>
		</div>

		<!--####	 View Support Ticket Modal start		####-->
		<div class="modal fade" id="viewStModal" role="dialog">
			<div class="modal-dialog">
				<!-- Modal content start -->
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title">Support Ticket Details</h4>
					</div>
					<div class="modal-body">
						<div class="" id="show_support_message"></div>
					</div>
				</div>
				<!-- Modal content end -->
			</div>
		</div>
		<!--####	 View Support Ticket Modal end		####-->
	@stop
