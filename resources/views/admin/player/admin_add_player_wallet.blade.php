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
                            <h2>Add Player Wallet</h2>
                        </div>
                        <div class="body">
                            <form id="user-data" action="{{route('admin-player-management-wallet-management-insert')}}" method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}"/>

                                <div class="body">

                                    <div class="row clearfix">
                                        <div class="col-sm-6">
                                                <label class="form-label">User</label>
                                                <select name="user_id" id="user_id" class="form-control show-tick selectpicker" data-live-search="true" tabindex="-98" required>
                                                    <option value="">-- Please select --</option>
                                                    @foreach($users as $users_data)
                                                    <option value="{{ $users_data->id }}">{{ $users_data->username }}</option>
                                                    @endforeach
                                                </select>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group form-float">
                                                <div class="form-line">
                                                    <input type="text" class="form-control" name="amount" value="{{ old('amount') }}" required>
                                                    <label class="form-label">Chips</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row clearfix">
                                        <div class="col-sm-6">
                                        <label class="form-label">Status</label>
                                        <select name="status" id="status" class="form-control show-tick selectpicker" data-live-search="true" tabindex="-98" required>
                                                    <option value="">-- Please select --</option>
                                                    <option value="active">Active</option>
                                                    <option value="inactive">Inactive</option>
                                        </select>
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
