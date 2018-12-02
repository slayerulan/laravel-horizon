@extends('admin.layout.adminlayout')
	@section('content')
    <div class="header"><h2>Market List</h2></div>
    <div class="table-responsive">
			<table id="marketTable" class="crud-table table table-bordered table-striped table-hover display" style="width:100%">
			<thead>
					<tr>
							<th>Sport</th>
							<th>Name</th>
							<th>Market Group</th>
							<th>Status</th>
							<th>Action</th>
					</tr>
			</thead>
	</table>
  </div>
@stop
