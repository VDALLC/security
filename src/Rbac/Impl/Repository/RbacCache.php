<?php
namespace Vda\Security\Rbac\Impl\Repository;

use \InvalidArgumentException;
use Vda\Datasource\IRepository;
use Vda\Query\Select;
use Vda\Security\Rbac\Permission;
use Vda\Security\Rbac\Role;

class RbacCache
{
    private $repository;

    private $daoFactory;

    private $allRoles;
    private $allRoleHierarchy;
    private $allPermissions;
    private $allPermissionsHierarchy;
    private $allRolePermissions;

    private $isDataLoaded = false;

    public function __construct(
        IRepository $repository,
        IRbacDaoFactory $daoFactory
    ) {
        $this->repository = $repository;
        $this->daoFactory = $daoFactory;
    }

    /**
     * @param $roleId
     * @return Role
     * @throws InvalidArgumentException
     */
    private function getRole($roleId)
    {
        if (isset($this->allRoles[$roleId])) {
            return $this->allRoles[$roleId];
        } else {
            throw new InvalidArgumentException('No such role #' . $roleId);
        }
    }

    /**
     * @param $permissionId
     * @return Permission
     * @throws InvalidArgumentException
     */
    private function getPermission($permissionId)
    {
        if (isset($this->allPermissions[$permissionId])) {
            return $this->allPermissions[$permissionId];
        } else {
            throw new InvalidArgumentException('No such permission #' . $permissionId);
        }
    }

    private function cacheData()
    {
        $dRole          = $this->daoFactory->DRole();
        $dRoleHierarchy = $this->daoFactory->DRoleHierarchy();
        $dPermission    = $this->daoFactory->DPermission();
        $dPermissionHierarchy = $this->daoFactory->DPermissionHierarchy();
        $dRolesPermissions = $this->daoFactory->DRolesPermissions();

        if (!$this->isDataLoaded) {
            $this->allRoles = $this->repository->select(
                Select::select()
                ->from($dRole)
                ->map($dRole->_entityClass)
                ->indexBy(0)
            );

            $data = $this->repository->select(
                Select::select()->from($dRoleHierarchy)
            );
            foreach ($data as $row) {
                $this->allRoleHierarchy[$row['parentId']][] = $row['childId'];
            }

            $this->allPermissions = $this->repository->select(
                Select::select()
                ->from($dPermission)
                ->map($dPermission->_entityClass)
                ->indexBy(0)
            );

            $data = $this->repository->select(
                Select::select()->from($dPermissionHierarchy)
            );
            foreach ($data as $row) {
                $this->allPermissionsHierarchy[$row['parentId']][] = $row['childId'];
            }

            $data = $this->repository->select(
                Select::select()->from($dRolesPermissions)
            );
            foreach ($data as $row) {
                $this->allRolePermissions[$row['roleId']][] = $row['permissionId'];
            }

            $this->isDataLoaded = true;
        }
    }

    /**
     * @param Role|Role[] $roles
     * @return Role[]
     */
    public function expandRole($roles)
    {
        $roles = is_array($roles) ? $roles : array($roles);

        $this->cacheData();

        $res = array();
        /* @var $role Role */
        foreach ((array)$roles as $role) {
            $res[] = $role;
            if (isset($this->allRoleHierarchy[$role->getId()])) {
                foreach ($this->allRoleHierarchy[$role->getId()] as $childId) {
                    $res = array_merge($res, $this->expandRole($this->getRole($childId)));
                }
            }
        }

        return $res;
    }

    /**
     * @param Permission|Permission[] $permissions
     * @return Permission[]
     */
    public function expandPermission($permissions)
    {
        $permissions = is_array($permissions) ? $permissions : array($permissions);
        $this->cacheData();

        $res = array();
        /* @var $permission Permission */
        foreach ((array)$permissions as $permission) {
            $res[] = $permission;
            if (isset($this->allPermissionsHierarchy[$permission->getId()])) {
                foreach ($this->allPermissionsHierarchy[$permission->getId()] as $childId) {
                    $res = array_merge($res, $this->expandPermission($this->getPermission($childId)));
                }
            }
        }

        return $res;
    }

    /**
     * Check role permission.
     *
     * Check only own permission, do not count role/permission hierarchy.
     *
     * @param Role $role
     * @param Permission $permission
     * @return bool
     */
    public function hasPermission(Role $role, Permission $permission)
    {
        $this->cacheData();
        if (isset($this->allRolePermissions[$role->getId()])) {
            return in_array($permission->getId(), $this->allRolePermissions[$role->getId()]);
        } else {
            return false;
        }
    }
}
