<?php
namespace Vda\Security\Rbac\Impl\Repository\Dao;

use Vda\Query\Field;
use Vda\Query\Table;
use Vda\Util\Type;

abstract class DRbacPermissionHierarchy extends Table
{
    public $childId;
    public $parentId;

    /**
     * Init like:
     * new ManyToOne(
     *  'Project\Dao\DRbacPermission',
     *  array('childId' => 'id')
     * );
     */
    public $_fkRbacPermission;

    public function __construct($name, $alias = 'PermissionHierarchy')
    {
        $this->childId = new Field(Type::STRING);
        $this->parentId = new Field(Type::STRING);

        parent::__construct($name, $alias, true);
    }
}


