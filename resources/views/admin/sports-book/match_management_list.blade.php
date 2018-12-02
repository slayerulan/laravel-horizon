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
        <h2>Match Management</h2>
    </div>

    <div id="msgDiv" class="alert alert-success alert-dismissible" style="display: none;">
          <strong>Successfully Updated</strong>
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
                      <input type="text" class="reportStartDate form-control" value="{{date('Y-m-d')}}" placeholder="Please choose a start date....">
                  </div>
              </div>
          </div>

          <div class="col-sm-4">
              <p class="control-label"><b>End Date</b></p>
              <div class="form-group">
                  <div class="form-line">
                      <input type="text" class="reportEndDate form-control" value="{{date('Y-m-d', strtotime(date('Y-m-d'). ' + 4 days'))}}" placeholder="Please choose an end date....">
                  </div>
              </div>
          </div>

        </div>
    </div>

    <div class="loder_cover" style="display: none;">
        <img src="{{ url('') }}/admin/images/loader.gif">
    </div>

    <div class="resultDiv">

    </div>
    <input type="hidden" id="page_no" name="page_no" value="1">
  </div>
</section>

@include('admin/layout/footer')

<script>
$(window).load(function(){
    cdate = new Date();
    var jdate = JSON.stringify(cdate);
    var ndate = jdate.split('T');
    var ndate = ndate[0].replace('"','');
    var max_date = new Date(ndate);
    max_date.setDate(max_date.getDate()+4);
    $('.reportEndDate').bootstrapMaterialDatePicker('setMaxDate', max_date);

    getData();
});


$('.reportStartDate').bootstrapMaterialDatePicker({
      //format: 'dddd DD MMMM YYYY',
      clearButton: false,
      weekStart: 1,
      time: false
}).on('change', function(e, date)
{
    var jdate = JSON.stringify(date);
    var ndate = jdate.split('T');
    var ndate = ndate[0].replace('"','');
    var max_date = new Date(ndate);
    max_date.setDate(max_date.getDate()+4);

    var startDate = $('.reportStartDate').val();
    var endDate = $('.reportEndDate').val();

    $('.reportEndDate').bootstrapMaterialDatePicker('setMaxDate', max_date);
    $('.reportEndDate').bootstrapMaterialDatePicker('setMinDate', date);
    getData();
});

$('.reportEndDate').bootstrapMaterialDatePicker({
      //format: 'dddd DD MMMM YYYY',
      minDate : new Date(),
      clearButton: false,
      weekStart: 1,
      time: false
}).on('change', function(e, date)
{
    getData();
});

function getData() {
    $('#page_no').val('1');
    var startDate = $('.reportStartDate').val();
    var endDate = $('.reportEndDate').val();

    var max_date = new Date(startDate);
    max_date.setDate(max_date.getDate()+4);

    if((startDate > endDate) || (new Date(endDate) > new Date(max_date))) {
        $('.reportEndDate').val('');
        $('.resultDiv').html('<p>Please select a end date which is within the range of 4 days with the start date!</p>');
    }
    else{
        getMatches();
    }
}

function getMatches() {
    var page = $('#page_no').val();
    var sport = $('#sport').val();
    var start_date = $('.reportStartDate').val();
    var end_date = $('.reportEndDate').val();
    $('.resultDiv').html('');
    $(".loder_cover").fadeIn();
    $.ajax({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {'sport' : sport, 'start_date' : start_date, 'end_date' : end_date, 'page': page},
        url: "{{route('admin-sports-book-management-post-matches')}}",
        type:"POST",
        success(result) {
            $('.loder_cover').hide();
            if (result) {
                $('.resultDiv').html(result);
            }
        }
    });
}

$(document.body).on('click',".page",function() {
    var page_selected = $(this).attr("data-page");
    $('#page_no').val(page_selected);
    getMatches();
});

var old = '';
$(document.body).on('focus',".status_change",function() {
    old = $(this).val();
}).on('change',".status_change",function(){
    var this_class = $(this);
    swal({
        title: "Do you really want to change the status ?",
        text: "It will refund credits into player's wallet",
        type: "warning",
        html:true,
        showSpinner: true,
        showCancelButton: true,
        confirmButtonClass: "btn-danger",
        confirmButtonText: "yes",
        cancelButtonText: "cancel",
        closeOnConfirm: true,
        closeOnCancel: true
    },function(isConfirm){
        if (isConfirm) {
            var id = this_class.attr("data-id");
            var td_id = $('#status_'+id);
            td_id.html('Loading....');
            var value = this_class.val();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {'id' : id, 'value' : value},
                url: "{{route('admin-sports-book-management-post-change-match-status')}}",
                type:"POST",
                success(result) {
                    if (result == 1) {
                        td_id.html(value);
                        $('#msgDiv').fadeIn();
                        setTimeout(function(){$('#msgDiv').fadeOut();}, 2000);
                    }
                }
            });
        }
        else{
            this_class.val(old);
        }
    });
});
</script>
