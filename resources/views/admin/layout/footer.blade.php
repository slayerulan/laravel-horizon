<!-- Jquery Core Js -->
    <script src="{{ asset("/admin/plugins/jquery/jquery.min.js") }}"></script>

    <!-- Bootstrap Core Js -->
    <script src="{{ asset("/admin/plugins/bootstrap/js/bootstrap.js") }}"></script>



    <!-- Dropzone Plugin Js -->
    <script src="{{ asset("/admin/plugins/dropzone/dropzone.js") }}"></script>
    <!-- Select Plugin Js -->
    <script src="{{ asset("/admin/plugins/bootstrap-select/js/bootstrap-select.js") }}"></script>


    <!-- Slimscroll Plugin Js -->
    <script src="{{ asset("/admin/plugins/jquery-slimscroll/jquery.slimscroll.js") }}" ></script>

    <!-- Waves Effect Plugin Js -->
    <script src="{{ asset("/admin/plugins/node-waves/waves.js") }}"  ></script>

    <!-- Jquery CountTo Plugin Js -->
    <script src="{{ asset("/admin/plugins/jquery-countto/jquery.countTo.js") }}" ></script>

    <!-- Morris Plugin Js -->
    <script src="{{ asset("/admin/plugins/raphael/raphael.min.js") }}" ></script>
    <script src="{{ asset("/admin/plugins/morrisjs/morris.js") }}" ></script>

    <!-- ChartJs -->
    <script src="{{ asset("/admin/plugins/chartjs/Chart.bundle.js") }}" ></script>

    <!-- Flot Charts Plugin Js -->
    <script src="{{ asset("/admin/plugins/flot-charts/jquery.flot.js") }}" ></script>
    <script src="{{ asset("/admin/plugins/flot-charts/jquery.flot.resize.js") }}" ></script>
    <script src="{{ asset("/admin/plugins/flot-charts/jquery.flot.pie.js") }}" ></script>
    <script src="{{ asset("/admin/plugins/flot-charts/jquery.flot.categories.js") }}" ></script>
    <script src="{{ asset("/admin/plugins/flot-charts/jquery.flot.time.js") }}" ></script>

    <!-- Sparkline Chart Plugin Js -->
    <script src="{{ asset("/admin/plugins/jquery-sparkline/jquery.sparkline.js") }}" ></script>

	<!-- Jquery DataTable Plugin Js -->
    <script src="{{ asset("/admin/plugins/jquery-datatable/jquery.dataTables.js") }}"></script>
    <script src="{{ asset("/admin/plugins/jquery-datatable/skin/bootstrap/js/dataTables.bootstrap.js") }}"></script>
    <script src="{{ asset("/admin/plugins/jquery-datatable/extensions/export/dataTables.buttons.min.js") }}"></script>
    <script src="{{ asset("/admin/plugins/jquery-datatable/extensions/export/buttons.flash.min.js") }}"></script>
    <script src="{{ asset("/admin/plugins/jquery-datatable/extensions/export/jszip.min.js") }}"></script>
    <script src="{{ asset("/admin/plugins/jquery-datatable/extensions/export/pdfmake.min.js") }}"></script>
    <script src="{{ asset("/admin/plugins/jquery-datatable/extensions/export/vfs_fonts.js") }}"></script>
    <script src="{{ asset("/admin/plugins/jquery-datatable/extensions/export/buttons.html5.min.js") }}"></script>
    <script src="{{ asset("/admin/plugins/jquery-datatable/extensions/export/buttons.print.min.js") }}"></script>

	<script src="{{ asset("/admin/plugins/ckeditor/ckeditor.js") }}"></script>
	<!-- Moment Plugin Js -->
    <script src="{{ asset("/admin/plugins/momentjs/moment.js") }}"></script>
	<!-- Bootstrap Material Datetime Picker Css -->

    <link href="{{ asset("/admin/plugins/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css") }}" rel="stylesheet" />
	<script src="{{ asset("/admin/plugins/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js") }}"></script>

	<!-- Bootstrap Select Css -->
    <link href="{{ asset("/admin/plugins/bootstrap-select/css/bootstrap-select.css") }}" rel="stylesheet" />
	<!-- Select Plugin Js -->
    <script src="{{ asset("/admin/plugins/bootstrap-select/js/bootstrap-select.js") }}"></script>

	<!-- Multi Select Css -->
    <link href="{{ asset("/admin/plugins/multi-select/css/multi-select.css") }}" rel="stylesheet">
	<script src="{{ asset("/admin/plugins/multi-select/js/jquery.multi-select.js") }}"></script>

	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css" />
	<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
	<!-- Custom Js -->
    <script src="{{ asset("/admin/js/admin.js") }}" ></script>
    <script src="{{ asset("/admin/js/pages/index.js") }}" ></script>

    <!-- Demo Js -->
    <script src="{{asset('/admin/js/demo.js')}}" ></script>
	<script type="text/javascript">
	$('.multiSelect_input').multiSelect();
	$('.js-exportable').DataTable({
		@if(isset($show_export) && $show_export)

		dom: 'Bfrtip',
		responsive: false,
		buttons: [
			'copy', 'excel', 'print'
		]
		@endif
	});
	$(function () {
	    //CKEditor
	    CKEDITOR.replace('ckeditor');
	    CKEDITOR.config.height = 300;
	});
    //Datetimepicker plugin
    $('.datetimepicker').bootstrapMaterialDatePicker({
        format: 'dddd DD MMMM YYYY - HH:mm',
        clearButton: true,
        weekStart: 1
    });

    $('.datepicker').bootstrapMaterialDatePicker({
        format: 'dddd DD MMMM YYYY',
        clearButton: true,
        weekStart: 1,
        time: false
    });

    $('.timepicker').bootstrapMaterialDatePicker({
        format: 'HH:mm',
        clearButton: true,
        date: false
    });
	$.material.init();
	//Multi-select
	</script>
	<script type="text/javascript">
		$('.delete_button').click(function(event){
			event.preventDefault();
			var delete_url = $(this).attr('href');
			swal({
				  title: "Do you really want to delete ?",
				  type: "warning",
				  showCancelButton: true,
				  confirmButtonColor: "red",
				  confirmButtonText: "yes",
				  cancelButtonText: "cancel",
				  closeOnConfirm: false,
				  closeOnCancel: true
				},
				function(isConfirm){
				  if (isConfirm) {
						window.location.href = delete_url;
				  }
				}
			);
		});

        // delete player
  $('.player_delete_button').click(function(event){
			event.preventDefault();
			var delete_url = $(this).attr('href');
			swal({
				  title: "Do you really want to delete ?",
          //text: "It will delete all players",
				  type: "warning",
				  showCancelButton: true,
				  confirmButtonColor: "red",
				  confirmButtonText: "yes",
				  cancelButtonText: "cancel",
				  closeOnConfirm: false,
				  closeOnCancel: true
				},
				function(isConfirm){
				  if (isConfirm) {
						window.location.href = delete_url;
				  }
				}
			);
		});
        //end

        // delete sub agent
  $('.sub_agent_delete_button').click(function(event){
			event.preventDefault();
			var delete_url = $(this).attr('href');
			swal({
				  title: "Do you really want to delete ?",
          text: "It will delete all related players",
				  type: "warning",
				  showCancelButton: true,
				  confirmButtonColor: "red",
				  confirmButtonText: "yes",
				  cancelButtonText: "cancel",
				  closeOnConfirm: false,
				  closeOnCancel: true
				},
				function(isConfirm){
				  if (isConfirm) {
						window.location.href = delete_url;
				  }
				}
			);
		});
        //end


	</script>

    <!-- Script for change allocate to in support ticket listing page - start -->
<script>
    $(document.body).on('change',".allocate_to",function(event)
    {
    	swal({
    	  title: "Are you sure?",
    	  type: "warning",
    	  showCancelButton: true,
    	  confirmButtonColor: "green",
    	  confirmButtonText: "Yes, Change it!",
    	  closeOnConfirm: true
    	},
    	function(){
    		var id =$(event.target).attr('data-value');
    		var allocate_to = $(event.target).val();
    		if(allocate_to.length > 0){
    			$.ajax({
                 type: "POST",
                 url: "{{route('admin-support-ticket-management-change-allocate-to')}}",
                 headers: {
                   'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                 },
                  data: {'id' : id, 'allocate_to' : allocate_to},
                 success: function(data)
                 {
                    if(data == 1)
                    {
                        $('#msgDiv').show();
                        setTimeout(function(){
                            $('#msgDiv').fadeOut('slow');
                        },1000);
                    }
                 }
        });
    		}
    	});
    });
</script>
<!-- Script for change allocate to in support ticket listing page - end -->

<!-- Script for change status in support ticket listing page - start -->
<script>
    $(document.body).on('change',".change_status",function(event)
    {
    	swal({
    	  title: "Are you sure?",
    	  type: "warning",
    	  showCancelButton: true,
    	  confirmButtonColor: "green",
    	  confirmButtonText: "Yes, Change it!",
    	  closeOnConfirm: true
    	},
    	function(){
    		var id =$(event.target).attr('data-value');
    		var status_id = $(event.target).val();
    		if(status_id.length > 0){
    			$.ajax({
                 type: "POST",
                 url: "{{route('admin-support-ticket-management-change-status')}}",
                 headers: {
                   'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                 },
                  data: {'id' : id, 'status_id' : status_id},
                 success: function(data)
                 {
                    if(data == 1)
                    {
                        $('#msgDiv').show();
                        setTimeout(function(){
                            $('#msgDiv').fadeOut('slow');
                        },1000);
                    }
                 }
        });
    		}
    	});
    });
</script>
<!-- Script for change status in support ticket listing page - end -->

<!-- Script for show single bet report details - start -->
<script type="text/javascript">
$(document.body).on('click',".view_details",function(){
  var id = $(this).attr('data-id');
  $('#viewBetModal-'+id).modal({
      show: 'true'
  });
});
</script>
<!-- Script for show single bet report details - end -->

<!-- Script for show ticket details in support ticket listing page - start -->
<script type="text/javascript">
	$(document.body).on('click',".view_messeges",function(){
		var id = $(this).attr('data-id');
        $('#viewStModal').modal({
            show: 'true'
        });
		$('#show_support_message').html("<i class='fa fa-spinner fa-spin' style='font-size:36px;'></i> <B style='font-size:24px;'>Loading....</B>");
        $(this).children('.badge').remove();
        $.ajax({
                 type: "POST",
                 url: "{{route('admin-support-ticket-management-show-message')}}",
                 headers: {
                   'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                 },
                  data: {'id' : id},
                 success: function(result)
                 {
                    if(result)
                    {
                        $('#show_support_message').html(result);
                    }
                 }
        });

	});
</script>
<!-- Script for show ticket details in support ticket listing page - end -->

<!-- Script for change language - start -->
<script>
    $('.change_language').click(function()
    {
	   var lang = $(this).attr('data-lang');
	   $.ajax({
   		headers: {
   		        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
   		},
   		type : 'POST',
   		data : {'lang':lang},
      		url  : "{{ route('change-language') }}",
      		success: function(data){
				        window.location.reload();
      		}
      	});
    });
</script>
<!-- Script for change language - end -->

<!--####    Support Ticket unread check start    ####-->
<script type="text/javascript">
    //$(document).ready(function(){
        $.ajax({
            url: "{{route('admin-unread-tickets')}}",
            type:"GET",
            success(result){
                if (result > 0)
                {
                    $('#unread_ticket_header').html(result);
                    var msg = result+' unread ticket message';
                    $('#unread_ticket_message').html(msg);
                }
                else
                {
                    $('#unread_ticket_header').html('');
                    var msg = 'No unread ticket messages';
                    $('#unread_ticket_message').html(msg);
                }
            }
        });

    //});
</script>
<!--####    Support Ticket unread check end    ####-->

<script>
// var table = $('#leagueTable').DataTable( {
//       "paging":   false,
//       "ordering": true,
//       "info":     true
//   } );

</script>

<script>
$(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip();
});
</script>


<script>
//script for load league data into league listing page using ajax
$('#leagueTable').DataTable({
       lengthMenu: [[10, 25, 50], [10, 25, 50]],
       processing: true,
       serverSide: true,
       "order": [],
       ajax: '{{route('admin-sports-book-management-league-settings-get-all-leagues')}}',
       columns: [
                { data: 'sport_id', name: 'sport.name' },
                { data: 'country_id', name: 'country.name' },
                { data: 'name', name: 'leagues.name' },
                { data: 'priority', name: 'leagues.priority' },
                { data: 'is_top', name: 'leagues.is_top' },
                { data: 'status', name: 'leagues.status' },
                { data: 'action', name: 'action' },
             ]
    });
</script>

<script>
//script for load market data into market listing page using ajax
$('#marketTable').DataTable({
       lengthMenu: [[10, 25, 50], [10, 25, 50]],
       processing: true,
       serverSide: true,
       "order": [],
       ajax: '{{route('admin-sports-book-management-market-get-all-markets')}}',
       columns: [
                { data: 'sport_id', name: 'sport.name' },
                { data: 'name', name: 'markets.name' },
                { data: 'market_group', name: 'markets.market_group' },
                { data: 'status', name: 'markets.status' },
                { data: 'action', name: 'action' },
             ]
    });
</script>

</body>

</html>
