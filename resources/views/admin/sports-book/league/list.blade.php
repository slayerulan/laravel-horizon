@extends('admin.layout.adminlayout')
	@section('content')
    <div class="header"><h2>League List</h2></div>
    <div class="table-responsive">
			<table id="leagueTable" class="crud-table table table-bordered table-striped table-hover display" style="width:100%">
			<thead>
					<tr>
							<th>Sport</th>
							<th>Country</th>
							<th>Name</th>
							<th>Priority</th>
							<th>Is Top</th>
							<th>Status</th>
							<th>Action</th>
					</tr>
			</thead>
	</table>
  </div>
@stop
