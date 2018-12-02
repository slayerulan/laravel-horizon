 
     <div class="history_listing_heading" data-original-title="" title="">
        <ul>
           <li>{{__('label.Row No.')}}</li>
           <li>{{__('label.Match Between')}}</li>
           <li>{{__('label.Market Name')}}</li>
           <li>{{__('label.Bet For')}}</li>
           <li>{{__('label.Bet Value')}}</li>
        </ul>
     </div>
     <div class="history_listing_body" data-original-title="" title="">
      @foreach($combination as $key1=>$val1)
               

        <div class="accordion" data-original-title="" title="">
               <div class="accordion-group history_accordion" data-original-title="" title="">
                  <div class="accordion-heading history_listing_body" data-original-title="" title="">
                     <a class="accordion-toggle combo_bet_detail bet_listin" data-toggle="collapse" id="{{'betDetails_'.$val1['id']}}" href="{{'#collapse_'.$bet_number.'_'.$key1}}">
                      <div class="history_item" data-original-title="" title="">
                         <ul>
                            <li>{{(int)$key1+(int)1}}</li>
                            <li><div data-toggle="tooltip" title="{{$val1['homeTeam']}} vs {{$val1['awayTeam']}}" data-placement="top">
                                <span>{{$val1['homeTeam']}}</span> | <span>{{$val1['awayTeam']}}</span>
                            </div></li>
                            <li><span class="history_bet_for" data-toggle="tooltip" title="{{$val1['market_name']}}" data-placement="top">{{$val1['market_name']}}</span></li>
                            <li><b class="history_bet_for">
                              {{ (($val1['bet_for'] === '1') ? 'Home' : (($val1['bet_for'] === '2') ? 'Away' : $val1['bet_for'])) }}
                              @if($val1['extra'] != "" )
                                {{' | '.$val1['extra']}}
                              @endif
                            </b></li>
                            <li>{{oddsValue(round($val1['odds_value'], 2))}}</li>
                         </ul>
           				  <div class="sportsType" data-original-title="" title="">
           					  <span title="" class="{{'sports_'.$val1['sportName']}}" data-original-title="{{$val1['sportName']}}"></span>
           				  </div>
                       <div class="{{'history_'.$val1['status']}}" data-original-title="" title="">
                           <span class="history_count" data-original-title="" title="">
                               <i class="fa fa-check" aria-hidden="true" data-original-title="Win" title=""></i>
                               <i class="fa fa-times" aria-hidden="true" data-original-title="Loss" title=""></i>
                               <i class="fa fa-check half-win" aria-hidden="true" data-original-title="Half Win" title=""></i>
                               <i class="fa fa-times half-loss" aria-hidden="true" data-original-title="half Loss" title=""></i>
                               <i class="fa fa-asterisk" aria-hidden="true" data-original-title="Not Calculate" title=""></i>
                               <i class="fa fa-ban" aria-hidden="true" data-original-title="Match Cancel" title=""></i>
                               <i class="fa fa-reply" aria-hidden="true" data-original-title="Return" title=""></i>
                          </span>
                         </div>

                        </div>
                        <div class="clearfix" data-original-title="" title=""></div>
                     </a>
                  </div>
                  <div id="{{'collapse_'.$bet_number.'_'.$key1}}" class="accordion-body bet_listin_in collapse animated zoomIn " data-original-title="" title="">
                     <div class="panel-body " data-original-title="" title="">
                        <div class="history_details" data-original-title="" title="">
                            
                            <!-- loader -->
                            <div class="loder_cover" style="display: none;" data-original-title="" title="">
                               <img src="http://apexsports.asia/frontend/images/loader.gif" >
                           </div>
                            <!-- Mobile view -->
                            <div class="Mobile_view" style="display: none;" data-original-title="" title="">
                                <p><i class="fa fa-angle-right" aria-hidden="true" data-original-title="" title=""></i> <b>{{__('label.Bet for')}} : </b> <span data-original-title="" title="">{{__('label.Home')}}</span></p>
                            </div>
                           <div class="clearfix" data-original-title="" title=""></div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>

            @endforeach

              </div>

<script >
  var combo_body_arr = [];
  var ajax_check = true;
  $(".combo_bet_detail").click(function(){
  var id = this.id.split("_");
  var combo_body = $(this).attr('href');
  var found = jQuery.inArray(combo_body, combo_body_arr);
  if (found >= 0) {
    combo_body_arr.splice(found, 1);
  }else{
    
    if(!$(combo_body+" .panel-body .history_details .perbet").length){
      if(ajax_check == false) return;
     
      ajax_check = false;

      $(combo_body+" .panel-body .history_details .loder_cover").show();
      $.ajax({
             type:'POST',
             url:'getComboBetSlipDetails',
             data:{'_token': $('meta[name="csrf-token"]').attr('content'),'id':id[1]},
             success:function(data){
                ajax_check = true;
                $(combo_body+" .panel-body .history_details").append(data);
                $(combo_body+" .panel-body .history_details .loder_cover").hide();
                combo_body_arr.push(combo_body);
             }
          });
    }else{
      combo_body_arr.push(combo_body);
    }
  }
});
</script>