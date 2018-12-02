<!-- Menu -->
<div class="menu">
    <ul class="list">
        <li class="@if(Route::current()->getName() == 'admin-dashboard') active  @endif">
            <a href="{{route('admin-dashboard')}}">
                <i class="material-icons">home</i>
                <span>Dashboard</span>
            </a>
        </li>

        @foreach($parent_menu as $parent_value)
        <li class="@if(starts_with(Route::current()->getAction()['as'],'admin-'.$parent_value->slug_name)) active  @endif">
			@if ($parent_value->is_group == 'no')
				<a href="{{route('admin-'.$parent_value->slug_name)}}">
	                <i class="material-icons">{{ $parent_value->icon }}</i>
	                <span>{{ $parent_value->title }}</span>
	            </a>
			@else
				<a href="javascript:void(0);" class="menu-toggle">
					<i class="material-icons">{{ $parent_value->icon }}</i>
					<span>{{ $parent_value->title }}</span>
				</a>
	            <ul class="ml-menu">
                    @if(isset($sub_menu[$parent_value->id][0]))
    	                @foreach($sub_menu[$parent_value->id][0] as $value)
    	                <li class="@if(str_is('admin-'.$parent_value->slug_name.'-'.str_replace_last('-list','',$value->slug_name).'*',Route::current()->getAction()['as']))) active  @endif" >
    	                    <a href="{{ route('admin-'.$parent_value->slug_name.'-'.$value->slug_name) }}">{{ $value->title }}</a>
    	                </li>
    	                @endforeach
                    @endif
	            </ul>
			@endif
        </li>
        @endforeach
        <li class="active" style="display:none;"><a href="javascript:void(0);"></a></li>
		@if(Session::get('role_id') == 1)
		<li class="">
            <a target="_blank" href="{{url('apex-site-admin/translations-management')}}">
                <i class="material-icons">g_translate</i>
                <span>Translation</span>
            </a>
        </li>
		@endif
    </ul>
</div>
            <!-- #Menu -->
