@extends('admin.layout.adminlayout')
	@section('content')
		<div class="table-responsive">
			<table class="table table-bordered table-striped table-hover">
				<tbody>
					@forelse ($label_data as $key => $value)
						<tr>
							<td >{{ $value }}</td>
							<td >{!! $details[$key] !!}</td>
						</tr>
					@empty
					@endforelse()
				</tbody>
			</table>			
		</div>
		<a href="{{ $back_url }}" class="btn btn-primary waves-effect">Back to list</a>
	@stop
