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

        @if (session('alert_msg'))
            <div class="alert alert-{{ session('alert_class') }} alert-dismissible">
                 <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">x</span></button>
                 <strong>{{ session('alert_msg') }}</strong>
            </div>
        @endif

        @if ($errors->any())
          <div class="alert alert-danger form_validation_error">
           <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">x</span></button>
           <ul class="error_list">
            @foreach ($errors->all() as $error)
                <li><i class="material-icons">info_outline</i> {{ $error }}</li>
               @endforeach
           </ul>
          </div>
        @endif


<div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <!-- Basic Validation -->
            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="header">
                            <h2>Edit User Wallet</h2>
                        </div>
                        <div class="body">
                            <form id="user-data" action="{{route('admin-wallet-management-update')}}" method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}"/>
                                <input type="hidden" name="player_wallet_id" value="{{ $player_wallet_data->id }}" />
                                <input type="hidden" name="wallet_balance" value="{{ $player_wallet_data->amount }}" />

                                <div class="body">
                                    <div class="row clearfix">
                                        <div class="col-sm-6">
                                            <input type="hidden" name="user_id" id="user_id" value="{{ $users->id }}">
                                                <label class="form-label">User : </label> {{ $users->username }}
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="form-label">Chips in wallet: </label> {{ $player_wallet_data->amount }}
                                        </div>
                                        <div class="col-sm-6">
                                            <input type="radio" name="action" value="deposit" checked style="position: relative; left: 0px; opacity: 1;"> Deposit
                                            <input type="radio" name="action" value="withdrawal" style="position: relative; left: 0px; opacity: 1;"> Withdrawal
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="form-group form-float">
                                                <div class="form-line">
                                                    <input type="text" class="form-control" name="amount">
                                                    <label class="form-label">New chips amount</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <button class="btn btn-primary waves-effect" type="submit">Submit</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- #END# Basic Validation -->
        </div>

    <div class="row clearfix">

        <!-- #END# Browser Usage -->
    </div>
</div>
</section>

@include('admin/layout/footer')


<script type="text/javascript">
function getMasterId(id) {
    $.ajax({
             type: "POST",
             url: "{{route('admin-get-basic-agent')}}",
             headers: {
               'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
             },
              data: {'id' : id},
             success: function(data)
             {
                 $("#basic_user")
                       .html(data)
                       .selectpicker('refresh');
             }
            });
};

function getBasicId(id) {
    $.ajax({
             type: "POST",
             url: "{{route('admin-get-players')}}",
             headers: {
               'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
             },
              data: {'id' : id},
             success: function(data)
             {
                 $("#user_id")
                       .html(data)
                       .selectpicker('refresh');
             }
            });
};
</script>
