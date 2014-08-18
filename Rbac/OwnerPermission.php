<?php
namespace Vda\Security\Rbac;

class OwnerPermission extends Permission
{
    public function check(ISubject $subject, $params)
    {
        if ($params['object'] instanceof IOwned) {
            return $subject->getId() == $params['object']->getOwnerId();
        } else {
            return false;
        }
    }
}
