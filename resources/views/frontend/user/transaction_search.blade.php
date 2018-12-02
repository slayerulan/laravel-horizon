<table id="table_transaction" class="table table-bordered table-striped">
	<thead>
		<tr>
			<th>#</th>
			<th>Title</th>
			<th>Type</th>
			<th>Amount</th>
			<th>Date</th>
		</tr>
	</thead>
	<tbody>
		@forelse($transactions as $key => $transaction)
			<tr>
                <td>{{$key+1}}</td>
                <td>{{$transaction->title}}</td>
                <td>{{$transaction->type}}</td>
                <td>{{$transaction->amount}}</td>
                <td>{{$transaction->created_at}}</td>
			</tr>
		@empty
			<tr>
                <td colspan="5" style="text-align: center;">NO RESULT FOUND</td>
			</tr>
		@endforelse
	</tbody>
</table>
<div class="row">
	<div class="col-sm-7">
		<div class="dataTables_paginate paging_simple_numbers">
			@if($total_page > 1)
	        	@php
	          		$left_check = 0;$right_check = 0;
	        	@endphp
	        	<ul class="pagination">
	          		<li class="@if($page_no == 1) disabled @endif">
						@if(($page_no-1) == 0)
							<span>«</span>
						@else
							<a href="javascript:void(0)" class="paginate" data-page="{{$page_no-1}}">«</a>
						@endif
					</li>
	          		@for($i=1;$i<=$total_page;$i++)
	            		@if($i > $page_no-5 && $i < $page_no+5 )
	            			<li class="@if($i == $page_no) active @endif">
	            				<a href="javascript:void(0)" class="paginate" data-page="{{$i}}">{{$i}}</a>
	            			</li>
	            		@elseif($i > 1 && $i < $page_no-5 && !$left_check)
	              			@php
	              				$left_check = 1;
	              			@endphp
	              			<li class="disabled"><span>...</span></li>
	            		@elseif($i > $page_no+5 && $i < $total_page  && !$right_check)
	              			@php
	              				$right_check = 1;
	              			@endphp
	              			<li class="disabled"><span>...</span></li>
	            		@endif
	          		@endfor
	          		<li class="@if($page_no == $total_page) disabled @endif">
						@if(($page_no+1) > $total_page)
							<span>»</span>
						@else
							<a href="javascript:void(0)" class="paginate" data-page="{{$page_no+1}}">»</a>
						@endif
					</li>
	        	</ul>
			@endif
		</div>
	</div>
</div>