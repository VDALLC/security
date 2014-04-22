<?php
namespace Vda\Security\Rbac\Impl\Repository\Dao;

use Vda\Query\Field;
use Vda\Query\Key\PrimaryKey;
use Vda\Query\Table;
use Vda\Util\Type;

abstract class DRbacUserRoles extends Table
{
    public $userId;
    public $roleId;

    public $tableName;

    /**
     * new ManyToOne(
     *  'Project\Dao\DRbacRole',
     *  array('roleId' => 'id')
     * );
     */
    public $_fkRbacRole;

    /**
     * new ManyToOne(
     *  'Project\Dao\DUser',
     *  array('userId' => 'userId')
     * );
     */
    public $_fkUser;

    public function __construct($table, $alias = 'UserRoles')
    {
        $this->roleId = new Field(Type::STRING);
        $this->userId = new Field(Type::INTEGER);

        $this->_primaryKey = new PrimaryKey('roleId', 'userId');

        parent::__construct($table, $alias, true);
    }
}
