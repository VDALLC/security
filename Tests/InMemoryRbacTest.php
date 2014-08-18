<?php
use Vda\Security\Rbac\Impl\Memory\RbacService;
use Vda\Security\Rbac\IOwned;
use Vda\Security\Rbac\IRbacService;
use Vda\Security\Rbac\ISubject;
use Vda\Security\Rbac\OwnerPermission;
use Vda\Security\Rbac\Permission;
use Vda\Security\Rbac\Role;

class User implements ISubject
{
    private $userId;

    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    public function getId()
    {
        return $this->userId;
    }
}

class Post implements IOwned
{
    private $ownerId;

    public function __construct($ownerId)
    {
        $this->ownerId = $ownerId;
    }

    public function getOwnerId()
    {
        return $this->ownerId;
    }
}

class InMemoryRbacTestClass extends PHPUnit_Framework_TestCase
{
    /**
     * @var IRbacService
     */
    private $rbac;

    public function setUp()
    {
        $this->rbac = new RbacService(
            array(new Role('visitor'), new Role('member'), new Role('moderator')),
            array(new Permission('read'), new Permission('edit'), new OwnerPermission('editOwn')),
            array('moderator' => array('member'), 'member' => array('visitor')),
            array('edit' => array('editOwn')),
            array('visitor' => array('read'), 'member' => array('editOwn'), 'moderator' => array('edit')),
            array(1 => 'visitor', 2 => 'member', 3 => 'moderator')
        );
    }

    public function testVisitorCanRead()
    {
        $this->assertTrue($this->rbac->checkPermission(
            new User(1),
            $this->rbac->loadPermission('read'),
            array('object' => new Post(1))
        ));

        $this->assertTrue($this->rbac->checkPermission(
            new User(1),
            $this->rbac->loadPermission('read'),
            array('object' => new Post(2))
        ));
    }

    public function testMemberCanEditOwnPosts()
    {
        $this->assertTrue($this->rbac->checkPermission(
            new User(2),
            $this->rbac->loadPermission('edit'),
            array('object' => new Post(2))
        ));

        $this->assertFalse($this->rbac->checkPermission(
            new User(2),
            $this->rbac->loadPermission('edit'),
            array('object' => new Post(3))
        ));
    }

    public function testModeratorCanEditAll()
    {
        $this->assertTrue($this->rbac->checkPermission(
            new User(3),
            $this->rbac->loadPermission('edit'),
            array('object' => new Post(2))
        ));

        $this->assertTrue($this->rbac->checkPermission(
            new User(3),
            $this->rbac->loadPermission('edit'),
            array('object' => new Post(3))
        ));
    }
}
