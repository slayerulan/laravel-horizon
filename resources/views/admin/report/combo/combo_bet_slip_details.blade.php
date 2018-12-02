

          <div class="modal-dialog">
            <!-- Modal content start -->
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Combo Bet Slip Details</h4>
              </div>

              <div class="modal-body">
                <div>
                  <table class="table table-striped newTickets combo_bet_slip">
                  		<tbody>
                        <tr>
                           <td><b>Agent</b></td>
                           <td>{{ $data['agent'] }}</td>
                        </tr>
                        <tr>
                           <td><b>Player</b></td>
                           <td>{{ $data['player'] }}</td>
                        </tr>
                        <tr>
                           <td><b>Sports</b></td>
                           <td>{{ $data['sportName'] }}</td>
                        </tr>
                        <tr>
                           <td><b>League</b></td>
                           <td>{{ $data['country'].' : '.$data['league'] }}</td>
                        </tr>
                        <tr>
                           <td><b>Match Between</b></td>
                           <td>{{ $data['homeTeam'] }} | {{ $data['awayTeam'] }}</td>
                        </tr>
                  			<tr>
                  			   <td><b>Match Date Time</b></td>
                           <td>{{ $data['match_date_time'] }}</td>
                  			</tr>
                  			<tr>
                  			   <td><b>Market Name</b></td>
                            <td>{{ $data['market_name'] }}</td>
                  			</tr>
                  			<tr>
                  			   <td><b>Bet For</b></td>
                  				 <td>{{ $data['bet_for'] }}</td>
                  			</tr>
                  			<tr>
                  			   <td><b>Bet Value</b></td>
                  				 <td>{{ $data['bet_value'] }}</td>
                  			</tr>
                  			<tr>
                  			   <td><b>New Bet Value</b></td>
                  				 <td>{{ $data['calculated_odds'] }}</td>
                  			</tr>
                        <tr>
                  			   <td><b>Bet Result</b></td>
                  				 <td>{{ str_replace('_',' ',$data['result']) }}</td>
                  			</tr>
                        <tr>
                  			   <td><b>Result of Match</b></td>
                  				 <td>
                             @foreach(SCOREPARSER($data['score'],$data['sportName']) as $val)
	    	                        <i data-original-title="" title="">{{$val}}</i>
	                           @endforeach
                           </td>
                  			</tr>
                      </tbody>
	                  </table>
                </div>
              </div>
            </div>
            <!-- Modal content end -->
          </div>
