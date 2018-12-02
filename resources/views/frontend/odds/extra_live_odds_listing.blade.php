<div class="ods_block" v-for="each_odd in ods.odds" id="extra-odd-block">
   <div :data-market-show="each_odd.name" class="ods_block_heading box_show_hide_odds hide-market">
      <i class="icon_class arrow_up"></i>
      <a href="javascript:void(0);">@{{each_odd.name}}
      </a>
   </div>
   <div class="show_hide_div_odds">
      <div :data-market-show="each_odd.name" class="ods_list many_odds hide-market">
         <div class="claub_time" v-for="odds_dtls in each_odd.participants">
            <a href="javascript:void(0);" v-bind:id="'each_odd_' + odds_dtls.id" class="each_odd" v-bind:data-id="odds_dtls.id" 
               v-bind:data-type="'LM'"
               :data-country="value.country"
               :data-league="valueKey"
               :data-match-id="ods.id"
               :data-bet-for="odds_dtls.short_name"
               :data-market-id="each_odd.id">
               <div class="mobile home_away">
                  @{{odds_dtls.short_name}}
               </div>
               <label class="each_market_name">
                     @{{odds_dtls.short_name}}
                     <i v-if="odds_dtls.handicap !== ''"> ( @{{ odds_dtls.handicap }} ) </i>
               </label>
               <span class="pull-right">@{{app.getOddsValue(odds_dtls.value_eu)}}</span>
            </a>
         </div>         
      </div>
   </div>
</div>