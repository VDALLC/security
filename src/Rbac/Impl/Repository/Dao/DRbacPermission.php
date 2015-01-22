<?php
namespace Vda\Security\Rbac\Impl\Repository\Dao;

use Vda\Query\Field;
use Vda\Query\Key\PrimaryKey;
use Vda\Query\Table;
use Vda\Util\Type;

abstract class DRbacPermission extends Table
{
    public $id;

    /**
     * Init like:
     * new ManyToMany(
     *  'Project\Dao\DRbacPermission',
     *  'Project\Dao\DRbacPermissionHierarchy'
     * );
     */
    public $_fkChildren;

    /**
     * Init like:
     * new ManyToMany(
     *  'Project\Dao\DRbacRole',
     *  'Project\Dao\DRbacRolesPermissions'
     * );
     */
    public $_fkRole;

    public $_primaryKey;
    public $_entityClass = 'Vda\Security\Rbac\Permission';

    public function __construct($name, $alias = 'permission')
    {
        $this->id = new Field(Type::STRING);

        $this->_primaryKey = new PrimaryKey('id');

        parent::__construct($name, $alias, true);
    }
}
