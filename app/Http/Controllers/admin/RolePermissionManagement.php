<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use Session;
use App\User;
use App\Http\Controllers\Controller;
use App\Http\Controllers\admin\AdminBaseController;
use App\Http\Traits\DataSaver;
use App\Role;
use DB;
use App\Module;
use App\RolePermission;

/**
 *  This is a command to fetch all roll permission management details and save into database from admin panel.
 *
 *  @author	Sourav Chowdhury
 */
class RolePermissionManagement extends AdminBaseController
{
    use DataSaver;
    /**
     * this function is for list role management listing page
     */
    public function listRolePermissionManagement()
    {
        
        $this->setLeftSideBarData();
        $roles = Role::where('status', 'active')
               ->where('id', '!=' , 4) 
               ->get()
               ->toArray();

        $parent_module = array();
        $sub_module = array();
        $all_modules = array();

        $where = array('role_permissions.status' =>'active');
        $permissions = DB::table('role_permissions')
        ->leftJoin('roles', 'role_permissions.r_id', '=', 'roles.id')
        ->where($where)
        ->orderBy('role_permissions.role_id', 'asc')
        ->orderBy('roles.id', 'asc')
        ->get()
        ->toArray();

        $arr = array();
        $per = array();
        foreach($permissions as $permission){
            $arr['role_id'] = $permission->role_id;
            $arr['r_id'] = $permission->r_id;
            $arr['can_add'] = $permission->can_add;
            $arr['can_view'] = $permission->can_view;
            $arr['can_modify'] = $permission->can_modify;
            $per[$permission->r_id.'-'.$permission->role_id] = $arr;
        }

        return view('admin/settings/role_permission_management_list',['profile_image' => $this->profile_image, 'parent_menu' => $this->parent_menu, 'sub_menu' =>$this->sub_menu, 'roles' => $roles, 'permissions' =>$per]);
    }

    /**
     * this function is for insert/update role permissions
     * @param  Request $request [description]
     * @var string
     */
    public function postRolePermissionManagement(Request $request)
    {
        if(($request))
        {
            $method = $request->method;
            $value = $request->value;
            $role_id = $request->role_id;
            $r_id = $request->r_id;

            $role_permissions = RolePermission::where('role_id', $role_id)->where('r_id', $r_id)->get()->toArray();

            if($role_permissions){
                $id = $role_permissions[0]['id'];

                RolePermission::where('id', $id)
                ->update([$method => $value, 'updated_by' => 1, 'updated_at' => date("Y-m-d H:i:s")]);
            }
            else {
                $RolePermission = new RolePermission;
                $RolePermission->role_id = $role_id;
                $RolePermission->r_id = $r_id;
                $RolePermission->$method = $value;
                $RolePermission->status = 'active';
                $RolePermission->created_by = 1;
                $RolePermission->created_at = date("Y-m-d H:i:s");
                $RolePermission->save();
                
            }
            $this->log('Permission Updated From Admin');

            echo 'true';

        }
    }
}
