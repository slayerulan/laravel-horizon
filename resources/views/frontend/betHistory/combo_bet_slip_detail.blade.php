<span class="perbet">
<p><i class="fa fa-angle-right" aria-hidden="true" data-original-title="" title=""></i>
<b>{{__('label.League')}} : </b> <span data-original-title="" title="">{{$data['country'].': '.$data['league']}}</span></p>
<p><i class="fa fa-angle-right" aria-hidden="true" data-original-title="" title=""></i>
<b>{{__('label.Bet Type')}} : </b> <span data-original-title="" title="">{{$data['bet_type']}}</span></p>
<p><i class="fa fa-angle-right" aria-hidden="true" data-original-title="" title=""></i> 
	<b>{{__('label.Match Between')}} : </b> <span data-original-title="" title="">
		<span data-original-title="" title="">{{$data['homeTeam']}}</span> | <span data-original-title="" title="">{{$data['awayTeam']}}</span></span></p>
<p><i class="fa fa-angle-right" aria-hidden="true" data-original-title="" title=""></i> 
	<b>{{__('label.Match Date Time')}} : </b> <span data-original-title="" title="">{{$data['match_date_time']}}</span></p>
<p><i class="fa fa-angle-right" aria-hidden="true" data-original-title="" title=""></i> 
	<b>{{__('label.Market Name')}} : </b> <span data-original-title="" title="">{{$data['market_name']}}</span></p>
<p><i class="fa fa-angle-right" aria-hidden="true" data-original-title="" title=""></i> 
	<b>{{__('label.Bet for')}} : </b> <span data-original-title="" title="">{{ (($data['bet_for'] === '1') ? 'Home' : (($data['bet_for'] === '2') ? 'Away' : $data['bet_for'])) }}</span></p>
<p><i class="fa fa-angle-right" aria-hidden="true" data-original-title="" title=""></i> 
	<b>{{__('label.Bet value')}} : </b> <span data-original-title="" title="">{{oddsValue(round($data['bet_value'], 2))}}</span></p>
<p><i class="fa fa-angle-right" aria-hidden="true" data-original-title="" title=""></i> 
	<b>{{__('label.New Bet value')}} : </b> <span data-original-title="" title="">{{$data['calculated_odds']}}</span></p>
<p><i class="fa fa-angle-right" aria-hidden="true" data-original-title="" title=""></i> 
	<b>{{__('label.Bet result')}} : </b> <span data-original-title="" title="">{{str_replace('_',' ',$data['result'])}}</span></p>
<p><i class="fa fa-angle-right" aria-hidden="true" data-original-title="" title=""></i> 
	<b class="result_of_match_head">{{__('label.Result of match')}} : </b><span data-original-title="" title="" class="result_of_match">
		@foreach(SCOREPARSER($data['score'],$data['sportName']) as $val)
	    	<i data-original-title="" title="">{{$val}}</i> 
	    @endforeach 
	</p></span>
    