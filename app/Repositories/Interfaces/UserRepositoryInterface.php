<?php

namespace App\Repositories\Interfaces;
use App\Http\Requests\RegisterRequest;
use Illuminate\Http\Request;
use App\Http\Requests\ProfileRequest;
use App\Models\User;
interface UserRepositoryInterface{

    public function all();
    public function paginator();
    public function findById($id);
    public function searchRecords($value);
    public function findByEmail($email);
    public function verifyUser($id);
    public function create(RegisterRequest $request);
    public function update(Request $request, $user_id);
    public function updateEmail(Request $request, $user_id);
    public function deleteById($id);
    public function deleteByIdForce($id);
    public function retrieveTrashedUsers();
    public function restoreAllDeletedUsers();
    public function restoreSingleUser($id);
    public function retrieveAllUsersWithTrashed();
    public function assignPermissionToUser($permission, $user);
    public function revokePermissionFromUser($permission, $user);
    public function assignRoleToUser($role, $user);
    public function replaceRoleFromUser($role, $user);
    public function HasRole($roles, $user);
    public function HasAnyRole($roles, $user);
    public function HasPermission($permissions, $user);
    public function HasAllPermission($permissions, $user);
    public function userCan($can, $user);
    public function getUserPermissionNames($user);
    public function getUserPermissionDirect($user);
    public function getAllUserPermission($user);
    public function getAllUserPermissionViaRoles($user);
    public function UserRoles($user);
    public function getUserWithSpecificRole(Request $request);
    public function getUsersWithCertainPermission(Request $request);
    public function getUsersWithRoles();
    public function getUserWithAllDirectPermissions();
    public function getUsersWithOutRoles();
    public function suspendUser($id);
    public function raiseUserSuspension($id);
}
