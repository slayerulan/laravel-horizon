@extends('frontend.layout.layout')
@section('main_body')
<?php $class_arr = array('pending'=>'fa-asterisk', 'win'=>'fa-check-circle-o', 'lost'=>'fa-times-circle-o', 'half_lost'=>'fa-times-circle-o half_loss', 'half_win'=>'fa-check-circle-o half_win', 'return_stake'=>'fa-undo', 'cancel'=>'fa-ban');?>
<div class="col-md-9">

    <div class="col-md-12" data-original-title="" title="">
       @if(count($betslips))
      <input type="text" id="search" placeholder="Search for Ref No. or Sport or Stake">
				<div class="history_body" data-original-title="" title="">

					 <div class="history_listing_heading" data-original-title="" title="">
					 	<ul>
              <li class="winn">&nbsp;</li>
					 		<li>{{__('label.Bet Place Time')}}</li>
					 		<li>{{__('label.Match Between')}}</li>
					 		<li>{{__('label.Market Name')}}</li>
					 		<li>{{__('label.Bet For')}}</li>
					 		<li>{{__('label.Stake')}}</li>
					 		<li>{{__('label.Prize')}}</li>
					 	</ul>
					 </div>
           @else
          <p>No data Found!.</p>
           @endif
					 <div class="history_listing_body" data-original-title="" title="">
				      	<div class="accordion main_div" data-original-title="" title="">
                <div class="loder_cover" style="display: none;" data-original-title="" title="">
                   <img src="http://apexsports.asia/frontend/images/loader.gif" >
               </div>
                @foreach($betslips as $key=>$val)

                  <div class="accordion-group history_accordion main_data" data-original-title="" title="">
          <div class="accordion-heading history_listing_body" data-original-title="" title="">
            <a class="accordion-toggle" data-toggle="collapse" href="{{'#collapse'.$val['bet_number']}}" id="{{$val['bet_number']}}" data-original-title="">
            <div class="history_item" data-original-title="" title="">
            <ul>
				<?php foreach($class_arr as $key => $class) {
					if($key == $val['status']) {
						$add_class = $class;
					}
				} ?>
                <li class="winn">
                  <span data-toggle="tooltip" title="{{str_replace('_',' ',$val['status'])}}" data-placement="top">
					           <i class="fa {{$add_class}}"></i>
                  </span>
				        </li>
                <li>{{$val['betPlaceTime']}}</li>
                <li><div data-toggle="tooltip" title="{{$val['homeTeam']}} vs {{$val['ayawTeam']}}" data-placement="top"><span>{{$val['homeTeam']}}</span> | <span>{{$val['ayawTeam']}}</span></div></li>
                <li><span class="history_bet_for" data-toggle="tooltip" title="{{$val['market_name']}}" data-placement="top">{{$val['market_name']}}</span></li>
                <li><b class="history_bet_for">
                  {{ (($val['bet_for'] === '1') ? 'Home' : (($val['bet_for'] === '2') ? 'Away' : $val['bet_for'])) }}
                  @if($val['extra'] != "" )
                    {{' | '.$val['extra']}}
                  @endif


                  </b></li>
                <li>{{$val['stake_amount']}}</li>
                <li>{{round($val['prize_amount'], 2)}}</li>
            </ul>
            <div class="sportsType">
                <span title="" data-toggle="tooltip" data-placement="top" class="{{'sports_'.$val['sportName']}}" data-original-title="{{$val['sportName']}}"></span>
            </div>

          <!--div class="{{'history_'.$val['status']}}" data-original-title="" title="">
            <span class="history_count" data-original-title="" title="">
                <i class="fa fa-check" aria-hidden="true" data-original-title="Win" title=""></i>
                <i class="fa fa-times" aria-hidden="true" data-original-title="Loss" title=""></i>
                <i class="fa fa-check half-win" aria-hidden="true" data-original-title="Half Win" title=""></i>
                <i class="fa fa-times half-loss" aria-hidden="true" data-original-title="half Loss" title=""></i>
                <i class="fa fa-asterisk" aria-hidden="true" data-original-title="Not Calculate" title=""></i>
                <i class="fa fa-ban" aria-hidden="true" data-original-title="Match Cancel" title=""></i>
                <i class="fa fa-reply" aria-hidden="true" data-original-title="Return" title=""></i>
           </span>
        </div-->

         </div>
             <div class="clearfix" data-original-title="" title=""></div>
          </a>

          </div>

          <div id="{{'collapse'.$val['bet_number']}}" class="accordion-body collapse animated zoomIn " data-original-title="" title="">
            <div class="panel-body " data-original-title="" title="">
              <div class="history_details" data-original-title="" title="">
                    <!-- loader -->
                    <div class="loder_cover" style="display: none;" data-original-title="" title="">
                       <img src="http://apexsports.asia/frontend/images/loader.gif" >
                   </div>
                 <!-- Mobile view -->
                    <div class="Mobile_view" style="display: none;" data-original-title="" title="">
                        <p><i class="fa fa-angle-right" aria-hidden="true" data-original-title="" title=""></i>
                          <b>{{__('label.Date and time of placing bet')}} : </b> <span data-original-title="" title="">{{$val['betPlaceTime']}}</span></p>
                        <p><i class="fa fa-angle-right" aria-hidden="true" data-original-title="" title=""></i>
                          <b>{{__('label.Bet for')}} : </b> <span data-original-title="" title="">
                          {{ (($val['bet_for'] === '1') ? 'Home' : (($val['bet_for'] === '2') ? 'Away' : $val['bet_for'])) }}
                          @if($val['extra'] != "" )
                            {{' | '.$val['extra']}}
                          @endif
                        </span></p>
                        <p><i class="fa fa-angle-right" aria-hidden="true" data-original-title="" title=""></i>
                          <b>{{__('label.Stake')}} : </b> <span data-original-title="" title="">
                            <span data-original-title="" title="">{{$val['stake_amount']}}</span>
                          </span></p>
                        <p><i class="fa fa-angle-right" aria-hidden="true" data-original-title="" title=""></i>
                          <b>{{__('label.Prize')}} : </b> <span data-original-title="" title="">{{$val['prize_amount']}}</span></p>
                    </div>

              </div>
           </div>
          </div>
       </div>
       @endforeach


    <div class="history_pagination_wrapper main_div_pagination" data-original-title="" title="">
        {{$paging->links()}}
    </div>

<script>

var bet_number_arr = [];
var ajax_check = true;



$("#search").keyup(function(){

  var val = $.trim($(this).val()).replace(/ +/g, ' ').toLowerCase();

  if(val != ""){
    $(".searchDivsForSingle").remove();
    $(".main_data").hide();
    $(".main_div_pagination").hide();
    $(".main_div .loder_cover").show();

    $.ajax({
       type:'POST',
       url:'searchBetsSingle',
       data:{'_token': $('meta[name="csrf-token"]').attr('content'),'data':val},
       success:function(data){

        $(".searchDivsForSingle").remove();
        $(".search_script").remove();
        $(".main_div .loder_cover").hide();
        if (!$(".main_data").is(":visible")){
          $(".main_div").append(data);
        }
		$(document.body).find('[data-toggle="tooltip"]').tooltip();
       }
    });
  }else{
    $(".searchDivsForSingle").remove();
    $(".search_script").remove();
    $(".main_data").show();
    $(".main_div_pagination").show();
    $(".main_div .loder_cover").hide();

  }

});

$(".accordion-toggle").click(function(){
  var bet_number = this.id;
  var found = jQuery.inArray(bet_number, bet_number_arr);
  if (found >= 0) {
    bet_number_arr.splice(found, 1);
  }else{
    if(!$("#collapse"+bet_number+" .panel-body .history_details .perbet").length){
      if(ajax_check == false) return;

      ajax_check = false;

      $("#collapse"+bet_number+" .panel-body .history_details .loder_cover").show();
      $.ajax({
             type:'POST',
             url:'getBetSlipDetails',
             data:{'_token': $('meta[name="csrf-token"]').attr('content'),'bet_number':bet_number},
             success:function(data){
                ajax_check = true;
                $("#collapse"+bet_number+" .panel-body .history_details").append(data);
                $("#collapse"+bet_number+" .panel-body .history_details .loder_cover").hide();
                bet_number_arr.push(bet_number);
             }
          });
    }else{
      bet_number_arr.push(bet_number);
    }
  }
});


</script>
</div>
                          <!-- history collapse end -->
                    <div class="clearfix" data-original-title="" title=""></div>
					 </div>
				</div>

		 </div>

 </div>
@endsection
