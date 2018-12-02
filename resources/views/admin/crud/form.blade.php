@extends('admin.layout.adminlayout')
	@section('content')
		@if ($errors->any())
		<div class="alert alert-danger form_validation_error">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">x</span></button>
			<ul class="error_list">
				@foreach ($errors->all() as $error)
			   		<li><i class="material-icons">info_outline</i> {{ $error }}</li>
		   		@endforeach
			</ul>
		</div>
		@endif


		<div class="col-md-8 col-sm-8 col-xs-12 col-md-offset-2 col-sm-offset-2 col-xs-offset-0">
			<form class="form-horizontal" action="{{ $insert_url }}" method="POST" enctype="multipart/form-data">
				{{ csrf_field() }}
				@forelse ($input_list as $field_name => $each_input)
					@if($each_input['field_type'] == 'hidden')
						<input type="{{  $each_input['field_type'] }}" name="{{$each_input['field_name']}}" id="{{$each_input['field_name']}}" class="{{$each_input['class_name']}}" placeholder="Enter {{  $each_input['field_label'] }}" value="{{   $each_input['default_value'] }}" {{  $each_input['extra_attribute'] }}>
					@else
					<div class="row clearfix">
						<div class="col-lg-2 col-md-2 col-sm-4 col-xs-12 form-control-label">
							<label for="{{$each_input['field_name']}}" class="title">{{  $each_input['field_label'] }}</label>
						</div>
						@if (empty($each_input['raw_html']))
							@if (in_array($each_input['field_type'],["radio","checkbox"]))
								<div class="row clearfix">
									<div class="col-lg-offset-2 col-md-offset-2 col-sm-offset-4 col-xs-offset-5">
									@if(is_array($each_input['option_values']))
										@foreach ($each_input['option_values'] as $key => $label)
											@if ($each_input['field_type'] == "radio")
												<input value="{{ $key }}"
													@if($key == old($each_input['field_name'], $each_input['default_value']))
														checked
													@endif
												name="{{$each_input['field_name']}}" type="radio" id="{{$each_input['field_name'].$key }}" class="with-gap radio-col-blue-grey {{$each_input['class_name']}}"  {{  $each_input['extra_attribute'] }} />
											@else
												<input value="{{ $key }}"
												@if($key == old($each_input['field_name'], $each_input['default_value']))
													checked
												@endif
												type="checkbox" name="{{$each_input['field_name']}}[]" id="{{$each_input['field_name'].$key }}" class="filled-in chk-col-blue-grey {{$each_input['class_name']}}"  {{  $each_input['extra_attribute'] }}>
											@endif
											<label for="{{$each_input['field_name'].$key }}">{{ $label }}</label>
										@endforeach
									@endif
									</div>
								</div>
							@elseif(in_array($each_input['field_type'],["select","file","muliselect"]))

								<div class="col-lg-10 col-md-10 col-sm-8 col-xs-12">

								<div class="form-group">
										<div class="form-line">
									@if($each_input['field_type'] == "file")
										<input type="file" value="{{ old($each_input['field_name'], $each_input['default_value']) }}" name="{{$each_input['field_name']}}" id="{{$each_input['field_name']}}" class="{{$each_input['class_name']}}" placeholder="Enter {{  $each_input['field_label'] }}" {{  $each_input['extra_attribute'] }}>
									@else
										@if($each_input['field_type'] == "select")
											<select name="{{$each_input['field_name']}}" id="{{$each_input['field_name']}}" class="show-tick {{$each_input['class_name']}}" {{  $each_input['extra_attribute'] }}>
										@else
											<select name="{{$each_input['field_name']}}" id="{{$each_input['field_name']}}" class="ms multiSelect_input {{$each_input['class_name']}}" multiple="multiple" {{  $each_input['extra_attribute'] }}>
										@endif
												<option value="" >{{ __('select') }}</option>
										@if(is_array($each_input['option_values']))
											@foreach ($each_input['option_values'] as $key => $label)
												<option value="{{ $key }}" @if($key == old($each_input['field_name'],$each_input['default_value'])) selected @endif >{{ $label }}</option>
											@endforeach
										@endif
									</select>
									@endif
									</div>
								</div>
								</div>
							@else
								<div class="col-lg-10 col-md-10 col-sm-8 col-xs-12">
									<div class="form-group">
										<div class="form-line">
										@if ($each_input['field_type'] == "date")
											<input type="text" name="{{$each_input['field_name']}}" id="{{$each_input['field_name']}}" class="datepicker {{$each_input['class_name']}}" placeholder="Enter {{  $each_input['field_label'] }}" value="{{  old($each_input['field_name'], $each_input['default_value']) }}" {{  $each_input['extra_attribute'] }}>

										@elseif ($each_input['field_type'] == "time")
											<input type="text" name="{{$each_input['field_name']}}" id="{{$each_input['field_name']}}" class="timepicker {{$each_input['class_name']}}" placeholder="Enter {{  $each_input['field_label'] }}" value="{{  old($each_input['field_name'], $each_input['default_value']) }}" {{  $each_input['extra_attribute'] }}>

										@elseif ($each_input['field_type'] == "date-time")
											<input type="text" name="{{$each_input['field_name']}}" id="{{$each_input['field_name']}}" class="datetimepicker {{$each_input['class_name']}}" placeholder="Enter {{  $each_input['field_label'] }}" value="{{  old($each_input['field_name'], $each_input['default_value']) }}" {{  $each_input['extra_attribute'] }}>

										@elseif ($each_input['field_type'] == "textarea")
											<textarea name="{{$each_input['field_name']}}" id="{{$each_input['field_name']}}" class="no-resize {{$each_input['class_name']}}"  {{  $each_input['extra_attribute'] }} >{{  old($each_input['field_name'], $each_input['default_value']) }}</textarea>

										@elseif ($each_input['field_type'] == "editor")
											<textarea name="{{$each_input['field_name']}}" id="{{$each_input['field_name']}}" class="ckeditor {{$each_input['class_name']}}"  {{  $each_input['extra_attribute'] }} >{{  old($each_input['field_name'], $each_input['default_value']) }}</textarea>

										@else
											<input type="{{  $each_input['field_type'] }}" name="{{$each_input['field_name']}}" id="{{$each_input['field_name']}}" class="{{$each_input['class_name']}}" placeholder="Enter {{  $each_input['field_label'] }}" value="{{  old($each_input['field_name'], $each_input['default_value']) }}" {{  $each_input['extra_attribute'] }}>
										@endif
										</div>
									</div>
								</div>
							@endif
						@else
							<div class="col-lg-10 col-md-10 col-sm-8 col-xs-12">
								<div class="form-group">
									<div class="form-line">
										{!! $each_input['raw_html']  !!}
									</div>
								</div>
							</div>
						@endif
					</div>
					@endif
				@empty

				@endforelse
				<div class="row clearfix">
					<div class="col-lg-offset-2 col-md-offset-2 col-sm-offset-4 col-xs-offset-5">
						<input type="submit" class="btn btn-primary m-t-15 waves-effect" value="Submit" />
					</div>
				</div>
			</form>
		</div>
		<div class="clearfix"></div>
	@stop
