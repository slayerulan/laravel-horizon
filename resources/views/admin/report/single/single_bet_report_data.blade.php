

<?php $class_arr = array('pending'=>'fa-asterisk', 'win'=>'fa-check-circle-o', 'lost'=>'fa-times-circle-o', 'half_lost'=>'fa-times-circle-o half_loss', 'half_win'=>'fa-check-circle-o half_win', 'return_stake'=>'fa-undo', 'cancel'=>'fa-ban'); ?>

@if(count($betslips))
<div class="table-responsive">
			<table class=" crud-table table table-bordered table-striped table-hover dataTable js-exportable">
				<thead>
					<tr>
            <th>&nbsp;</th>
						<th>Agent</th>
            <th>Player</th>
            <th>Match Between</th>
            <th>Stake Amount</th>
            <th>Market Name</th>
            <th>Result</th>
            <th>Action</th>
					</tr>
				</thead>
				<tbody>
          @foreach ($betslips as $data)
          <tr>
              <?php
	              foreach($class_arr as $key => $class)
	              {
	                if($key == $data['status']){ $add_class = $class; }
	        			}
			  ?>

		    <td class="winn">
              <span data-toggle="tooltip" title="{{str_replace('_',' ',$data['status'])}}" data-placement="top">
					           <i class="fa {{$add_class}}"></i>
              </span>
            </td>
            <td>{{ $data['agent'] }}</td>
            <td>{{ $data['player'] }}</td>
            <td>{{ $data['homeTeam'] }} | {{ $data['awayTeam'] }}</td>
            <td>{{ $data['stakeAmount'] }}</td>
            <td>{{ $data['marketName'] }}</td>
            <td>
            @foreach(SCOREPARSER($data['score'],$data['sport']) as $val)
                {{$val}}
            @endforeach
            </td>
            <td>
							<a href="javascript:void(0)" class="waves-effect btn btn-info view_details" data-id="{{ $data['id'] }}"><i class="material-icons">info</i>Details</a>
							@if($data['status'] == 'pending')
								<a href="javascript:void(0)" class="waves-effect single-bet-report-delete-button btn btn-danger" data-id="{{ $data['id'] }}"><i class="material-icons">delete_sweep</i>Delete</a>
							@endif
						</td>
          </tr>

          @endforeach
				</tbody>
			</table>

      @foreach ($betslips as $data)
      <!--####	 Modal start		####-->
        <div class="modal fade" id="viewBetModal-{{ $data['id'] }}" role="dialog">
          <div class="modal-dialog">
            <!-- Modal content start -->
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Single Bet Report Details</h4>
              </div>
              <div class="modal-body">

                <div>
                  <table class="table table-striped newTickets single_bet_slip">
                  		<tbody>
                        <tr>
                           <td><b>Sport</b></td>
                           <td>{{ $data['sport'] }}</td>
                        </tr>
                        <tr>
                           <td><b>Ref No.</b></td>
                           <td>{{ $data['refNo'] }}</td>
                        </tr>
                  			<tr>
                  			   <td><b>Bet Place Time</b></td>
                           <td>{{ $data['betPlaceTime'] }}</td>
                  			</tr>
                  			<tr>
                  			   <td><b>Agent</b></td>
                            <td>{{ $data['agent'] }}</td>
                  			</tr>
                  			<tr>
                  			   <td><b>Player</b></td>
                  				 <td>{{ $data['player'] }}</td>
                  			</tr>
                  			<tr>
                  			   <td><b>Match Between</b></td>
                  				 <td>{{ $data['homeTeam'] }} | {{ $data['awayTeam'] }}</td>
                  			</tr>
                  			<tr>
                  			   <td><b>Market Name</b></td>
                  				 <td>{{ $data['marketName'] }}</td>
                  			</tr>
                        <tr>
                  			   <td><b>Stake Amount</b></td>
                  				 <td>{{ $data['stakeAmount'] }}</td>
                  			</tr>
                        <tr>
                  			   <td><b>Bet For</b></td>
                  				 <td>{{ $data['betFor'] }}</td>
                  			</tr>
                        <tr>
                  			   <td><b>Extra Value</b></td>
                  				 <td>{{ $data['extraValue'] }}</td>
                  			</tr>
                        <tr>
                  			   <td><b>Odds Value</b></td>
                  				 <td>{{ $data['oddsValue'] }}</td>
                  			</tr>
                        <tr>
                  			   <td><b>Prize Amount</b></td>
                  				 <td>{{ $data['prizeAmount'] }}</td>
               			</tr>
                        <tr>
                  			   <td><b>Result of Match</b></td>
                  				 <td>
                                     @foreach(SCOREPARSER($data['score'],$data['sport']) as $val)
                                        <i data-original-title="" title="">{{$val}}</i>
                                     @endforeach
                                 </td>
               			</tr>
                        <tr>
                  			   <td><b>Status</b></td>
                  				 <td>{{ str_replace('_',' ',$data['status']) }}</td>
                  			</tr>

                      </tbody>
	                  </table>
                </div>
              </div>
            </div>
            <!-- Modal content end -->
          </div>
        </div>
        <!--#### Modal end		####-->
      @endforeach


</div>

<div class="history_pagination_wrapper search_div_pagination" data-original-title="" title="">
        @if($total_page != 1)
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

@else
  <p>No data found!</p>
@endif

<script>
// delete sigle bet report
	$('.single-bet-report-delete-button').click(function(event){
			event.preventDefault();
			var delete_url = $(this).attr('href');
			var id = $(this).attr('data-id');
			var page = $('.blade-pagination').attr('data-current');

			swal({
					title: "Do you really want to delete ?",
					text: "It will refund credits into player's wallet",
					type: "warning",
					showCancelButton: true,
					confirmButtonColor: "red",
					confirmButtonText: "yes",
					cancelButtonText: "cancel",
					closeOnConfirm: false,
					closeOnCancel: true
				},
				function(isConfirm){
					if (isConfirm)
					{
							$.ajax({
			            headers: {
			              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			            },
			            data: {'id' : id},
			            url: "{{route('admin-report-management-single-bet-report-delete')}}",
			            type:"POST",
			            success(result)
			            {
			                if (result)
			                {
													swal.close();
													$('#msgDiv').show();
			                    setTimeout(function(){
			                        $('#msgDiv').fadeOut('slow');
			                    },1000);
													$('.resultDiv').html('');
													$(".loder_cover").show();
											    setTimeout(function(){
											        $('.loder_cover').fadeOut('fast');
											    },500);
													//getData();
																var sport = $('#sport').val();
															    var startDate = $('.reportStartDate').val();
															    var endDate = $('.reportEndDate').val();
															    var status = $('#status').val();
																var agent = $('#agent').val();
																var player = $('#player').val();
                                                                var betTypeVal = $('#betType').is(':checked')?'yes':'no';
                                                                  if(betTypeVal=='yes')
                                                                  {
                                                                      betType = 'pre';
                                                                  }
                                                                  else
                                                                  {
                                                                      betType = 'live';
                                                                  }

															  if(sport !='' && startDate !='' && endDate !='')
															  {
															    $.ajax({
															            headers: {
															              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
															            },
															            data: {'sport' : sport, 'start_date' : startDate, 'end_date' : endDate, 'status' : status, 'agent' : agent, 'player' : player, 'page' : page, 'bet_type' : betType },
															            url: "{{route('admin-report-management-single-bet-report')}}",
															            type:"POST",
															            success(result)
															            {
															                if (result)
															                {
															                    $('.resultDiv').html(result);
															                }
															            }
															        });
															  }
			                }
			            }
	        		});
					}
				}
			);
		});
//end

// Code for pagination
$(".page").click(function()
{
			var sport = $('#sport').val();
			var startDate = $('.reportStartDate').val();
			var endDate = $('.reportEndDate').val();
			var status = $('#status').val();
			var agent = $('#agent').val();
			var player = $('#player').val();
            var page = $(this).attr("data-page");

            var betTypeVal = $('#betType').is(':checked')?'yes':'no';
              if(betTypeVal=='yes')
              {
                  betType = 'pre';
              }
              else
              {
                  betType = 'live';
              }

      $.ajax({
         type:'POST',
         url: "{{route('admin-report-management-single-bet-report')}}",
         data:{'_token': $('meta[name="csrf-token"]').attr('content'),'sport' : sport, 'start_date' : startDate, 'end_date' : endDate, 'status' : status, 'agent' : agent, 'player' : player, 'page' : page, 'bet_type' : betType},
         success:function(result)
				 {
          	$('.resultDiv').html(result);
            $(document.body).find('[data-toggle="tooltip"]').tooltip();
         }
      });
});
//end
</script>
