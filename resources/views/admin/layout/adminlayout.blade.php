@include('admin/layout/header')
@include('admin/layout/leftmenubar')
@include('admin/layout/legal')
</aside>
@include('admin/layout/rightsidebar')
</section>
  
<section class="content">
	<div class="container-fluid">
		<div class="block-header">
		</div>

		@if (session('alert_msg'))
			<div class="alert alert-{{ session('alert_class') }} alert-dismissible">
				 <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">x</span></button>
				 <strong>{{ session('alert_msg') }}</strong>
			</div>
		@endif

		<div class="row clearfix">
			<div class="row clearfix">
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
					<div class="card">
						<div class="header">
                            <h2>@if(isset($page_title)) {{ $page_title }} @endif</h2>
                        </div>
						<div class="body">
							@yield('content')
						</div>
					</div>
				</div>
			</div>
		</div>

<div class="row clearfix">

</div>
</div>
</section>
@include('admin/layout/footer')
