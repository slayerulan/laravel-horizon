@extends('admin.layout.adminlayout')
	@section('content')
		<div class="table-responsive">
			<table class="table table-bordered table-striped table-hover">
				<tbody>
					@forelse ($rules as $key => $value)
						<tr>
							@if($key == 'default_bookmaker')
							<td >Bookmaker</td>
							<td >{{ $bookmaker_name }}</td>
							@else
							<td >{{ labelCase($key)}}</td>
							<td >{{ $value }}</td>
							@endif

						</tr>
					@empty

					@endforelse
				</tbody>
			</table>
			<a href="{{ $back_url }}" class="btn btn-primary waves-effect">Back to list</a>
		</div>
	@stop
