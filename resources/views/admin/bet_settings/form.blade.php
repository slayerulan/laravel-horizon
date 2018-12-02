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

				<div class="row clearfix">
					<div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
						<label for="pre-match">Default Sports Slug</label>
					</div>
					<div class="col-lg-10 col-md-10 col-sm-8 col-xs-7">
						<div class="form-group">
							<div class="form-line">
								<input type="text" name="default_sports_slug" id="default_sports_slug" class="form-control" placeholder="Enter Pre Match Hide Minutes" value="{{ $rule_details->default_sports_slug }}" required>
							</div>
						</div>
					</div>
				</div>

				<div class="row clearfix">
					<div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
						<label class="form-label">Bookmaker</label>
					</div>
					<div class="col-lg-10 col-md-10 col-sm-8 col-xs-7">
						<div class="form-group">
							<div class="form-line">
			                    <select name="default_bookmaker" class="form-control show-tick" data-live-search="true" tabindex="-98" required>
			                        <option value="">-- Please select --</option>
			                        @foreach($bookmaker as $bookmaker_data)
			                        <option value="{{ $bookmaker_data['id'] }}" @if($rule_details->default_bookmaker==$bookmaker_data['id']) selected @endif >{{ $bookmaker_data['name'] }}</option>
			                        @endforeach
			                    </select>
							</div>
						</div>
					</div>
				</div>

				<div class="row clearfix">
					<div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
						<label for="pre-match">Pre match hide minutes</label>
					</div>
					<div class="col-lg-10 col-md-10 col-sm-8 col-xs-7">
						<div class="form-group">
							<div class="form-line">
								<input type="text" name="hide_minute" id="hide_minute" class="form-control" placeholder="Enter Pre Match Hide Minutes" value="{{ $rule_details->hide_minute }}" required>
							</div>
						</div>
					</div>
				</div>

				<div class="row clearfix">
					<div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
						<label for="pre-match">Max hour to place bet</label>
					</div>
					<div class="col-lg-10 col-md-10 col-sm-8 col-xs-7">
						<div class="form-group">
							<div class="form-line">
								<input type="text" name="maximum_hour" id="maximum_hour" class="form-control" placeholder="Enter Max day to place bet" value="{{ $rule_details->maximum_hour }}" required>
							</div>
						</div>
					</div>
				</div>

				<!--div class="row clearfix">
					<div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
						<label for="pre-match">Maximum League Selection</label>
					</div>
					<div class="col-lg-10 col-md-10 col-sm-8 col-xs-7">
						<div class="form-group">
							<div class="form-line">
								<input type="text" name="maximum_league_selection" id="maximum_league_selection" class="form-control" placeholder="Enter Max day to place bet" value="" required>
							</div>
						</div>
					</div>
				</div-->


			<div class="row clearfix">
				<div class="col-lg-offset-2 col-md-offset-2 col-sm-offset-4 col-xs-offset-5">
					<input type="submit" class="btn btn-primary m-t-15 waves-effect" value="Submit" />
				</div>
			</div>
		</form>
	@stop
