<?php
use Vda\Security\Rbac\IRbacService;
use Vda\Security\Rbac\ISubject;
use Vda\Security\Rbac\Permission;
use Vda\Security\Rbac\Role;

/**
 * Class RbacService
 *
 * @todo implement
 */
class RbacService implements IRbacService
{
    /**
     * @var Role[]
     */
    private $roles;

    /**
     * @var Permission[]
     */
    private $permissions;

    public function __construct($roles, $permissions)
    {
        $this->roles        = $roles;
        $this->permissions  = $permissions;
    }

    /**
     * Assumed there is no recursive roles.
     *
     * @param string $role
     * @return Role[]
     */
    private function flatRole($role)
    {
        $res = array();
        foreach ((array)$role as $each) {
            $res[] = $this->roles[$each];
            foreach ($this->roles[$each]->getChildren() as $child) {
                $res = array_merge($res, $this->flatRole($child));
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
        $res[] = $this->permissions[$perm];
        foreach ($this->permissions[$perm]->getChildren() as $child) {
            $res = array_merge($res, $this->flatPermission($child));
        }
        return $res;
    }

    public function checkPermission(ISubject $subject, Permission $permission, array $params = array())
    {
        $roles = $this->flatRole($subject->getRoles());
        $perms = $this->flatPermission($perm);

        foreach ($roles as $role) {
            foreach ($perms as $permission) {
                if ($this->roles[$role->getName()]->hasPermission($permission->getName())) {
                    if ($permission->check($subject, $object)) {
                        return true;
                    }
                }
            }
        }
        return false;
    }
}
