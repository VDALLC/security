<?php
namespace Vda\Security\Rbac;

interface IRbacService
{

    /**
     * @param ISubject $subject
     * @param Permission $permission
     * @param array $params
     * @return bool
     */
    public function checkPermission(ISubject $subject, Permission $permission, array $params = array());
}
