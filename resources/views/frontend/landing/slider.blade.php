<!-- Carousel -->
<div class="tab_buttons">
   <a href="javascript:void(0);" class="active loggedin-pre">Pre Match</a>
   <a href="javascript:void(0);" class="loggedin-live">Live Match</a>
</div>
<div id="myCarousel" class="carousel slide" data-ride="carousel">
   	<div class="carousel-inner" role="listbox">
   		@foreach($banner_images as $key => $banner)
	  		   <div class="item <?php if($key == 1)echo 'active';?>">
   		 		<img class="first-slide" src="{{asset('storage/'.$banner->image)}}" alt="{{$banner->title}}">
   	  		</div>
   	  	@endforeach
   	</div>
   	<a class="left carousel-control" href="#myCarousel" role="button" data-slide="prev">
   		<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
   		<span class="sr-only">Previous</span>
   	</a>
   	<a class="right carousel-control" href="#myCarousel" role="button" data-slide="next">
   		<span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
   		<span class="sr-only">Next</span>
   	</a>
</div>
<!-- /.carousel -->
