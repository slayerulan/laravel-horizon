<?php $class_arr = array('pending'=>'fa-asterisk', 'win'=>'fa-check-circle-o', 'lost'=>'fa-times-circle-o', 'half_lost'=>'fa-times-circle-o half_loss', 'half_win'=>'fa-check-circle-o half_win', 'return_stake'=>'fa-undo', 'cancel'=>'fa-ban');?>
<span class="searchDivsForSingle">

  @if(count($betslips))
@foreach($betslips as $key=>$val)                     
  <div class="accordion-group history_accordion" data-original-title="" title="">
      <div class="accordion-heading history_listing_body" data-original-title="" title="">
          <a class="accordion-toggle search_divs" data-toggle="collapse" href="{{'#collapse'.$val['bet_number'].'_search'}}" id="{{$val['bet_number']}}">
              <div class="history_item">
                  <ul>
              				<?php foreach($class_arr as $key => $class) {
              					if($key == $val['status']) {
              						$add_class = $class;
              					}
              				} ?>
                      <li class="winn">
                          <span data-toggle="tooltip" title="{{$val['status']}}" data-placement="top">
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
                      <li>{{$val['prize_amount']}}</li>
                  </ul>

                  <div class="sportsType" data-original-title="" title="">
                      <span title="" class="{{'sports_'.$val['sportName']}}" data-toggle="tooltip" data-placement="top" class="{{'sports_'.$val['sportName']}}" data-original-title="{{$val['sportName']}}"></span>
                  </div>
              </div>
              <div class="clearfix"></div>
          </a>

          </div>

          <div id="{{'collapse'.$val['bet_number'].'_search'}}" class="accordion-body collapse animated zoomIn " data-original-title="" title="">
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
       
       <div class="history_pagination_wrapper search_div_pagination" data-original-title="" title="">
        @if($total_page != 1)
        <ul id="history_pagination" class="blade-pagination" data-current="{{$active_page}}" data-total="{{$total_page}}">
          <li class="page first @if($active_page == 1) disabled @endif" data-page="1">First</li>
          <li class="page prev @if($active_page == 1) disabled @endif" data-page="{{(int)$active_page-1}}">Prev</li>
          @for($i=1;$i<=$total_page;$i++)
            <li class="page @if($i == $active_page) active disabled @endif" data-page="{{$i}}">{{$i}}</li>
          @endfor
          <li class="page next @if($active_page == $total_page) disabled @endif" data-page="{{(int)$active_page+1}}">Next</li>
          <li class="page last  @if($active_page == $total_page) disabled @endif" data-page="{{$total_page}}">Last</li>
        </ul>
        @endif
      </div>

       @else
       <pre><b>No Data Found.</b></pre>
       @endif

       
       

      <script>
        var ajax_check = true;
        var bet_number_arr_search = [];
         $(".search_divs").click(function(){
          var bet_number = this.id;
          var found = jQuery.inArray(bet_number, bet_number_arr_search);
          if (found >= 0) {
            bet_number_arr_search.splice(found, 1);
          }else{
            if(!$("#collapse"+bet_number+"_search .panel-body .history_details .perbet").length){
              if(ajax_check == false) return;
     
                ajax_check = false;
                
              $("#collapse"+bet_number+"_search .panel-body .history_details .loder_cover").show();
              $.ajax({
                     type:'POST',
                     url:'getBetSlipDetails',
                     data:{'_token': $('meta[name="csrf-token"]').attr('content'),'bet_number':bet_number},
                     success:function(data){
                        ajax_check = true;
                        $("#collapse"+bet_number+"_search .panel-body .history_details").append(data);
                        $("#collapse"+bet_number+"_search .panel-body .history_details .loder_cover").hide();
                        bet_number_arr_search.push(bet_number);
                     }
                  });
            }else{
              bet_number_arr_search.push(bet_number);
            }
          }
        });

         
       </script>

     </span>
     
<script class="search_script">
  $(".page").click(function(){
          var page = $(this).attr("data-page");
          var val = $("#search").val();
          $(".searchDivsForSingle").remove();
          $.ajax({
             type:'POST',
             url:'searchBetsSingle',
             data:{'_token': $('meta[name="csrf-token"]').attr('content'),'data':val,'page':page},
             success:function(data){
              $(".search_script").remove();
              $(".main_div").append(data);
             }
          });

         });
</script>