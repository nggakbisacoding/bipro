<?php

namespace Modules\Auth\Http\Controllers\Backend\Role;

use Modules\Auth\Http\Requests\Backend\Role\DeleteRoleRequest;
use Modules\Auth\Http\Requests\Backend\Role\EditRoleRequest;
use Modules\Auth\Http\Requests\Backend\Role\StoreRoleRequest;
use Modules\Auth\Http\Requests\Backend\Role\UpdateRoleRequest;
use Modules\Auth\Entities\Role;
use Modules\Auth\Services\PermissionService;
use Modules\Auth\Services\RoleService;
use Modules\Auth\Transformers\RoleResource;

/**
 * Class RoleController.
 */
class RoleController
{
    protected RoleService $roleService;

    protected PermissionService $permissionService;

    /**
     * RoleController constructor.
     *
     * @param  RoleService  $roleService
     * @param  PermissionService  $permissionService
     */
    public function __construct(RoleService $roleService, PermissionService $permissionService)
    {
        $this->roleService = $roleService;
        $this->permissionService = $permissionService;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $roles = $this->roleService->getAll();
        return inertia('auth::backend.role.index', [
            'roles' => new RoleResource($roles)
        ]);
    }

    /**
     * @return mixed
     */
    public function create()
    {
        return inertia('auth::backend.role.create',[
            'categories' => $this->permissionService->getCategorizedPermissions(),
            'general' => $this->permissionService->getUncategorizedPermissions(),
        ]);
    }

    /**
     * @param  StoreRoleRequest  $request
     * @return mixed
     *
     * @throws \App\Exceptions\GeneralException
     * @throws \Throwable
     */
    public function store(StoreRoleRequest $request)
    {
        $this->roleService->store($request->validated());

        return redirect()
            ->route('admin.roles.index')
            ->withFlashSuccess(__('The role was successfully created.'));
    }

    /**
     * @param  Role  $role
     * @return mixed
     */
    public function edit(EditRoleRequest $request, Role $role)
    {
        return inertia('auth::backend.role.edit',[
            'categories' => $this->permissionService->getCategorizedPermissions(),
            'general' => $this->permissionService->getUncategorizedPermissions(),
            'role' => $role,
            'userPermissions' => $role->permissions->modelKeys(),
        ]);
    }

    /**
     * @param  UpdateRoleRequest  $request
     * @param  Role  $role
     * @return mixed
     *
     * @throws \App\Exceptions\GeneralException
     * @throws \Throwable
     */
    public function update(UpdateRoleRequest $request, Role $role)
    {
        $this->roleService->update($role, $request->validated());

        return redirect()
            ->route('admin.roles.index')
            ->withFlashSuccess(__('The role was successfully updated.'));
    }

    /**
     * @param  Role  $role
     * @return mixed
     *
     * @throws \Exception
     */
    public function destroy(DeleteRoleRequest $request, Role $role)
    {
        $this->roleService->destroy($role);
        return redirect()
            ->route('admin.roles.index')
            ->withFlashSuccess(__('The role was successfully deleted.'));
    }
}
