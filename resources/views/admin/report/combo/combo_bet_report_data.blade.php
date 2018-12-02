<?php $class_arr = array('pending'=>'fa-asterisk', 'win'=>'fa-check-circle-o', 'lost'=>'fa-times-circle-o', 'half_lost'=>'fa-times-circle-o half_loss', 'half_win'=>'fa-check-circle-o half_win', 'return_stake'=>'fa-undo', 'cancel'=>'fa-ban');?>

@if(count($betComboSlips))
<div class="table-responsive">
  <table class=" crud-table table table-bordered table-striped table-hover dataTable js-exportable">
    <thead>
  			<tr>
          <th>&nbsp;</th>
		  <th>Ref. No.</th>
          <th>Bet Place Time</th>
          <th>Stake Amount</th>
          <th>Total Odd</th>
          <th>Total Multiple Odd</th>
          <th>Prize</th>
          <th>Bet Type</th>
          <th>Action</th>
  			</tr>
		</thead>
    <tbody>
      @foreach ($betComboSlips as $data)
      <tr style="position:relative">
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
            <td>{{ $data['bet_number'] }}</td>
            <td>{{ $data['betPlaceTime'] }}</td>
            <td>{{ $data['stake_amount'] }}</td>
            <td id="each_combo_bet_{{ $data['bet_number'] }}" class="accordion-toggle collapsed each_combo_bet" onclick="showBetSlip('{{ $data['bet_number'] }}')" data-id="{{$data['bet_number']}}" data-toggle="collapse" data-parent="#OrderPackages" data-target=".each_combo_bet_details_{{ $data['bet_number'] }}">
            {{ $data['total_odds'] }}
            <span><i class="indicator glyphicon glyphicon-chevron-down pull-right"></i></span>
            </td>
            <td>{{ $data['multiple_odds'] }}</td>
            <td>
              {{ $data['prize_amount'] }}
            </td>
            <td >
              <select class="form-control show-tick" style="width:108px" id="bet_type_{{ $data['bet_number'] }}" onchange="showBetSlip('{{ $data['bet_number'] }}')">
                  <option value="pre" selected>Prematch</option>
                  <option value="live">Live</option>
              </select>
            </td>
            <td>
              @if($data['status'] == 'pending')
								<a href="javascript:void(0)" class="waves-effect combo-bet-report-delete-button btn btn-danger" data-id="{{ $data['id'] }}"><i class="material-icons">delete_sweep</i>Delete</a>
							@endif
            </td>
      </tr>

      <tr>
            <td colspan="9" class="hiddenRow drk-bg">
                <div class="accordion-body collapse each_combo_bet_details_{{ $data['bet_number'] }}" id="accordion1" style="height: auto;">
                    <p><i class="fa fa-angle-right" aria-hidden="true" data-original-title="" title=""></i> <b>Ref. No. : </b> <span data-original-title="" title="">{{$data['bet_number']}}</span></p>
                    <div class="{{'loder_cover_'.$data['bet_number']}} combo_bet_slip_loader" style="display: none;" data-original-title="" title="">
                        <img src="{{ url('') }}/admin/images/loader.gif" >
                    </div>
                    <div id="{{'combo_child_'.$data['bet_number']}}">

                    </div>
                </div>
            </td>
      </tr>

      @endforeach
    </tbody>
  </table>



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

      $.ajax({
         type:'POST',
         url: "{{route('admin-report-management-combo-bet-report')}}",
         data:{'_token': $('meta[name="csrf-token"]').attr('content'),'sport' : sport, 'start_date' : startDate, 'end_date' : endDate, 'status' : status, 'agent' : agent, 'player' : player, 'page' : page},
         success:function(result)
				 {
          	$('.resultDiv').html(result);
            $(document.body).find('[data-toggle="tooltip"]').tooltip();
         }
      });
});
//end

/*$(".each_combo_bet").click(function()
{
    var bet_number = $(this).attr("data-id");
    //var bet_type = $("#bet_type_"+bet_number).val();
    var child = $("#combo_child_"+bet_number).children('table').length;

    if( child == 0)
    {
      $(".loder_cover_"+bet_number).show();
      setTimeout(function(){
          $('.loder_cover_'+bet_number).fadeOut('fast');
      },500);
        $.ajax({
                headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {'bet_number' : bet_number },
                url: "{{route('admin-report-management-combo-bet-slip')}}",
                type:"POST",
                success(result)
                {
                    if (result)
                    {
                        $("#combo_child_"+bet_number).html(result);
                    }
                }
            });
      }

  });*/


  function showBetSlip(bet_number)
  {
    var bet_type = $("#bet_type_"+bet_number).val();
    var child = $("#combo_child_"+bet_number).children('table').length;
    //console.log(bet_number);
    //if( child == 0)
    //{
      $(".loder_cover_"+bet_number).show();
      setTimeout(function(){
          $('.loder_cover_'+bet_number).fadeOut('fast');
      },500);
        $.ajax({
                headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {'bet_number' : bet_number, 'bet_type' : bet_type },
                url: "{{route('admin-report-management-combo-bet-slip')}}",
                type:"POST",
                success(result)
                {
                    if (result)
                    {
                        $("#combo_child_"+bet_number).html(result);
                    }
                }
            });
      //}
  }
</script>

<script>
// delete sigle bet report
	$('.combo-bet-report-delete-button').click(function(event){
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
			            url: "{{route('admin-report-management-combo-bet-report-delete')}}",
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

															  if(sport !='' && startDate !='' && endDate !='')
															  {
															    $.ajax({
															            headers: {
															              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
															            },
															            data: {'sport' : sport, 'start_date' : startDate, 'end_date' : endDate, 'status' : status, 'agent' : agent, 'player' : player, 'page' : page },
															            url: "{{route('admin-report-management-combo-bet-report')}}",
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
</script>
