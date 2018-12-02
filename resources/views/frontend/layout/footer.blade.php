<div class="footer">
	<div class="container">
		<ul>
			<li>
				<a href="{{ route('front-home') }}">{{ __('label.Home') }}</a>
			</li>
			@forelse ($cms_pages as $key => $page)
				<li>
					<a href="{{ url('view').'/'.$page->slug_name }}">{{ __('label.'.$page->slug_name) }}</a>
				</li>
			@empty
			@endforelse
		</ul>
	</div>

	<div class="copyright">{{ date('Y') }} &copy; {{ __('label.site_fotter_title') }} </div>


</div>
<b id="error_title" data-value="{{__('label.Error!')}}"></b>
<b id="success_title" data-value="{{__('label.Success!')}}"></b>
<b id="info_title" data-value="{{__('label.Attention!')}}"></b>
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="{{ asset('frontend/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('frontend/js/jasny-bootstrap.min.js') }}"></script>

<script src="{{ asset('frontend/js/owl.carousel.js') }}"></script>
<script src="{{ asset('frontend/js/jquery.mousewheel.min.js') }}"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jQuery-Validation-Engine/2.6.4/languages/jquery.validationEngine-en.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jQuery-Validation-Engine/2.6.4/jquery.validationEngine.min.js"></script>
<!-- Jquery DataTable Plugin Js -->
<script src="{{ asset('/frontend/plugins/jquery-datatable/jquery.dataTables.js') }}"></script>
<script src="{{ asset('/frontend/plugins/jquery-datatable/skin/bootstrap/js/dataTables.bootstrap.js') }}"></script>
<script src="{{ asset('/frontend/plugins/jquery-datatable/extensions/export/dataTables.buttons.min.js') }}"></script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>

<script src="{{ asset('frontend/js/custom_design.js') }}"></script>
<script>
$(document).ready(function(){
    $('.dropdown-toggle').dropdown();
});
$(document).ready(function(){
    $("form").validationEngine();
   });
   $('.check_unique').blur(function(){
   	var field = $(this).attr('name');
		var id 	  = $(this).attr('data-id') !== undefined ? $(this).attr('data-id') : 0;
   	var value = $(this).val();
   	$.ajax({
		headers: {
		        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},
		type : 'POST',
		data : {'field':field,'value': value,'id':id},
   		url  : "{{ route('check-unique') }} ",
   		success: function(data){
   			if(parseInt(data) < 1){
				$('#error_'+field).html('{{__('validation.already exist')}}');
			}else{
				$('#error_'+field).html('');
			}
   		}
   	});
   });
   $('.change_language').click(function(){
	   var lang = $(this).attr('data-lang');
	   $.ajax({
   		headers: {
   		        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
   		},
   		type : 'POST',
   		data : {'lang':lang},
      		url  : "{{ route('change-language') }}",
      		success: function(data){
				window.location.reload();
      		}
      	});
   });
   $('.change_odds_type').click(function(){
	   var oddsType = $(this).attr('data-odds-type');
	   $.ajax({
   		headers: {
   		        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
   		},
   		type : 'POST',
   		data : {'odds_type':oddsType},
      		url  : "{{ route('change-odds-type') }}",
      		success: function(data){
				window.location.reload();
      		}
      	});
   });
</script>
<script src="{{ asset('js/app.js') }}"></script>
@yield('script')
	<script type="text/javascript">
		function loadExtraOdds(sport_id, match_id) {
			if(isNaN(sport_id) == false || isNaN(match_id) == false) {
				if($('#extra_odds_of_'+match_id).children().hasClass('ods_block') == false) {
					axios.post("{{ route('front-sports-fetch-extra-odds') }}", {'sport_id':sport_id, 'match_id': match_id})
						.then((response)  =>  {
							$('#extra_odds_of_'+match_id).html(response.data);
						}, (error)  =>  {
							showError('{{ __('alert_info.Something went wrong!') }}');
						});
				}
				$('#extra_odds_of_'+match_id).animate({ height: "toggle" }, 500);
				$('.icon_class_'+match_id).toggleClass('fa-minus fa-plus');
			} else {
				showError('{{ __('alert_info.Invalid Selection') }}');
			}
		}

		$(document.body).on('click', '.expand-odds', function(evt) {
			var match_id = $(this).attr('data-id');		
			$('#each_odd_'+match_id).animate({ height: "toggle" }, 500);
				$('.icon_class_'+match_id).toggleClass('fa-minus fa-plus');
			
		});

		function placeBet (oddsId) {
			axios.post("{{ route('front-prematch-betting-place-bet') }}", {'odds_id': oddsId})
				.then((response)  =>  {
					$(document.body).find('.clapsible_bet').html(response.data);
					restoreBetSlipData();
					$('.clapsible_bet').show();
				}, (error)  =>  {
					showError('{{ __('alert_info.Something went wrong!') }}');
				})
		}
		function placeLiveBet(country, league, match_id, market_id, bet_for) {
			axios.post("{{ route('front-live-betting-place-bet') }}", {'country': country, 'league': league, 'match_id': match_id, 'market_id': market_id, 'bet_for': bet_for})
				.then((response)  =>  {
					$(document.body).find('.clapsible_bet').html(response.data);
					restoreBetSlipData();
					$('.clapsible_bet').show();
				}, (error)  =>  {
					showError('{{ __('alert_info.Something went wrong!') }}');
				})
		}
		function unsetBet(id) {
			$(document.body).find('.bet_slip_'+id).remove();
			$(".each_odd").each(function() {
				if ($(this).attr('data-id') == id) {
					$(this).removeClass('active');
				}
			});
			axios.post("{{ route('front-prematch-betting-remove-bet') }}", {'odd_id': id})
				.then((response)  =>  {
					if(response.data == 0)
						showError('{{ __('alert_info.Something went wrong!') }}');
					else
						$(document.body).find('.clapsible_bet').html(response.data);
						restoreBetSlipData();
				}, (error)  =>  {
					showError('{{ __('alert_info.Something went wrong!') }}');
				})
		}
		function refreshBetSlip() {
			axios.get("{{ route('front-prematch-betting-bet-slip') }}")
				.then((response)  =>  {
					$(document.body).find('.clapsible_bet').html(response.data);
					restoreBetSlipData();
				}, (error)  =>  {
					showError('{{ __('alert_info.Something went wrong!') }}');
			});
			getWalletBalence();
		}
		function getWalletBalence() {
			axios.post("{{ route('front-post-wallet-balance') }}")
			.then((response)  =>  {
				$(document.body).find('#wallet_balance').html(response.data);
			});
		}
		function calculateAmount(element, action, update = true) {
			var calculate_bind 	= element.attr('calculate-data-bind');
			var data_bind 	= element.attr('data-bind');
			var amount 	= parseInt(element.val());
			var odds 	= parseFloat(element.attr('data-value'));
			var result 	= 0;
			amount = isNaN(amount) ? 0 : amount;
			if(action == '/') {
				result 	= Math.ceil(amount / odds);
				if (calculate_bind == 'combo_stake_amount') {
					var maximum_payout = $('#maximum_parlay_payout').val();
					if (maximum_payout != '' && amount > Math.floor(maximum_payout)) {
						element.val(maximum_payout)
						result = Math.floor(maximum_payout / odds);
					}
				}
				else{
					var maximum_payout = $('#maximum_straight_bet_payout').val();
					if (maximum_payout != '' && amount > Math.floor(maximum_payout)) {
						element.val(maximum_payout)
						result = Math.floor(maximum_payout / odds);
					}
				}
			}
			else {
				result 	= Math.floor(amount * odds);
				if (calculate_bind == 'combo_prize_amount') {
					var maximum_payout = $('#maximum_parlay_payout').val();
					if (maximum_payout != '' && result > Math.floor(maximum_payout)) {
						result = Math.floor(maximum_payout);
					}
				}
				else{
					var maximum_payout = $('#maximum_straight_bet_payout').val();
					if (maximum_payout != '' && result > Math.floor(maximum_payout)) {
						result = Math.floor(maximum_payout);
					}
				}
			}
			
			result = isNaN(result) ? 0 : result;
			$('.'+data_bind).html(amount);
			$( "input[name='"+calculate_bind+"']" ).val(result);
			$('.'+calculate_bind).html(result);
			calculateTotalSingleBetAmount(update);
		}
		function calculateTotalSingleBetAmount(update=true) {
			var single_total_stake = 0;
			var single_total_prize = 0;
			$(document.body).find('.single_stake_amount').each(function() {
				var value = $(this).val() == '' ? 0 : parseInt($(this).val());
				single_total_stake += value;
				if(update) {
					sessionStorage.setItem($(this).attr('id'), value);
				}
			});
			$(document.body).find('.single_prize_amount').each(function() {
				single_total_prize += parseInt($(this).val());
			});
			single_total_stake = isNaN(single_total_stake) ? 0 : single_total_stake;
			single_total_prize = isNaN(single_total_prize) ? 0 : single_total_prize;

			$(document.body).find('#single_total_stake').html(single_total_stake);
			$(document.body).find('#single_total_prize').html(single_total_prize);
		}
		function restoreBetSlipData() {
			$(document.body).find('.stake_field').each(function() {
				var old_value = sessionStorage.getItem($(this).attr('id'));
				$(this).val(old_value);
				calculateAmount($(this), '*', false);
			});
			$(document.body).find('[data-toggle="tooltip"]').tooltip();
			$('.counter_bet').html(parseInt($('.each_single_bet').length));
		}
		$(document.body).on('click', '.each_odd', function(evt) {
			var bet_type = $(this).attr('data-type');
			var id = $(this).attr('data-id');
			if($(this).hasClass('active')) {
				$(this).removeClass('active');
				unsetBet(id);
			} else {
				$(".each_odd").each(function() {
					if ($(this).attr('data-id') == id) {
						$(this).addClass('active');
					}
				});
				if (bet_type == 'LM') {
					var country = $(this).attr('data-country');
					var league = $(this).attr('data-league');
					var match_id = $(this).attr('data-match-id');
					var market_id = $(this).attr('data-market-id');
					var bet_for = $(this).attr('data-bet-for');
					placeLiveBet(country, league, match_id, market_id, bet_for);
				}
				else{
					placeBet(id);
				}
			}
		});
		$(document.body).on('keypress', '.only_number_input', function(evt) {
		    evt = (evt) ? evt : window.event;
		    var charCode = (evt.which) ? evt.which : evt.keyCode;
		    if (charCode > 31 && (charCode < 48 || charCode > 57)) {
		        return false;
		    }
		    return true;
		});
		$(document.body).on('keyup', '.stake_field', function() {
			$('.stake_per_single_bet').val('');
			calculateAmount($(this), '*');
			if ($(this).attr('id') == 'combo_stake_amount') {
				var value = isNaN($(this).val()) ? 0 : parseInt($(this).val());
				sessionStorage.setItem($(this).attr('id'), value);
			}
		});
		$(document.body).on('keyup', '.prize_field', function() {
			calculateAmount($(this), '/');
		});
		$(document.body).on('keyup', '.stake_per_single_bet', function() {
			var amount = $(this).val();
			amount = isNaN(amount) ? 0 : amount;
			$(document.body).find('.single_stake_amount').each(function(){
				$(this).val(amount);
				calculateAmount($(this), '*');
			});
		});
		$(document).ready(function(){
			restoreBetSlipData();
			// getWalletBalence();
		});
		$(document.body).on('click', '#place_single_bet', function() {
			var except_single_odds_changes = '';
			except_single_odds_changes = $('input[name="except_single_odds_changes"]:checked').val();
			var stake_amount = $("input[name^='pre_stake_amount']").map(function (idx, ele) {
			   return $(ele).val();
			}).get();

			var odds_values = $("input[name^='pre_stake_amount']").map(function (idOdd, oddEle) {
			   return $(oddEle).attr('data-value');
			}).get();
			if (parseInt(stake_amount) > 0) {
				$('#place_single_bet').prop('disabled', true);
				axios.post("{{ route('front-prematch-betting-save-single-bet') }}", {'stake_amount': stake_amount, 'odds_values': odds_values, 'except_single_odds_changes': except_single_odds_changes})
					.then((response)  =>  {
						var alertfunction	= 'show'+response.data.status;
						eval(alertfunction + '("' +response.data.message + '")');
						if (response.data.status == 'Success') {
							$(".each_odd").each(function() {
								$(this).removeClass('active');
							});
						}
						refreshBetSlip();
						$('#place_single_bet').prop('disabled', false);
					}, (error)  =>  {
						showError('{{ __('alert_info.Something went wrong!') }}');
						$('#place_single_bet').prop('disabled', false);
				});
			}
			else{
				showError('{{ __('alert_info.Invalid stake amount!') }}');
			}
		})
		$(document.body).on('click', '#place_combo_bet', function() {
			var except_combo_odds_changes = '';
			except_combo_odds_changes = $('input[name="except_combo_odds_changes"]:checked').val();
			var stake_amount = $("#combo_stake_amount").val();
			var odds_values = $("input[name^='combo_odds_values']").map(function (idOdd, oddEle) {
			   return $(oddEle).val();
			}).get();
			var total_num_of_bet = $("#total_num_of_bet").attr('data-totalbet');
			if(parseInt(stake_amount) > 0) {
				$('#place_combo_bet').prop('disabled', true);
				axios.post("{{ route('front-prematch-betting-save-combo-bet') }}", {'stake_amount': stake_amount, 'bet':total_num_of_bet, 'odds_values': odds_values, 'except_combo_odds_changes': except_combo_odds_changes})
					.then((response)  =>  {
						var alertfunction	= 'show'+response.data.status;
						eval(alertfunction + '("' +response.data.message + '")');
						if (response.data.status == 'Success') {
							$(".each_odd").each(function() {
								$(this).removeClass('active');
							});
						}
						refreshBetSlip();
						$('#place_combo_bet').prop('disabled', false);
					}, (error)  =>  {
						showError('{{ __('alert_info.Something went wrong!') }}');
						$('#place_combo_bet').prop('disabled', false);
				});
			} else {
				showError('{{ __('alert_info.Invalid stake amount!') }}');
			}
		})
	</script>
<script type="text/javascript">
	$('.js-exportable').DataTable({
		"order": [[ 8, "desc" ]]
		@if(isset($show_export) && $show_export)

		dom: 'Bfrtip',
		responsive: false,
		buttons: [
			'copy', 'excel', 'print'
		]
		@endif
	});
</script>

<!-- Script for change status in support ticket listing page - start -->
<script>
    $(document.body).on('change',".change_status",function(event)
    {
    	swal({
    	  title: "Are you sure?",
    	  type: "warning",
    	  showCancelButton: true,
    	  confirmButtonColor: "green",
    	  confirmButtonText: "Yes, Change it!",
    	  closeOnConfirm: true
    	},
    	function(){
    		var id =$(event.target).attr('data-value');
    		var status_id = $(event.target).val();
    		if(status_id.length > 0){
    			$.ajax({
                 type: "POST",
                 url: "{{route('front-change-status')}}",
                 headers: {
                   'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                 },
                  data: {'id' : id, 'status_id' : status_id},
                 success: function(data)
                 {
                    if(data == 1)
                    {
                        $('#msgDiv').show();
                        setTimeout(function(){
                            $('#msgDiv').fadeOut('slow');
                        },1000);
                    }
                 }
        });
    		}
    	});
    });
</script>
<!-- Script for change status in support ticket listing page - end -->

<!--####    Support Ticket unread check start    ####-->
<script type="text/javascript">
    $(document).ready(function(){
        $.ajax({
            url: "{{route('front-unread-ticket-reply')}}",
						headers: {
                   'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                 },
            type:"POST",
            success(result){
                if (result > 0)
                {
                    $('#unread_ticket_header').html(result);
                }
                else
                {
                    $('#unread_ticket_header').html('');
                }
            }
        });


    });

 function financial(x) {
  return Number.parseFloat(x).toFixed(2);
}
</script>
<!--####    Support Ticket unread check end    ####-->
@yield('script')
	<script type="text/javascript">
		$(document).ready(function() {
		  var owl = $('.owl-carousel');
		  owl.owlCarousel({
		  	dots: false,
		  	items: 2,
			loop: true,
			nav: true,
			smartSpeed :900,
     		navText : ["<i class='fa fa-chevron-left'></i>","<i class='fa fa-chevron-right'></i>"],
			margin: 5,
			// responsiveClass:true,
		    responsive:{
		        0:{
		            items:1.5,
		        },
		        350:{
		            items:1.75,
		        },
		        400:{
		            items:2,
		        },
		        450:{
		            items:2.25,
		        },
		        500:{
		            items:2.50,
		        },
		        550:{
		            items:2.75,
		        },
		        600:{
		            items:3,
		        },
		        700:{
		            items:3.5,
		        },
		        1000:{
		            items:3.75,
		        },
		        1200:{
		            items:4,
		        }
		    }
		  });
		  owl.on('mousewheel', '.owl-stage', function(e) {
			if (e.deltaY > 0) {
			  owl.trigger('next.owl');
			}
			else {
			  owl.trigger('prev.owl');
			}
			e.preventDefault();
		  });
		});
	</script>

	<script>
  $("ul.tabs li").click(function(){
    var activeTab = $(this).attr("rel");
    if(activeTab == 'tab1')
    {
      $(".tab1_form").show();
      $(".tab2_form").hide();
      $(".tab_one_content").show();
    }
    else
    {
      $(".tab2_form").show();
      $(".tab1_form").hide();
      $(".tab_one_content").hide();

      $('.cloned').remove();
    }
  });

  $(".loggedin-live").click(function(){
  	$("#navbar>ul>li.active").removeClass("active");
  	$(".tab_buttons>a.active").removeClass("active");

  	$(this).addClass('active');
  	$(".loggedin-live").addClass('active');

	$(".tab2_form").show();
	$("#tab2").show();
	$("#tab1").hide();
	$("#tab2").addClass('active');
	$(".tab1_form").hide();
	$(".tab_one_content").hide();
	$('.cloned').remove();
  });
	$(".loggedin-pre").click(function(){
		$(".tab1_form").show();
		$(".tab2_form").hide();
		$(".tab_one_content").show();
  	});
  	$(document).ready(function(){
	  	$(document.body).find('.each_odd').each(function(){
		  	var oddsArr = new Array();
		  	<?php if(session('unique_bet_key') && is_array(session('unique_bet_key'))){ ?>
		    <?php foreach(session('unique_bet_key') as $key => $val){ ?>
		        oddsArr.push('<?php echo $val; ?>');
		    <?php }} ?> 	
		     		
			var id = $(this).attr('data-id');
			if(jQuery.inArray(id, oddsArr) !== -1){
				$(this).addClass('active');
			}
		});
	  		
	  	var allowd_market =["Fulltime Result","Double Chance","Half Time Result","To Qualify","Match Goals","First Half Goals","Half Time/Full Time","Half Time Correct Score","Final Score","3-Way Handicap","1st Half 3-Way Handicap","Draw No Bet","Goals Odd/Even","Result / Both Teams To Score","Both Teams to Score","Both Teams to Score in 1st Half","Both Teams to Score in 2nd Half","Home Team Clean Sheet","Away Team Clean Sheet","Home Team Exact Goals","Away Team Exact Goals","Home Team Goals","Away Team Goals","Home Team to Score in Both Halves","Away Team to Score in Both Halves","To Win 2nd Half"];
		  	$(document.body).find('.hide-market').each(function(){		     		
				var show_market = $(this).attr('data-market-show');
				if(jQuery.inArray(show_market, allowd_market) !== -1){
					$(this).show();
				}else{
					$(this).hide();
				}
			});
		});

	function liveMatchApi(handleData){
		var dataApi = [];		
		var url = "http://apexsports.asia:4000/api/getlivefeed";
		$.ajax({
		  url: url,
		  dataType: 'json',
		  async: false,
		  success: function(data) {		   
		   	var dataApi = setAlldata(data.message);
		   	handleData(dataApi);
		  }
		});

	}      


   /**
   * this will get feed data
   */
    function setAlldata(res){    	
      	var allCountries = [];
      	var league_details  = [];
      	var league = [];  
      	var totalCountry = 0;
      	if (_.toArray(res[0]).length) {
          	var finalData = res[0];
          	totalCountry = _.toArray(res[0]).length;
          	var liveMatchCounter = 0;
          	for (let key in finalData) {          		
              	if (key != 'updated') {
                  	var leagues = [];
                  	var leaguesName =[];
                  	for (let leagueKey in finalData[key]) {
                    	var leagues = _.toArray(finalData[key]).length;
                    	var league_data = finalData[key];
                    	var l_name = leagueKey;                    	
                  	}
                  	var eachCountry = { "country": key, 'leagues': leagues ,"league_data": league_data ,"leagues_name" : l_name};
                  	allCountries.push(eachCountry);   
              	}
          	}
    	}   	
    	return {'allCountries':allCountries,'totalCountry':totalCountry}
   	}

   /**
   * Socket connection done here
   */

   	var socket = io('http://apexsports.asia:5000');	
   	// console.log(socket);
	Vue.filter('round', function(value, decimals) {
	  if(!value) {
	    value = 0;
	  }
	  if(!decimals) {
	    decimals = 0;
	  }
	  value = Math.round(value * Math.pow(10, decimals)) / Math.pow(10, decimals);
	  return value;
	});         

   	var app = new Vue({
    	el: '#tab2',
    	data: {
        	message: 0,
        	objects: {}
        },
        mounted:function(){
        	var mongoData ={}; 
        	liveMatchApi(function(mongoData){
	        	this.objects= mongoData.allCountries;
				this.message= mongoData.totalCountry;        		
        	}.bind(this));
        	socket.on('feed', function(data) {         	 
        	if(_.toArray(data[0]).length != 0){
	        	var dt = {};           		
				var dt = setAlldata(data);				
				this.objects= dt.allCountries;
				this.message= dt.totalCountry;
        	}        	
			}.bind(this));
        },
        methods: {
		    showByCoutry: function (country,is_true) {
	    	 	live_match.country = country;
	    	 	if(is_true==1){
	    	 	 	live_match.show_all= true;
	    	 	}else{
	    	 	 	live_match.show_all= false;
	    	 	}
			
		    },
		    getOddsValue: function(odds_value) {
		    	var odds_type = '<?php echo Session::get('odds_value_type'); ?>';
				var oddsValue = Math.round(odds_value*100)/100;
				if (odds_type == 'American') {
					if (odds_value >= 2) {
						oddsValue = '+'+Math.round(((Math.round(odds_value*100)/100)-1)*100);
					}
					else{
						if ((Math.round(odds_value*100)/100) == 1) {
							oddsValue = '+000';
						}
						else{
							oddsValue = '-'+Math.ceil(100/((Math.round(odds_value*100)/100)-1));
						}
					}
				}
				return oddsValue;
		    }
	  	}
  	})
  	var live_match = new Vue({
    	el: '#live_match_list',
    	data: {
        	objects: {},
        	show: false,
        	country:'',
        	show_all:true
        },
        mounted:function(){
        	var mongoData ={}; 
        	liveMatchApi(function(mongoData){
	        	this.objects= mongoData.allCountries;
        	}.bind(this));
        	socket.on('feed', function(data) {
        	if(_.toArray(data[0]).length != 0){
	        	var dt = {};           		
				var dt = setAlldata(data);				
				this.objects= dt.allCountries;
        	}         	
			}.bind(this));
        } 
	})
	function showAllCountry(el){
		live_match.show_all= true;
		if ($(window).width() < 767) {
			$(el).attr({"data-toggle":'collapse', "data-target":'#navbar',"aria-expanded": 'false'});
		}
	}
	$(function(){
 
	$('#title').keyup(function()
	{
		var yourInput = $(this).val();
		re = /[`~!@#$%^&*()_|+\-=?;:'",.<>\{\}\[\]\\\/]/gi;
		var isSplChar = re.test(yourInput);
		if(isSplChar)
		{
			var no_spl_char = yourInput.replace(/[`~!@#$%^&*()_|+\-=?;:'",.<>\{\}\[\]\\\/]/gi, '');
			$(this).val(no_spl_char);
		}
	});
 
});
  </script>
</body>

</html>
