@extends('admin.layout.adminlayout')
	@section('content')
		@if($add)
			<a href="{{ route('admin-settings-bet-rules-add') }}" class="btn btn-success add_button pull-right"><i class="material-icons">add_circle_outline</i>Add Rule</a>
			<div class="devider"></div>
		@endif
		<div class="table-responsive">
			<table class=" crud-table table table-bordered table-striped table-hover dataTable js-exportable">
				<thead>
					<tr>
						@if(Session::get('role_id') == 1)
						<th>Agent</th>
						@endif
						<th>Title</th>
						<th>Details</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					@forelse ($all_rules as $key => $value)
						<tr>
							@if(Session::get('role_id') == 1)
								<th>{{ !empty($value->user) ? $value->user->username : 'Admin' }}</th>
							@endif
								<th>{{ $value->title }}</th>
								<th>{{ str_limit($value->rule, 50, ' ...') }}</th>
								<th>
									<a href="{{ route('admin-settings-bet-rules-view').'/'.$value->id }}" class="waves-effect btn btn-info"><i class="material-icons">info</i>View</a>
									@if($modify)
										<a href="{{ route('admin-settings-bet-rules-edit').'/'.$value->id }}" class="waves-effect btn btn-warning"><i class="material-icons">create</i>Edit</a>
										<a href="{{ route('admin-settings-bet-rules-delete').'/'.$value->id }}" class="waves-effect  btn btn-danger delete_button"><i class="material-icons">delete_sweep</i>Delete</a>
									@endif
								</th>
						</tr>
					@empty
					@endforelse()
				</tbody>
			</table>
		</div>
	@stop
