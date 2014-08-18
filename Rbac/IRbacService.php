<?php
namespace Vda\Security\Rbac;

interface IRbacService
{
    /**
     * @param $permissionId
     * @return Permission
     */
    public function loadPermission($permissionId);

    /**
     * @param $roleId
     * @return Role
     */
    public function loadRole($roleId);

    /**
     * @param ISubject $subject
     * @param Permission $permission
     * @param array $params
     * @return bool
     */
    public function checkPermission(ISubject $subject, Permission $permission, array $params = array());
}
