<?php

namespace Modules\Auth\Http\Controllers\Frontend\User;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Auth\Entities\User;
use Modules\Auth\Services\PermissionService;
use Modules\Auth\Services\RoleService;
use Modules\Auth\Http\Requests\Backend\User\StoreUserRequest;
use Modules\Auth\Http\Requests\Backend\User\UpdateUserRequest;
use Modules\Auth\Services\UserService;
use Modules\Auth\Transformers\UserResource;

class UserController extends Controller
{
    private UserService $userService;

    protected RoleService $roleService;

    protected PermissionService $permissionService;

    public function __construct(
        UserService $userService,
        RoleService $roleService,
        PermissionService $permissionService
    )
    {
        $this->userService = $userService;
        $this->roleService = $roleService;
        $this->permissionService = $permissionService;
    }

    /**
     * Display a listing of the resource.
     * @return \Inertia\Response
     */
    public function index()
    {
        $users = $this->userService->getAllUser();
        return inertia('users::backend.index', [
            'users' => new UserResource($users)
        ]);
    }

    /**
     * Show the form for creating a new resource.
     * @return \Inertia\Response
     */
    public function create()
    {
        return inertia('users::backend.create', [
            'roles' => $this->roleService->with('permissions')->get(),
            'categories' => $this->permissionService->getCategorizedPermissions(),
            'general' => $this->permissionService->getUncategorizedPermissions(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     * @param StoreUserRequest $request
     * @return \Inertia\Response
     */
    public function store(StoreUserRequest $request)
    {
        $user = $this->userService->store($request->validated());

        return redirect()
            ->route('admin.users.index', $user)
            ->withFlashSuccess(__('The user was successfully created.'));
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return \Inertia\Response
     */
    public function show($id)
    {
        return inertia('users::backend.show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param User $user
     * @return \Inertia\Response
     */
    public function edit(User $user)
    {
        return inertia('users::backend.edit',[
            'user' => $user,
            'roles' => $this->roleService->get(),
            'categories' => $this->permissionService->getCategorizedPermissions(),
            'general' => $this->permissionService->getUncategorizedPermissions(),
            'userPermissions' => $user->permissions->modelKeys(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param User $user
     * @return \Inertia\Response
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param User $user
     * @return \Inertia\Response
     */
    public function destroy(User $user)
    {
        $this->userService->delete($user);

        return redirect()
            ->route('admin.users.index', $user)
            ->withFlashSuccess(__('The user was successfully deleted.'));
    }
}
