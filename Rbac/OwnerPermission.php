<?php
namespace Vda\Security\Rbac;

class OwnerPermission extends Permission
{
    public function check(ISubject $subject, IOwned $object)
    {
        return $subject->getId() == $object->getOwnerId();
    }
}
