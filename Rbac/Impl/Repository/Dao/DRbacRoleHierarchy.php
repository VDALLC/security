<?php
namespace Vda\Security\Rbac\Impl\Repository\Dao;

use Vda\Query\Field;
use Vda\Query\Table;
use Vda\Util\Type;

abstract class DRbacRoleHierarchy extends Table
{
    public $childId;
    public $parentId;

    /**
     * new ManyToOne(
     *  'Project\Dao\DRbacRole',
     *  array('childId' => 'id')
     * );
     */
    public $_fkRbacRole;

    public function __construct($table, $alias = 'RoleHierarchy')
    {
        $this->childId = new Field(Type::STRING);
        $this->parentId = new Field(Type::STRING);

        parent::__construct($table, $alias, true);
    }
}
