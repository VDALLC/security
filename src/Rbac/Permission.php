<?php
namespace Vda\Security\Rbac;

class Permission
{
    private $id;
    private $children;

    public function __construct($id = null)
    {
        $this->id = $id;
    }

    /**
     * @param mixed $children
     */
    public function setChildren($children)
    {
        $this->children = $children;
    }

    /**
     * @return mixed
     */
    public function getChildren()
    {
        return $this->children;
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

    public function check(ISubject $subject, $params)
    {
        return true;
    }
}
