<div id="live_match_list" v-cloak>
   <div v-for ="value in objects">
      <div v-if="country==value.country || show_all" class="ods_block" v-for ="league, valueKey in value.league_data">
         <div class="ods_block_heading box_show_hide">
            <i class="icon_class arrow_up"></i>
            <a href="javascript:void(0);">@{{ value.country + ' : ' + valueKey }}
            </a>
         </div>
         <div class="show_hide_div" >
            <div v-for ="ods in league">
               <div class="ods_list ods_head">
                  <div  class="date_time">
                     <i class="football_sports_icon"></i>
                     <span>@{{ods.period}} <br>@{{ods.minute}} min</span>                     
                  </div>
                  <div class="mobile mob_title">
                     @{{ods.name}}
                  </div>
                  <div class="claub_time" v-if="ods.odds" v-for="firstOdd,firstOddKey,index in ods.odds[0].participants" >
                     <a href="javascript:void(0);" v-bind:id="'each_odd_' + firstOdd.id"  class="each_odd" 
                        :data-country="value.country"
                        :data-league="valueKey"
                        :data-match-id="ods.id"
                        :data-id="firstOdd.id"
                        :data-bet-for="firstOdd.short_name"
                        :data-type="'LM'"
                        :data-market-id="ods.odds[0].id">
                        <div class="mobile home_away">
                           @{{((index==0)?'Home': ((index==1) ? 'X': 'Away'))}}
                        </div>
                        <span class="ods_title">@{{firstOdd.name}}</span>
                        <span class="pull-right">
                           @{{app.getOddsValue(firstOdd.value_eu)}}
                        </span>
                     </a>
                  </div>
                  <div class="market">                  
                     <a href="javascript:void(0);" class="expand-odds" v-bind:data-id="ods.id">
                     <i :class="'fa fa-plus icon_class_'+ods.id"></i></a>
                  </div>
               </div>
               <div class="each_match_extra_odd"  v-bind:id="'each_odd_' + ods.id" style="display: none;">
                  @include('frontend.odds.extra_live_odds_listing')
               </div>
               <!-- odd list -->
            </div>
         </div>
      </div>
   </div>
   <div v-if="objects.length == 0" class="leagues_list_block">
      <div class="leagues">{{ __('user_label.No Match Found') }}</div>
   </div>
</div>