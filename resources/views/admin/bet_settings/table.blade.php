@extends('admin.layout.adminlayout')
	@section('content')

		<div class="table-responsive">
			<table class=" crud-table table table-bordered table-striped table-hover dataTable js-exportable">
				<thead>
					<tr>
						<th>Details</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					@forelse ($all_rules as $key => $value)
						<tr>
								<th>{{ str_limit($value->rule, 50, ' ...') }}</th>
								<th>
									<a href="{{ route('admin-sports-book-management-bet-settings-view').'/'.$value->id }}" class="waves-effect btn btn-info"><i class="material-icons">info</i>View</a>
									<a href="{{ route('admin-sports-book-management-bet-settings-edit').'/'.$value->id }}" class="waves-effect btn btn-warning"><i class="material-icons">create</i>Edit</a>
								</th>
						</tr>
					@empty
					@endforelse()
				</tbody>
			</table>
		</div>
	@stop
