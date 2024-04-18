<?php

namespace App\Repositories\ConcreteClasses;

use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Models\User;
use App\Http\Requests\RegisterRequest;
use Illuminate\Http\Request;
use Hash;

class UserRepository implements UserRepositoryInterface
{


    public function all()
    {
        $data = User::with('roles')->get()
            ->map->format();
        return $data;
    }

    public function paginator()
    {
        return User::paginate(10)
            ->getCollection()
            ->map->format();
    }

    public function findById($id)
    {
        $user = User::find($id);

        return $user ? $user : null;
    }


    public function findByEmail($email)
    {
        return User::where('email', $email)
            ->first();
    }

    public function searchRecords($value)
    {
        return User::SearchEmail($value)
            ->SearchName($value)
            ->SearchProfile($value)
            ->get();
    }


    public function verifyUser($id)
    {

        return User::where('id', $id)
            ->first()->update(['email_verified' => true]);
    }

    public function create(RegisterRequest $request)
    {
        return User::create($request->only(['name', 'email', 'password']));
    }

    public function update(Request $request, $user_id)
    {
        $user = User::find($user_id);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $userData = $request->except('password');
        $user->update($userData);

        return response()->json(['message' => 'User updated successfully', 'data' => $user], 200);
    }



    public function updateEmail(Request $request, $user_id)
    {
        return User::where('id', $user_id)
            ->update(
                $request->only(
                    [
                        'email',
                    ]
                )
            );
    }


    public function deleteById($id)
    {
        User::where('id', $id)->delete();
    }

    public function deleteByIdForce($id)
    {
        try {
            User::where('id', $id)->forceDelete();
        } catch (\Exception $e) {
            return $this->jsonResponse([], 500, $e->getMessage(), 'error');
        }
    }

    public function retrieveTrashedUsers()
    {
        return User::onlyTrashed()->get();
    }

    public function retrieveAllUsersWithTrashed()
    {
        return User::withTrashed()->get();
    }

    public function restoreAllDeletedUsers()
    {
        return User::withTrashed()->restore();
    }

    public function restoreSingleUser($id)
    {
        return User::where('id', $id)->restore();
    }


    /**
     * Assign permission to users
     */
    public function assignPermissionToUser($permission, $user)
    {
        return $user->givePermissionTo($permissions);
    }

    /**
     * Revoke permission from users
     */
    public function revokePermissionFromUser($permission, $user)
    {
        return $user->revokePermissionTo($permission);
    }

    /**
     * Assign role to users
     */

    public function assignRoleToUser($role, $user)
    {
        return $user->assignRole($role);
    }

    /**
     * Revoke role from users
     */
    public function replaceRoleFromUser($role, $user)
    {
        return $user->syncRoles($role);
    }


    /**
     * Checking if user has any of the role mentioned
     */

    public function HasRole($roles, $user)
    {
        return $user->hasRole($roles); // accept array , tuple or string
    }

    public function HasAnyRole($roles, $user)
    {
        return $user->hasAnyRole($roles); // accept array , tuple or string
    }

    public function HasPermission($permissions, $user)
    {
        return $user->hasAnyPermission($permissions); // if user has any any of the permission specified could be in the form of an array, tuple, or string
    }

    public function HasAllPermission($permissions, $user)
    {
        return $user->hasAllPermissions($permissions);
    }

    public function userCan($can, $user)
    {
        return $user->can($can);
    }

    public function getUserPermissionNames($user)
    {
        $permissionNames = $user->getPermissionNames();
        return $permissionNames;
    }

    public function getUserPermissionDirect($user)
    {
        $permissionDirect = $user->getDirectPermissions();
        return $permissionDirect;
    }

    public function getAllUserPermission($user)
    {
        $allUserPermission = $user->getAllPermissions();
        return $allUserPermission;
    }

    public function getAllUserPermissionViaRoles($user)
    {
        $allUserPermissionViaRole = $user->getPermissionsViaRoles();
        return $allUserPermissionViaRole;
    }

    public function UserRoles($user)
    {
        $roles = $user->getRoleNames();
        return $roles;
    }

    public function getUserWithSpecificRole(Request $request)
    {
        $users = User::role($request->name)->get(); // Returns only users with the role 'name'
        return $users;
    }

    public function getUsersWithCertainPermission(Request $request)
    {
        $users = User::permission($request->name)->get();
        return $users;
    }

    public function getUsersWithRoles()
    {
        $all_users_with_all_their_roles = User::with('roles')->get();
        return $all_users_with_all_their_roles;
    }


    public function getUserWithAllDirectPermissions()
    {
        $all_users_with_all_direct_permissions = User::with('permissions')->get();
        return $all_users_with_all_direct_permissions;
    }

    public function getUsersWithOutRoles()
    {
        $users_without_any_roles = User::doesntHave('roles')->get();
        return $users_without_any_roles;
    }

    public function suspendUser($id)
    {
        $user = User::findOrFail($id);
        $user->hasBeenSuspended = true;
        $user->save();
    }

    public function raiseUserSuspension($id)
    {
        $user = User::findOrFail($id);
        $user->hasBeenSuspended = false;
        $user->save();
}
}
