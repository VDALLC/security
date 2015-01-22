<?php
namespace Vda\Security\Rbac\Impl\Repository;

use Vda\Security\Rbac\ISubject;
use Vda\Security\Rbac\Permission;
use Vda\Security\Rbac\Impl\Repository;
use Vda\Security\Rbac\Role;

interface IRbacService extends \Vda\Security\Rbac\IRbacService
{
    /**
     * @param RoleFilter $filter
     * @return Role[]
     */
    public function findRoles(RoleFilter $filter);

    /**
     * @return string[]
     */
    public function listRoles();

    public function saveRole(Role $role);

    public function removeRole(Role $role);

    /**
     * @param PermissionFilter $filter
     * @return Permission[]
     */
    public function findPermissions(PermissionFilter $filter);

    /**
     * @return string[]
     */
    public function listPermissions();

    /**
     * @param ISubject $subject
     * @param Role[] $roles
     */
    public function setUserRoles(ISubject $subject, array $roles);

    /**
     * @param Role $role
     * @param Role[] $children
     */
    public function setRoleChildren(Role $role, array $children);

    /**
     * @param Role $role
     * @param Permission[] $permissions
     */
    public function setRolePermission(Role $role, array $permissions);
}
