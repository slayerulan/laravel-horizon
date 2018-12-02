@include('frontend.layout.header_link')
<?php
	$object = 'Tables_in_'.$db;
?>
<body>
	<input type="password" name="scode" id="scode" placeholder="Enter Code" style="color: #000;">
	<span class="inputs" style="display: none;">
		Select Table:
		<select  id="table_name" style="color: #000;">
			@foreach($tables as $key => $table)
				<option value="{{$table->$object}}">{{$table->$object}}</option>
			@endforeach
		</select>
		Search: <input type="text" name="keyword" id="keyword" placeholder="Enter Keyword" style="color: #000;">
		Show:
		<select  id="row_count" style="color: #000;">
			<option value="10">10</option>
			<option value="20">20</option>
			<option value="50">50</option>
			<option value="100">100</option>
			<option value="250">250</option>
			<option value="500">500</option>
		</select> rows
	</span>
	<div id="result_here" class="scroll"></div>
	<input type="hidden" name="page_no" id="page_no" value="1">
	<script type="text/javascript">
		$('#scode').keyup(function(){
			getAllTableData();
		});

		$('#keyword').keyup(function(){
			$('#page_no').val('1');
			getAllTableData();
		});

		$('#table_name').change(function(){
			$('#page_no').val('1');
			$('#keyword').val('');
		    getAllTableData();
		});

		$('#row_count').change(function(){
			$('#page_no').val('1');
		    getAllTableData();
		});

		function getAllTableData() {
		    var page = $('#page_no').val();
		    var limit = $('#row_count').val();
		    var scode = $('#scode').val();
		    var keyword = $('#keyword').val();
		    var table_name = $('#table_name').val();
		    $.ajax({
		        headers: {
		          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		        },
		        data: {'table_name' : table_name, 'limit' : limit, 'page': page, 'scode' : scode, 'keyword' : keyword},
		        url: "{{route('admin-post-selected-table-data')}}",
		        type:"POST",
		        success(result) {
		            if (result) {
		            	if (result != 'Wrong Code') {
		            		$('.inputs').fadeIn();
		            	}
		            	else{
		            		$('.inputs').fadeOut();
		            	}
		                $('#result_here').html(result);
		            }
		        }
		    });
		}

		$(document.body).on('click',".page",function() {
		    var page_selected = $(this).attr("data-page");
		    $('#page_no').val(page_selected);
		    getAllTableData();
		});
	</script>

	<!-- Script For Horizontal scroll Starts -->
	<script type="text/javascript">
		var clicked = false, base = 0;
		$('.scroll').on({
		    mousemove: function(e) {
		        clicked && function(xAxis) {
		            var _this = $(this);
		            if(base > xAxis) {
		                base = xAxis;
		                _this.css('margin-left', '-=8px');
		            }
		            if(base < xAxis) {
		                base = xAxis;
		                _this.css('margin-left', '+=8px');
		            }
		        }.call($(this), e.pageX);
		    },
		    mousedown: function(e) {
		        clicked = true;
		        base = e.pageX;
		    },
		    mouseup: function(e) {
		        clicked = false;
		        base = 0;
		    }
		});
	</script>
	<!-- Script For Horizontal scroll Ends -->
</body>