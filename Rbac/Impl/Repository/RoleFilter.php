<?php
namespace Vda\Security\Rbac\Impl\Repository;

use Vda\Query\Filter;
use Vda\Security\Rbac\Impl\Repository\Dao\DRbacRole;

class RoleFilter extends Filter
{
    protected $dRole;
    protected $dRoleParent;
    protected $dRoleChild;
    protected $dUser;
    protected $isUserInvolved = false;
    protected $isParentInvolved = false;
    protected $isChildInvolved = false;

    public function __construct(DRbacRole $dRole, DRbacRole $parent, DRbacRole $child, $dUser)
    {
        $this->dRole = $dRole;
        $this->dRoleParent = $parent;
        $this->dRoleChild = $child;
        $this->dUser = $dUser;
    }

    public function role()
    {
        return $this->dRole;
    }

    public function user()
    {
        $this->isUserInvolved = true;
        return $this->dUser;
    }

    /**
     * @return DRbacRole
     */
    public function child()
    {
        $this->isChildInvolved = true;
        return $this->dRoleChild;
    }

    /**
     * @return DRbacRole
     */
    public function parent()
    {
        $this->isParentInvolved = true;
        return $this->dRoleParent;
    }

    public function isUserInvolved()
    {
        return $this->isUserInvolved;
    }

    public function isParentInvolved()
    {
        return $this->isParentInvolved;
    }

    public function isChildInvolved()
    {
        return $this->isChildInvolved;
    }
}
