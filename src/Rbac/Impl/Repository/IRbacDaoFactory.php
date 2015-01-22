<?php
namespace Vda\Security\Rbac\Impl\Repository;

use Vda\Security\Rbac\Impl\Repository\Dao\DRbacPermission;
use Vda\Security\Rbac\Impl\Repository\Dao\DRbacPermissionHierarchy;
use Vda\Security\Rbac\Impl\Repository\Dao\DRbacRole;
use Vda\Security\Rbac\Impl\Repository\Dao\DRbacRoleHierarchy;
use Vda\Security\Rbac\Impl\Repository\Dao\DRbacRolesPermissions;
use Vda\Security\Rbac\Impl\Repository\Dao\DRbacUserRoles;

interface IRbacDaoFactory
{
    /**
     * @param string $alias
     * @return DRbacPermission
     */
    public function DPermission($alias = 'permission');

    /**
     * @param string $alias
     * @return DRbacPermissionHierarchy
     */
    public function DPermissionHierarchy($alias = 'permissionHierarchy');

    /**
     * @param string $alias
     * @return DRbacRole
     */
    public function DRole($alias = 'role');

    /**
     * @param string $alias
     * @return DRbacRoleHierarchy
     */
    public function DRoleHierarchy($alias = 'roleHierarchy');

    /**
     * @param string $alias
     * @return DRbacRolesPermissions
     */
    public function DRolesPermissions($alias = 'rolesPermissions');

    /**
     * @param string $alias
     * @return DRbacUserRoles
     */
    public function DUserRoles($alias = 'userRoles');

    /**
     * @param string $alias
     * @return object [project]/Dao/DUser
     */
    public function DUser($alias = 'user');
}
