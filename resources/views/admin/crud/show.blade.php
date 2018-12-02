@extends('admin.layout.adminlayout')
	@section('content')
		@if($add_button)
			<a href="{{$add_button['link']}}" class="{{$add_button['class']}} pull-right add_button"><i class="material-icons">{{$add_button['icon']}}</i>{{ $add_button['label'] }}</a>
			<div class="devider"></div>
		@endif
		<div class="table-responsive">
			<table class=" crud-table table table-bordered table-striped table-hover dataTable js-exportable">
				<thead>
					<tr>
					@forelse ($table_field as $key => $value)
						<th>{{ $value }}</th>
					@empty
						<th>{{ 'No Title Found' }}</th>
					@endforelse()
					</tr>
				</thead>
				<tbody>
					@forelse ($table_data as $key => $value)
						<tr>
							@forelse ($table_field as $field_name => $field_label)
								<td >{!! $value[$field_name] !!}</td>
							@empty
							@endforelse()
						</tr>
					@empty
					@endforelse()
				</tbody>
			</table>
		</div>
	@stop
