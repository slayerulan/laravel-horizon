@extends('frontend.layout.layout')
@section('main_body')
	<div class="brb-heading">Transaction History</div>
	<div class="col-md-12" style="background-color: white; padding-top: 15px; padding-bottom: 15px;color: #000!important;">
		<div class="table-responsive datatable_st">
			<div id="" class="">
				<label><input class="form-control" placeholder="Search" type="search" id="transaction_search"></label>
			</div>

			<div class="table_trans_1">
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
						<div class="dataTables_paginate paging_simple_numbers" id="table_transaction_paginate">
							{{$page->links()}}
						</div>
					</div>
				</div>
			</div>
			<div class="table_trans" style="display: none;"></div>
		</div>
    </div>
    <script type="text/javascript">
    	$(document.body).on('keyup',"#transaction_search",function(){
    		var key = $('#transaction_search').val();
    		if (key != '') {
    			$('.table_trans_1').fadeOut();
    			$('.table_trans').fadeIn();
	    		$.ajax({
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					type : 'POST',
					data : {'key': key},
					url  : "{{ route('front-search-transactions') }} ",
					success: function(data){
						$('.table_trans').html(data);
					}
			    });
    		}
    		else{
    			$('.table_trans').fadeOut();
    			$('.table_trans_1').fadeIn();
    			$('.table_trans').html('');
    		}
    	});
    	$(document.body).on('click',".paginate",function(){
    		var page = $(this).attr('data-page');
    		var key = $('#transaction_search').val();
    		$.ajax({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				type : 'POST',
				data : {'key': key, 'page': page},
				url  : "{{ route('front-search-transactions-paginate') }} ",
				success: function(data){
					$('.table_trans').html(data);
				}
		    });
    	});
    </script>
@endsection