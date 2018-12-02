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
		<form class="form-horizontal" action="{{ $save_url }}" method="POST">
			{{ csrf_field() }}
			@if(isset($rule_details->id))
				<input type="hidden" name="id" value="{{ $rule_details->id }}"/>
			@endif
			<div class="row clearfix">
				<div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
					<label for="title">Title</label>
				</div>
				<div class="col-lg-10 col-md-10 col-sm-8 col-xs-7">
					<div class="form-group">
						<div class="form-line">
							<input type="text" name="title" id="title" class="form-control" placeholder="Enter title" value="{{  old('title',$rule_details->title) }}">
						</div>
					</div>
				</div>
			</div>
			@if($show_default_field == true)
				<div class="row clearfix">
					<div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
						<label>Is Default</label>
					</div>
					<div class="col-lg-10 col-md-10 col-sm-8 col-xs-7">
						<div class="form-group">
							<div class="form-line">
								<select name="is_default" class="form-control show-tick" data-live-search="true" tabindex="-98" required>
					                <option value="no">No</option>
					                <option value="yes" <?php if(isset($rule_details->is_default)){if($rule_details->is_default == 'yes'){echo 'selected';}}?>>Yes</option>
					            </select>
							</div>
						</div>
					</div>
				</div>
			@endif

			@forelse ($rules as $field_name)
				<div class="row clearfix">
					<div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
						<label for="{{$field_name}}">{{  labelCase($field_name) }}</label>
					</div>
					@if($field_name=='bookmaker')
						{{-- <div class="col-lg-10 col-md-10 col-sm-8 col-xs-7">
							<div class="form-group">
								<div class="form-line">
									<select name="bookmaker" class="form-control show-tick" data-live-search="true" tabindex="-98" required>
				                  		<option value=""> Please select </option>
				                  		@foreach($bookmaker as $bookmaker_data)
											@if ($rule_details->bookmaker == $bookmaker_data['id'])
							       				<option value="{{ $bookmaker_data['id'] }}" selected>{{ $bookmaker_data['name'] }}</option>
											@else
										    	<option value="{{ $bookmaker_data['id'] }}">{{ $bookmaker_data['name'] }}</option>
											@endif
				                  		@endforeach
				              		</select>
								</div>
							</div>
						</div> --}}
					@elseif($field_name == 'maximum_number_of_bets_per_parlay' || $field_name == 'minimum_number_of_bets_per_parlay')
						<div class="col-lg-10 col-md-10 col-sm-8 col-xs-7">
							<div class="form-group">
								<div class="form-line">
									<input type="text" name="{{$field_name}}" id="{{$field_name}}" class="form-control" placeholder="Enter {{  labelCase($field_name) }}" value="{{  old($field_name,$rule_details->{$field_name}) }}">
								</div>
							</div>
						</div>
					@else
						<div class="col-lg-10 col-md-10 col-sm-8 col-xs-7">
							<div class="row">
						@foreach($currencies as $currency)
							<div class="col-lg-6 col-md-6 col-sm-12 col-xs-6">
								<div class="form-group">
										<div class="form-line">
											<label for="{{$currency['curency_name']}}">{{labelCase($currency['curency_name'])}}</label>
											<input type="text" name="{{$field_name}}[{{$currency['curency_name']}}]" id="{{$field_name}}_{{$currency['curency_name']}}" class="form-control" placeholder="Enter {{  labelCase($field_name) }} for {{  labelCase($currency['curency_name']) }}" value="@if(isset($rule_details->{$field_name}->{$currency['curency_name']})) {{  old($field_name,$rule_details->{$field_name}->{$currency['curency_name']}) }} @endif">
										</div>
									</div>
							</div>
						@endforeach
							</div>
						</div>	
					@endif
				</div>
			@empty

			@endforelse

			<div class="row clearfix">
				<div class="col-lg-offset-2 col-md-offset-2 col-sm-offset-4 col-xs-offset-5">
					<input type="submit" class="btn btn-primary m-t-15 waves-effect" value="Submit" />
				</div>
			</div>
		</form>
	@stop
