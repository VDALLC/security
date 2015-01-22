<?php
namespace Vda\Security\Rbac\Impl\Repository;

use Vda\Datasource\IRepository;
use Vda\Query\Delete;
use Vda\Query\Insert;
use Vda\Query\Operator\Operator;
use Vda\Query\Select;
use Vda\Security\Rbac\ISubject;
use Vda\Security\Rbac\Permission;
use Vda\Security\Rbac\Impl\Repository;
use Vda\Security\Rbac\Role;

class RbacService implements IRbacService
{
    /**
     * @var IRepository
     */
    private $repository;

    private $cache;

    private $daoFactory;

    public function __construct(
        IRepository $repository,
        RbacCache $cache,
        IRbacDaoFactory $daoFactory
    ) {
        $this->repository = $repository;
        $this->cache = $cache;

        $this->daoFactory = $daoFactory;
    }

    public function loadRole($roleId)
    {
        $dRole = $this->daoFactory->DRole();

        return $this->repository->select(
            Select::select()
                ->from($dRole)
                ->where($dRole->id->eq($roleId))
                ->map($dRole->_entityClass)
                ->singleRow()
        );
    }

    public function findRoles(RoleFilter $filter)
    {
        $select = Select::select($filter->role())->from($filter->role());

        if ($filter->isUserInvolved()) {
            $select->join($filter->user(), $filter->role()->_fkUser);
        }

        if ($filter->isParentInvolved()) {
            $toParent = $this->daoFactory->DRoleHierarchy('toParent');
            $select->join($toParent, $filter->role()->id->eq($toParent->childId));
            $select->join($filter->parent(), $toParent->parentId->eq($filter->parent()->id));
        }

        if ($filter->isChildInvolved()) {
            $toChild = $this->daoFactory->DRoleHierarchy('toChild');
            $select->join($toChild, $filter->role()->id->eq($toChild->parentId));
            $select->join($filter->child(), $toChild->childId->eq($filter->child()->id));
        }

        return $this->repository->select(
            $select
            ->filter($filter)
            ->map($filter->role()->_entityClass)
        );
    }

    public function saveRole(Role $role)
    {
        $dRole = $this->daoFactory->DRole();

        if ($this->loadRole($role->getId())) {
            // nothing to do, there is one field already in database
        } else {
            $this->repository->insert(
                Insert::insert()
                ->into($dRole)
                ->populate($role)
            );
        }
    }

    public function removeRole(Role $role)
    {
        $dRoleHierarchy     = $this->daoFactory->DRoleHierarchy();
        $dRolesPermissions  = $this->daoFactory->DRolesPermissions();
        $dUserRoles         = $this->daoFactory->DUserRoles();
        $dRole              = $this->daoFactory->DRole();

        $this->repository->delete(
            Delete::delete()
            ->from($dRoleHierarchy)
            ->where(Operator::orOp(
                $dRoleHierarchy->childId->eq($role->getId()),
                $dRoleHierarchy->parentId->eq($role->getId())
            ))
        );

        $this->repository->delete(
            Delete::delete()
            ->from($dRolesPermissions)
            ->where($dRolesPermissions->roleId->eq($role->getId()))
        );

        $this->repository->delete(
            Delete::delete()
                ->from($dUserRoles)
                ->where($dUserRoles->roleId->eq($role->getId()))
        );

        $this->repository->delete(
            Delete::delete()
            ->from($dRole)
            ->where($dRole->id->eq($role->getId()))
        );
    }

    public function findPermissions(PermissionFilter $filter)
    {
        $dPermission = $this->daoFactory->DPermission();

        $select = Select::select($filter->permission())->from($filter->permission());

        if ($filter->isRoleInvolved()) {
            $select->join($filter->role(), $filter->permission()->_fkRole);
        }

        $select->filter($filter)->map($dPermission->_entityClass);
        return $this->repository->select($select);
    }

    public function listRoles()
    {
        $dRole = $this->daoFactory->DRole();

        return $this->repository->select(
            Select::select($dRole->id)
            ->from($dRole)
            ->singleColumn()
        );
    }

    public function listPermissions()
    {
        $dPermission = $this->daoFactory->DPermission();

        return $this->repository->select(
            Select::select($dPermission->id)
                ->from($dPermission)
                ->singleColumn()
        );
    }

    /**
     * @param ISubject $subject
     * @param Role[] $roles
     */
    public function setUserRoles(ISubject $subject, array $roles)
    {
        $dUserRoles = $this->daoFactory->DUserRoles();

        $this->repository->delete(
            Delete::delete()
            ->from($dUserRoles)
            ->where($dUserRoles->userId->eq($subject->getId()))
        );

        foreach ($roles as $role) {
            $this->repository->insert(
                Insert::insert()
                ->into($dUserRoles)
                ->set($dUserRoles->userId, $subject->getId())
                ->set($dUserRoles->roleId, $role->getId())
            );
        }
    }

    /**
     * @param Role $role
     * @param Role[] $children
     */
    public function setRoleChildren(Role $role, array $children)
    {
        $dRoleHierarchy = $this->daoFactory->DRoleHierarchy();

        $this->repository->delete(
            Delete::delete()
            ->from($dRoleHierarchy)
            ->where($dRoleHierarchy->parentId->eq($role->getId()))
        );

        foreach ($children as $child) {
            $this->repository->insert(
                Insert::insert()
                ->into($dRoleHierarchy)
                ->set($dRoleHierarchy->parentId, $role->getId())
                ->set($dRoleHierarchy->childId, $child->getId())
            );
        }
    }

    /**
     * @param Role $role
     * @param Permission[] $permissions
     */
    public function setRolePermission(Role $role, array $permissions)
    {
        $dRolesPermissions = $this->daoFactory->DRolesPermissions();

        $this->repository->delete(
            Delete::delete()
            ->from($dRolesPermissions)
            ->where($dRolesPermissions->roleId->eq($role->getId()))
        );

        foreach ($permissions as $permission) {
            $this->repository->insert(
                Insert::insert()
                ->into($dRolesPermissions)
                ->set($dRolesPermissions->roleId, $role->getId())
                ->set($dRolesPermissions->permissionId, $permission->getId())
            );
        }
    }

    public function loadPermission($permissionId)
    {
        $dPermission = $this->daoFactory->DPermission();

        return $this->repository->select(
            Select::select()
            ->from($dPermission)
            ->where($dPermission->id->eq($permissionId))
            ->map($dPermission->_entityClass)
            ->singleRow()
        );
    }

    public function checkPermission(ISubject $subject, Permission $permission, array $params = array())
    {
        $roleFilter = new RoleFilter(
            $this->daoFactory->DRole(),
            $this->daoFactory->DRole('parent'),
            $this->daoFactory->DRole('child'),
            $this->daoFactory->DUser()
        );
        $roleFilter->where($roleFilter->user()->userId->eq($subject->getId()));
        $roles = $this->cache->expandRole($this->findRoles($roleFilter));

        $permissions = $this->cache->expandPermission($permission);

        foreach ($roles as $eachRole) {
            foreach ($permissions as $eachPermission) {
                if ($this->cache->hasPermission($eachRole, $eachPermission)) {
                    if ($eachPermission->check($subject, $params)) {
                        return true;
                    }
                }
            }
        }

        return false;
    }
}
