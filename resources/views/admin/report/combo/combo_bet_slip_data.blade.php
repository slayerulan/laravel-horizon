@if($combination[0] !=NULL)
<table class=" crud-table table table-bordered table-striped table-hover dataTable js-exportable">
<thead>
  			<tr>
  				<th>Row No.</th>
          <th>Agent</th>
          <th>Player</th>
          <th>Match Between</th>
          <th>Market Name</th>
          <th>Bet For</th>
          <th>Bet Value</th>
          <th>Action</th>
  			</tr>
</thead>
<tbody>

@foreach ($combination as $key1=>$val1)
<tr>
<td>{{(int)$key1+(int)1}}</td>
<td>{{ $val1['agent'] }}</td>
<td>{{ $val1['player'] }}</td>
<td>{{$val1['homeTeam']}}</span> | <span>{{$val1['awayTeam']}}</td>
<td>{{$val1['market_name']}}</td>
<td>{{$val1['bet_for']}}
    @if($val1['extra'] != "" )
      {{' | '.$val1['extra']}}
    @endif
</td>
<td>{{$val1['odds_value']}}</td>
<td><a href="javascript:void(0)" class="waves-effect btn btn-info view_details" data-id="{{ $val1['id'] }}"><i class="material-icons">info</i>Details</a></td>
</tr>

@endforeach

</tbody>
</table>
<!--div id="combo_bet_slip_details_modal">

</div-->
@foreach ($combination as $key1=>$val1)
<!--####	 Modal start		####-->
<div class="modal fade" id="viewBetModal-{{ $val1['id'] }}" role="dialog">

</div>
<!--#### Modal end		####-->
@endforeach
@else
<h5>No data found!</h5>
@endif

<script>
$(".view_details").click(function()
{
    var comboBetslipId = $(this).attr("data-id");
    $.ajax({
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {'comboBetslipId' : comboBetslipId },
            url: "{{route('admin-report-management-combo-bet-slip-details')}}",
            type:"POST",
            success(result)
            {
                if (result)
                {
                    $("#viewBetModal-"+comboBetslipId).html(result);
                }
            }
        });
});

/*function showBetSlipDetails(comboBetslipId,bet_number)
{
    var bet_type = $("#bet_type_"+bet_number).val();
    $.ajax({
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {'comboBetslipId' : comboBetslipId, 'bet_type' : bet_type },
            url: "{{route('admin-report-management-combo-bet-slip-details')}}",
            type:"POST",
            success(result)
            {
                if (result)
                {
                    $("#viewBetModal-"+comboBetslipId).html(result);
                }
            }
        });
}*/
</script>
