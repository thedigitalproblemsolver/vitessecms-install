<?php declare(strict_types=1);

namespace VitesseCms\Install\Repositories;

use VitesseCms\Content\Repositories\ItemRepository;
use VitesseCms\Database\Interfaces\BaseRepositoriesInterface;
use VitesseCms\Datafield\Repositories\DatafieldRepository;
use VitesseCms\Core\Repositories\DatagroupRepository;
use VitesseCms\User\Repositories\PermissionRoleRepository;

class RepositoryCollection implements RepositoryCollectionInterface, BaseRepositoriesInterface
{
    /**
     * @var PermissionRoleRepository
     */
    public $permissionRole;

    public function __construct(
        PermissionRoleRepository $permissionRoleRepository
    ) {
        $this->permissionRole = $permissionRoleRepository;
    }
}
