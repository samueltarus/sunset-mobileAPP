<?php

namespace App\Http\Controllers\API\Backend\Roles;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;


class RolesController extends Controller
{
    # construct
    public function __construct()
    {
        $this->middleware(['permission:roles_and_permissions'])->only('index');
        $this->middleware(['permission:add_roles_and_permissions'])->only(['create', 'store']);
        $this->middleware(['permission:edit_roles_and_permissions'])->only(['edit', 'update']);
        $this->middleware(['permission:delete_roles_and_permissions'])->only(['delete']);
    }

    # role list
    public function index(Request $request)
    {
        $searchKey = null;
        $roles = Role::oldest();
        if ($request->search != null) {
            $roles = $roles->where('name', 'like', '%' . $request->search . '%');
            $searchKey = $request->search;
        }

        $roles = $roles->paginate(paginationNumber());
        return response()->json([
            $roles,
            $searchKey,
        ],200);
    }

    # return view of create form
    public function create()
    {
        $permission_groups = Permission::all()->groupBy('group_name');
        return response()->json([
            $permission_groups,
        ],200);
    }

    # role store
    public function store(Request $request)
    {
        $role = Role::create(['name' => $request->name]);
        $role->givePermissionTo($request->permissions);
        flash(localize('New Role has been added successfully'))->success();
        return response()->json([
            'message'=>'New Role has been added successfully',
        ],200);
    }

    # edit role
    public function edit(Request $request, $id)
    {
        $role = Role::findOrFail($id);
        $permission_groups = Permission::all()->groupBy('group_name');
        return response()->json([
            $role,
            $permission_groups,
        ],200);
    }

    # update role
    public function update(Request $request)
    {
        $role = Role::findOrFail($request->id);
        $role->name = $request->name;
        $role->syncPermissions($request->permissions);
        $role->save();
        flash(localize('Role has been updated successfully'))->success();
        return response()->json([
            'message'=>'Role has been updated successfully'
        ],200);
    }

    # delete role
    public function delete($id)
    {
        Role::destroy($id);
        flash(localize(''))->success();
        return response()->json([
            'message'=>'Role has been deleted successfully'
        ],200);
    }
}
