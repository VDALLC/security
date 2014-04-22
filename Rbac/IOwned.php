<?php
namespace Vda\Security\Rbac;

/**
 * Interface IOwned
 *
 * for Memory/RbacService
 *
 * @package Vda\Security\Rbac
 */
interface IOwned
{
    public function getOwnerId();
}
