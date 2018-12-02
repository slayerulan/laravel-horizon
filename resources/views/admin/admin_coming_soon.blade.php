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
    <h2>DASHBOARD</h2>
</div>

@if (session('alert_msg'))
        <div class="alert alert-{{ session('alert_class') }} alert-dismissible">
             <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">x</span></button>
             <strong>{{ session('alert_msg') }}</strong>
        </div>
@endif

<!-- Widgets -->
<div class="row clearfix">

    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <div class="info-box bg-pink hover-expand-effect">

            <div class="content">
                <div class="text">COMING SOON</div>
            </div>
        </div>
    </div>


</div>


<div class="row clearfix">

    <!-- #END# Browser Usage -->
</div>
</div>
</section>
@include('admin/layout/footer')
