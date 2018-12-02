@extends('admin.layout.adminlayout')
	@section('content')
		<div class="table-responsive">
			<table class="table table-bordered table-striped table-hover">
				<tbody>
					@forelse ($rules as $key => $value)
						@if(is_object($value))
							<tr class="par_row">
								<td colspan="2">{{ labelCase($key)}}</td>
							</tr>
							@foreach($value as $currency => $value_per_currency)
								<tr>
									<td >{{ labelCase($currency)}}</td>
									<td >{{ $value_per_currency }}</td>
								</tr>
							@endforeach
						@else
							<tr class="par_row">
	                            @if($key == 'bookmaker')
								<td >Bookmaker</td>
								<td >{{ $bookmaker_name }}</td>
								@else
								<td >{{ labelCase($key)}}</td>
								<td >{{ $value }}</td>
								@endif
							</tr>
						@endif
					@empty

					@endforelse
				</tbody>
			</table>
			<a href="{{ $back_url }}" class="btn btn-primary waves-effect">Back to list</a>
		</div>
	@stop
