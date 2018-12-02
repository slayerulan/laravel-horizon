<div class="col-md-12" style="background-color: white; padding-top: 15px; padding-bottom: 15px;color: #000!important;">
	<div class="table-responsive datatable_st">
		<div class="table_trans">
			<table class="table table-bordered table-striped">
				<thead>
					<tr>
						@foreach($columns as $column)
							<th>{{$column}}</th>
						@endforeach
					</tr>
				</thead>
				<tbody>
					@forelse ($table_data as $data)
						<tr>
							@foreach($data as $value)
								<td>{{$value}}</td>
							@endforeach
						</tr>
					@empty
						<tr>
							<td colspan="<?php echo count($columns);?>">No data found!</td>
						</tr>
					@endforelse
				</tbody>
			</table>
		</div>
		<div class="history_pagination_wrapper search_div_pagination">
	        @if($total_page > 1)
	        	@php
	          		$left_check = 0;$right_check = 0;
	        	@endphp
	        	<ul id="history_pagination" class="blade-pagination" data-current="{{$active_page}}" data-total="{{$total_page}}">
	          		<li class="page first @if($active_page == 1) disabled @endif" data-page="1">First</li>
	          		<li class="page prev @if($active_page == 1) disabled @endif" data-page="{{(int)$active_page-1}}">Prev</li>
	          		@for($i=1;$i<=$total_page;$i++)
	            		@if($i > $active_page-5 && $i < $active_page+5 )
	            			<li class="page @if($i == $active_page) active disabled @endif" data-page="{{$i}}">{{$i}}</li>
	            		@elseif($i > 1 && $i < $active_page-5 && !$left_check)
	              			@php
	              				$left_check = 1;
	              			@endphp
	              			<li class="disabled"><span>...</span></li>
	            		@elseif($i > $active_page+5 && $i < $total_page  && !$right_check)
	              			@php
	              				$right_check = 1;
	              			@endphp
	              			<li class="disabled"><span>...</span></li>
	            		@endif
	          		@endfor
	          		<li class="page next @if($active_page == $total_page) disabled @endif" data-page="{{(int)$active_page+1}}">Next</li>
	          		<li class="page last  @if($active_page == $total_page) disabled @endif" data-page="{{$total_page}}">Last</li>
	        	</ul>
			@endif
		</div>
	</div>
</div>