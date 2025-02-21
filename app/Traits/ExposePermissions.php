<?php

namespace App\Traits;

use Auth;
use Spatie\Permission\Models\Permission;
/**
 * Class ExposePermissions.
 *
 * @package Acacha\Users\Traits
 */
trait ExposePermissions
{
    /**
     * Get all user permissions in a flat array.
     *
     * @return array
     */
    public function getCanAttribute()
    {
        $permissions = [];
        foreach (Permission::all() as $permission) {
            if (auth()->guard('api')->check() && auth()->guard('api')->user()->can($permission->name)) {
                $permissions[$permission->name] = true;
            } else {
                $permissions[$permission->name] = false;
            }
        }
        return $permissions;
    }
    /**
     * Get all user permissions.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAllPermissionsAttribute()
    {
        return $this->getAllPermissions();
    }


}
