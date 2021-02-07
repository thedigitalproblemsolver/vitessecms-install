<?php declare(strict_types=1);

namespace VitesseCms\Install;

use VitesseCms\Admin\Utils\AdminUtil;
use VitesseCms\Content\Repositories\ItemRepository;
use VitesseCms\Core\AbstractModule;
use VitesseCms\Datafield\Repositories\DatafieldRepository;
use VitesseCms\Core\Repositories\DatagroupRepository;
use VitesseCms\Install\Repositories\AdminRepositoryCollection;
use Phalcon\DiInterface;
use VitesseCms\Install\Repositories\RepositoryCollection;
use VitesseCms\User\Repositories\PermissionRoleRepository;

class Module extends AbstractModule
{
    public function registerServices(DiInterface $di, string $string = null)
    {
        parent::registerServices($di, 'Install');

        if (AdminUtil::isAdminPage()) :
            $di->setShared('repositories', new AdminRepositoryCollection(
                new ItemRepository(),
                new DatagroupRepository(),
                new DatafieldRepository()
            ));
        else :
            $di->setShared('repositories', new RepositoryCollection(
                new PermissionRoleRepository()
            ));
        endif;
    }
}