<?php
namespace Vda\Security\Rbac\Impl\Memory;

use Exception;
use Vda\Security\Rbac\IRbacService;
use Vda\Security\Rbac\ISubject;
use Vda\Security\Rbac\Permission;
use Vda\Security\Rbac\Role;

class RbacService implements IRbacService
{
    /**
     * @var Role[]
     */
    private $roles;

    /**
     * @var array (parentRoleId => array childRolesId)
     */
    private $rolesHierarchy;

    /**
     * @var Permission[]
     */
    private $permissions;

    /**
     * @var array (parentPermissionId => array childPermissionId)
     */
    private $permissionHierarchy;

    /**
     * @var array (userId => roleId)
     */
    private $userRoles;

    /**
     * @param Role[] $roles
     * @param Permission[] $permissions
     * @param array $rolesHierarchy roleId => array(childRolesId)
     * @param array $permissionsHierarchy permissionId => array(childPermissionId)
     * @param array $rolePermissions array(roleId => array(permissionId))
     * @param array $userRoles
     */
    public function __construct(
        array $roles,
        array $permissions,
        array $rolesHierarchy,
        array $permissionsHierarchy,
        array $rolePermissions,
        array $userRoles
    ) {
        foreach ($roles as $role) {
            $this->roles[$role->getId()] = $role;
        }
        foreach ($permissions as $permission) {
            $this->permissions[$permission->getId()] = $permission;
        }
        $this->rolesHierarchy       = $rolesHierarchy;
        $this->permissionHierarchy  = $permissionsHierarchy;
        $this->rolePermissions      = $rolePermissions;
        $this->userRoles            = $userRoles;
    }

    /**
     *
     * @throws Exception
     * @param Role|string $roleId
     * @return string|Role
     */
    public function loadRole($roleId)
    {
        if ($roleId instanceof Role) {
            $roleId = $roleId->getId();
        }
        if (is_string($roleId)) {
            foreach ($this->roles as $eachRole) {
                if ($eachRole->getId() == $roleId) {
                    return $eachRole;
                }
            }
            throw new Exception('Invalid role id #' . $roleId);
        } else {
            throw new Exception('Role must be Role or string, ' . get_class($roleId) . ' given');
        }
    }

    /**
     *
     * @throws Exception
     * @param Permission|string $permissionId
     * @return string|Permission
     */
    public function loadPermission($permissionId)
    {
        if ($permissionId instanceof Permission) {
            $permissionId = $permissionId->getId();
        }
        if (is_string($permissionId)) {
            if (isset($this->permissions[$permissionId])) {
                return $this->permissions[$permissionId];
            }
            throw new Exception('Invalid permission id #' . $permissionId);
        } else {
            throw new Exception('Permission must be Permission or string, '
                . get_class($permissionId) . ' given');
        }
    }

    /**
     * Assumed there is no recursive roles.
     *
     * @param string|array $role
     * @return Role[]
     */
    private function flatRole($role)
    {
        $res = array();
        $role = is_array($role) ? $role : array($role);
        foreach ($role as $each) {
            $each = $this->loadRole($each);
            $res[] = $each;
            if (isset($this->rolesHierarchy[$each->getId()])) {
                foreach ($this->rolesHierarchy[$each->getId()] as $child) {
                    $res = array_merge($res, $this->flatRole($child));
                }
            }
        }
        return $res;
    }

    /**
     * Assumed there is no recursive permission.
     *
     * @param string $perm
     * @return Permission[]
     */
    private function flatPermission($perm)
    {
        $res = array();
        $perm = is_array($perm) ? $perm : array($perm);
        foreach ($perm as $each) {
            $each = $this->loadPermission($each);
            $res[] = $each;
            if (isset($this->permissionHierarchy[$each->getId()])) {
                foreach ($this->permissionHierarchy[$each->getId()] as $child) {
                    $res = array_merge($res, $this->flatPermission($child));
                }
            }
        }
        return $res;
    }

    private function getUserRoles($userId)
    {
        if (isset($this->userRoles[$userId])) {
            return $this->userRoles[$userId];
        } elseif (isset($this->userRoles[0])) {
            return $this->userRoles[0];
        } else {
            return null;
        }
    }

    private function hasPermission($role, $perm)
    {
        $role = $this->loadRole($role);
        $perm = $this->loadPermission($perm);
        return in_array($perm->getId(), $this->rolePermissions[$role->getId()]);
    }

    public function checkPermission(ISubject $subject, Permission $permission, array $params = array())
    {
        $roles = $this->flatRole($this->getUserRoles($subject->getId()));
        $perms = $this->flatPermission($permission);

        foreach ($roles as $role) {
            foreach ($perms as $permission) {
                if ($this->hasPermission($role, $permission)) {
                    if ($permission->check($subject, $params)) {
                        return true;
                    }
                }
            }
        }
        return false;
    }
}
