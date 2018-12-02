@extends('frontend.layout.layout')
@section('main_body')
<?php $class_arr = array('pending'=>'fa-asterisk', 'win'=>'fa-check-circle-o', 'lost'=>'fa-times-circle-o', 'half_lost'=>'fa-times-circle-o half_loss', 'half_win'=>'fa-check-circle-o half_win', 'return_stake'=>'fa-undo', 'cancel'=>'fa-ban');?>
<div class="col-md-9">
         <div class="col-md-12" data-original-title="" title="">
          @if(count($betComboSlips))
          <input type="text" id="search" placeholder="Search for Ref No. or Sport or Stake">
          @else
          <p>No data Found!.</p>
          @endif
                     <div class="history_body combo_history_body" data-original-title="" title="">
                        @if(count($betComboSlips))
                        <div class="history_listing_heading" data-original-title="" title="">
                           <ul>
                              <li class="winn">&nbsp;</li>
                              <li>{{__('label.Ref. No.')}}</li>
                              <li>{{__('label.Bet Place Time')}}</li>
                              <li>{{__('label.Stake')}}</li>
                              <li>{{__('label.Total odd')}}</li>
                              <li>{{__('label.Total multiple odd')}}</li>
                              <li>{{__('label.Prize')}}</li>
                           </ul>
                        </div>
                        @endif
                        <div class="history_listing_body" data-original-title="" title="">
                           <div class="accordion main_div" data-original-title="" title="">
                            <div class="accordion" data-original-title="" title="">
                  <div class="loder_cover" style="display: none;" data-original-title="" title="">
                   <img src="http://apexsports.asia/frontend/images/loader.gif" >
               </div>
                @foreach($betComboSlips as $key=>$val)

           <div class="accordion-group history_accordion main_data">
              <div class="accordion-heading history_listing_body">
                 <a class="accordion-toggle each_combo_bet" data-id="{{$val['bet_number']}}" data-toggle="collapse" href="{{'#collapse'.$val['bet_number']}}"> </a>
                 <div class="history_item"><a class="accordion-toggle each_combo_bet active" data-id="{{$val['bet_number']}}" data-toggle="collapse" href="{{'#collapse'.$val['bet_number']}}">
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
							<li><span data-original-title="" title="">{{$val['bet_number']}}</span></li>
							<li>{{$val['betPlaceTime']}}</li>
							<li>{{$val['stake_amount']}}</li>
							<li>{{$val['total_odds']}}</li>
							<li>{{oddsValue(round($val['multiple_odds'], 2))}}</li>
							<li>{{$val['prize_amount']}}</li>
                       </ul>

                     <div class="sportsType" data-original-title="" title="">
                         <span title="" class="{{'sports_'.$val['sportName']}}" data-original-title="{{$val['sportName']}}"></span>
                     </div>
                    <div class="clearfix"></div>
                 </a>
              </div>
              <div id="{{'collapse'.$val['bet_number']}}" class="accordion-body animated zoomIn collapse" data-original-title="" title="" >
                 <div class="panel-body " data-original-title="" title="">

                    <div class="combo_history_details" data-original-title="" title="">
                        <!-- Mobile view -->
                        <div class="Mobile_view" style="display: none;" data-original-title="" title="">
                           <p><i class="fa fa-angle-right" aria-hidden="true" data-original-title="" title=""></i> <b>{{__('label.Bet Place Time')}} : </b> <span data-original-title="" title="">{{$val['betPlaceTime']}}</span></p>
                           <p><i class="fa fa-angle-right" aria-hidden="true" data-original-title="" title=""></i> <b>{{__('label.Total Odd')}} : </b> <span data-original-title="" title="">{{$val['total_odds']}}</span></p>
                           <p><i class="fa fa-angle-right" aria-hidden="true" data-original-title="" title=""></i> <b>{{__('label.Total Multiple Odds')}} : </b> <span data-original-title="" title="">{{oddsValue($val['multiple_odds'])}}</span></p>
                           <p><i class="fa fa-angle-right" aria-hidden="true" data-original-title="" title=""></i> <b>{{__('label.Stake')}} : </b> <span data-original-title="" title="">{{$val['stake_amount']}}</span></p>
                           <p><i class="fa fa-angle-right" aria-hidden="true" data-original-title="" title=""></i> <b>{{__('label.Prize')}} : </b> <span data-original-title="" title="">{{$val['prize_amount']}}</span></p>
                        </div>

                       <p><i class="fa fa-angle-right" aria-hidden="true" data-original-title="" title=""></i> <b>{{__('label.RefNO')}} : </b> <span data-original-title="" title="">{{$val['bet_number']}}</span></p>
                       <div class="clearfix" data-original-title="" title=""></div>
                    </div>
                  <!-- combo_history_child end -->
                  <div class="combo_history_child" id="{{'combo_child_'.$val['bet_number']}}" data-original-title="" title="">
                  <div class="loder_cover" style="display: none;" data-original-title="" title="">
                       <img src="http://apexsports.asia/frontend/images/loader.gif" >
                   </div>
                  </div>
                    <!-- combo_history_child end -->
                 </div>
              </div>
           </div>
         </div>
         @endforeach
         <div class="accordion" data-original-title="" title="">

           <div class="history_pagination_wrapper main_div_pagination" data-original-title="" title="">
               {{$paging->links()}}
           </div>



 <script>
    //var $rows = $(".myUL");
    var ajax_check = true;
    var bet_number_arr =[];

  $("#search").keyup(function(){
    var val = $.trim($(this).val()).replace(/ +/g, ' ').toLowerCase();

    if(val != ""){
      $(".searchDivsForSingle").remove();
      $(".main_data").hide();
      $(".main_div_pagination").hide();
      $(".main_div .loder_cover").show();

    $.ajax({
       type:'POST',
       url:'searchBetsCombo',
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

  $(".each_combo_bet").click(function(){
    var bet_number = $(this).attr("data-id");
    var found = jQuery.inArray(bet_number, bet_number_arr);
  if (found >= 0) {
    bet_number_arr.splice(found, 1);
  }else{
    if(!$("#combo_child_"+bet_number+" .history_listing_heading").length){
      if(ajax_check == false) return;

        ajax_check = false;
        $("#combo_child_"+bet_number+" .loder_cover").show();
        $.ajax({
             type:'POST',
             url:'getComboBetSlips',
             data:{'_token': $('meta[name="csrf-token"]').attr('content'),'bet_number':bet_number},
             success:function(data){
                ajax_check = true;
                $("#combo_child_"+bet_number).append(data);
                $("#combo_child_"+bet_number+" .loder_cover").hide();
                bet_number_arr.push(bet_number);
				        $(document.body).find('[data-toggle="tooltip"]').tooltip();
             }
          });
    }else{
      bet_number_arr.push(bet_number);
    }
  }
  });





 </script>
 </div></div></div></div></div></div></div>
     <!-- history collapse end -->
     <div class="clearfix" data-original-title="" title=""></div>

<div class="combo_info col-md-12" data-original-title="" title="">
    <p>Please note with one bet lost, remaining bets will not be calculated and combo will be declared lost.</p>
</div>

@endsection
