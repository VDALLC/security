<?php
namespace Vda\Security\Rbac\Impl\Repository;

use Vda\Query\Filter;
use Vda\Security\Rbac\Impl\Repository\Dao\DRbacPermission;
use Vda\Security\Rbac\Impl\Repository\Dao\DRbacRole;

class PermissionFilter extends Filter
{
    private $permission;

    private $role;
    private $isRoleInvolved = false;

    public function __construct(
        DRbacPermission $permission,
        DRbacRole $role
    ) {
        $this->permission = $permission;
        $this->role = $role;
    }

    public function permission()
    {
        return $this->permission;
    }

    public function isRoleInvolved()
    {
        return $this->isRoleInvolved;
    }

    public function role()
    {
        $this->isRoleInvolved = true;
        return $this->role;
    }
}
