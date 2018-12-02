@include('admin/layout/header')
<!-- #User Info -->
@include('admin/layout/leftmenubar')
@include('admin/layout/legal')
</aside>
<!-- #END# Left Sidebar -->
@include('admin/layout/rightsidebar')
</section>

<section class="content">
  <div class="container-fluid">
    <div class="block-header">
        <h2>Combo Bet Report</h2>
    </div>

    <div id="msgDiv" class="alert alert-success alert-dismissible" style="display:none;">
             <strong>Successfully Deleted</strong>
    </div>

    <div class="body filter-div">
        <div class="row clearfix">

          <div class="col-sm-4">
            <p class="control-label"><b>Sport</b></p>
            <select class="form-control show-tick" data-live-search="true" id='sport' onChange="getData();">
                  <option value="all" selected>All</option>
                  @foreach ($sports as $sport)
                      <option value="{{ $sport->id }}">{{ $sport->name }}</option>
                  @endforeach
            </select>
          </div>

          <div class="col-sm-4">
              <p class="control-label"><b>Start Date</b></p>
              <div class="form-group">
                  <div class="form-line">
                      <input type="text" class="reportStartDate form-control" value="{{ $start_date }}" placeholder="Please choose a start date...">
                  </div>
              </div>
          </div>

          <div class="col-sm-4">
              <p class="control-label"><b>End Date</b></p>
              <div class="form-group">
                  <div class="form-line">
                      <input type="text" class="reportEndDate form-control" value="{{ $end_date }}" placeholder="Please choose an end date...">
                  </div>
              </div>
          </div>

          <div class="col-sm-4">
            <p><b>Status</b></p>
            <select class="form-control show-tick" data-live-search="true" id='status' onChange="getData();">
                  <option value="all" selected>All</option>
                  <option value="pending">Pending</option>
                  <option value="win">Win</option>
                  <option value="half_win">Half Win</option>
                  <option value="lost">Lost</option>
                  <option value="half_lost">Half Lost</option>
                  <option value="return_stake">Return Stake</option>
                  <option value="cancel">Cancel</option>
            </select>
          </div>

          <div class="col-sm-4">
            <p><b>Agent</b></p>
            <select class="form-control show-tick" data-live-search="true" id='agent' onChange="getData();">
                  <option value="" selected>All</option>
                  @foreach ($agents[0] as $agent)
                      <option value="{{ $agent['id'] }}">{{ $agent['username'] }}</option>
                  @endforeach
            </select>
          </div>

          <div class="col-sm-4">
            <p><b>Player</b></p>
            <select class="form-control show-tick" data-live-search="true" id='player' onChange="getData();">
                  <option value="" selected>All</option>
                  @foreach ($players as $player)
                      <option value="{{ $player->id }}">{{ $player->username }}</option>
                  @endforeach
            </select>
          </div>
        </div>
    </div>

    <div class="loder_cover" style="display: none;" data-original-title="" title="">
        <img src="{{ url('') }}/admin/images/loader.gif" >
    </div>

    <div class="resultDiv">

    </div>

  </div>
</section>

@include('admin/layout/footer')

<script>
$(window).load(function(){
  getData();
});

$('.reportStartDate').bootstrapMaterialDatePicker({
      //format: 'dddd DD MMMM YYYY',
      maxDate : new Date(),
      clearButton: false,
      weekStart: 1,
      time: false
}).on('change', function(e, date)
{
    var startDate = $('.reportStartDate').val();
    var endDate = $('.reportEndDate').val();

    if( (startDate !='' & endDate !='') && (startDate > endDate))
    {
        $('.reportEndDate').val('');
        $('.resultDiv').html('<p>Please select end date!</p>');
    }

    $('.reportEndDate').bootstrapMaterialDatePicker('setMinDate', date);
    getData();
});

$('.reportEndDate').bootstrapMaterialDatePicker({
      //format: 'dddd DD MMMM YYYY',
      maxDate : new Date(),
      clearButton: false,
      weekStart: 1,
      time: false
}).on('change', function(e, date)
{
    getData();
});

function getData()
{
  var sport = $('#sport').val();
  var startDate = $('.reportStartDate').val();
  var endDate = $('.reportEndDate').val();
  var status = $('#status').val();
  var agent = $('#agent').val();
  var player = $('#player').val();

  //if(sport !='' && startDate !='' && endDate !='')
  //{
    $(".loder_cover").show();
    setTimeout(function(){
        $('.loder_cover').fadeOut('fast');
    },500);

    $.ajax({
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {'sport' : sport, 'start_date' : startDate, 'end_date' : endDate, 'status' : status, 'agent' : agent, 'player' : player },
            url: "{{route('admin-report-management-combo-bet-report')}}",
            type:"POST",
            success(result)
            {
                if (result)
                {
                    $('.resultDiv').html(result);
                }
                $(document.body).find('[data-toggle="tooltip"]').tooltip();
            }
        });
  /*}
  if(sport =='' && startDate !='' && endDate !='')
  {
      $('.resultDiv').html('<p>Please select sport!</p>');
  }*/
}
</script>
