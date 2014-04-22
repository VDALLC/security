<?php
namespace Vda\Security\Rbac;

class Role
{
    private $id;

    public function __construct($id = null)
    {
        $this->id = $id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }
}
