<?php
namespace Vda\Security\Rbac\Impl\Repository\Dao;

use Vda\Query\Field;
use Vda\Query\Key\PrimaryKey;
use Vda\Query\Table;
use Vda\Util\Type;

abstract class DRbacRole extends Table
{
    public $id;

    /**
     * new OneToMany(
     *  'Project\Dao\DRbacRoleHierarchy',
     *  array('id' => 'childId')
     * );
     */
    public $_fkRbacRoleHierarchy;

    /**
     * new OneToMany(
     *  'Project\Dao\DRbacUserRoles',
     *  array('id' => 'roleId')
     * );
     */
    public $_fkRbacUserRoles;

    /**
     * new OneToMany(
     *  'Project\Dao\DRbacRolesPermissions',
     *  array('id' => 'roleId')
     * );
     */
    public $_fkRbacRolesPermissions;

    /**
     * new ManyToMany(
     *  'Project\Dao\DUser',
     *  'Project\Dao\DRbacUserRoles'
     * );
     */
    public $_fkUser;

    /**
     * new ManyToMany(
     *  'Project\Dao\DRbacRole',
     *  'Project\Dao\DRbacRoleHierarchy'
     * );
     */
    public $_fkParent;

    /**
     * new ManyToMany(
     *  'Project\Dao\DRbacRole',
     *  'Project\Dao\DRbacRoleHierarchy'
     * );
     */
    public $_fkChild;

    public $_primaryKey;

    public $_entityClass = 'Vda\Security\Rbac\Role';

    public function __construct($table, $alias = 'role')
    {
        $this->id = new Field(Type::STRING);

        $this->_primaryKey = new PrimaryKey('id');

        parent::__construct($table, $alias, true);
    }
}
