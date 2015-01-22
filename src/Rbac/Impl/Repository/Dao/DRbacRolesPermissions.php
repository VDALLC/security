<?php
namespace Vda\Security\Rbac\Impl\Repository\Dao;

use Vda\Query\Field;
use Vda\Query\Key\PrimaryKey;
use Vda\Query\Table;
use Vda\Util\Type;

abstract class DRbacRolesPermissions extends Table
{
    public $roleId;
    public $permissionId;

    /**
     * new ManyToOne(
     *  'Project\Dao\DRbacRole',
     *  array('roleId' => 'id')
     * );
     */
    public $_fkRbacRole;

    /**
     * new ManyToOne(
     *  'Project\Dao\DRbacPermission',
     *  array('permissionId' => 'id')
     * );
     */
    public $_fkRbacPermission;

    public function __construct($table, $alias = 'RolesPermissions')
    {
        $this->roleId = new Field(Type::STRING);
        $this->permissionId = new Field(Type::STRING);

        $this->_primaryKey = new PrimaryKey('roleId', 'permissionId');

        parent::__construct($table, $alias, true);
    }
}

