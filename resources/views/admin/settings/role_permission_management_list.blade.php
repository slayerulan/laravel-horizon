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
    <h2>Role Permission Management</h2>
</div>


        <div id="msgDiv" class="alert alert-success alert-dismissible" style="display:none;">
             <strong>Successfully Updated</strong>
        </div>


<!-- Example Tab -->
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="body">
                            <!-- Nav tabs -->
                            <ul class="nav nav-tabs tab-nav-right" role="tablist">
                                <?php /**/ $i = 1 /**/ ?>
                                @foreach($roles as $role)
                                <li role="presentation" @if ($i==1)class='active'@endif><a href="#{{ $role['id'] }}" data-toggle="tab">{{ $role['role_name'] }}</a></li>
                                <?php /**/ $i++ /**/ ?>
                                @endforeach
                            </ul>

                            <!-- Tab panes -->
                            <form id="role_management" method="POST" action="">
                            <div class="tab-content">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}"/>

                                <?php /**/ $j = 1 /**/ ?>
                                @foreach($roles as $role)
                                <div role="tabpanel" class="tab-pane fade in <?php if($j==1){ echo 'active'; } ?>" id="{{ $role['id'] }}">
                                    <div class="body table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Role name</th>
                                                    <th>View</th>
                                                    <th>Add</th>
                                                    <th>Edit/Delete</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($roles as $key => $rolesData)
                                                <tr>
                                                    <td>{{ $rolesData['role_name'] }}</td>

                                                    @if(isset($permissions))
                                                        <td>
                                                            <div class="switch">
                                                               <label>No<input name="can_view" id="can-view-{{ $role['id'] }}-{{ $rolesData['id'] }}" onchange="changePermission('can_view',this,{{ $role['id'] }},{{ $rolesData['id'] }})" type="checkbox" @if(isset($permissions[$rolesData['id'].'-'.$role['id']]['can_view']) && $permissions[$rolesData['id'].'-'.$role['id']]['can_view']==1) checked="" @endif><span class="lever"></span>Yes</label>
                                                            </div>
                                                        </td>

                                                        <td>
                                                            <div class="switch">
                                                               <label>No<input name="can_add" id="can-add-{{ $role['id'] }}-{{ $rolesData['id'] }}" onchange="changePermission('can_add',this,{{ $role['id'] }},{{ $rolesData['id'] }})" type="checkbox" @if(isset($permissions[$rolesData['id'].'-'.$role['id']]['can_add']) && $permissions[$rolesData['id'].'-'.$role['id']]['can_add']==1) checked="" @endif><span class="lever"></span>Yes</label>
                                                            </div>
                                                        </td>

                                                        <td>
                                                            <div class="switch">
                                                               <label>No<input name="can_modify" id="can-modify-{{ $role['id'] }}-{{ $rolesData['id'] }}" onchange="changePermission('can_modify',this,{{ $role['id'] }},{{ $rolesData['id'] }})" id="can_modify" type="checkbox" @if(isset($permissions[$rolesData['id'].'-'.$role['id']]['can_modify']) && $permissions[$rolesData['id'].'-'.$role['id']]['can_modify']==1) checked="" @endif><span class="lever"></span>Yes</label>
                                                            </div>
                                                        </td>
                                                    @endif
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                </div>
                                </div>
                                <?php /**/ $j++ /**/ ?>
                                @endforeach
                        </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- #END# Example Tab -->


<div class="row clearfix">

    <!-- #END# Browser Usage -->
</div>
</div>
</section>

@include('admin/layout/footer')

<script>

function changePermission(method,obj,role_id,r_id) {
    state = document.getElementById(obj.id).checked;
    if(state==true){
        value = 1;
    }
    else{
        value = 0;
    }

    $.ajax({
             type: "POST",
             url: "{{route('admin-settings-role-permission-management')}}",
             headers: {
               'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
             },
              data: {'method' : method, 'value' : value, 'role_id' : role_id, 'r_id' : r_id},
             success: function(result)
             {
                 if(result == 'true')
                 {
                    $('#msgDiv').show();
                    setTimeout(function(){
                        $('#msgDiv').fadeOut('slow');
                    },1000);
                 }
             }
            });
}
</script>
