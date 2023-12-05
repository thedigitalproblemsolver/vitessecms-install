<?php
declare(strict_types=1);

namespace VitesseCms\Install;

use Phalcon\Di\DiInterface;
use VitesseCms\Admin\Utils\AdminUtil;
use VitesseCms\Content\Repositories\ItemRepository;
use VitesseCms\Core\AbstractModule;
use VitesseCms\Datafield\Repositories\DatafieldRepository;
use VitesseCms\Datagroup\Repositories\DatagroupRepository;
use VitesseCms\Install\Repositories\AdminRepositoryCollection;
use VitesseCms\Install\Repositories\RepositoryCollection;
use VitesseCms\Language\Models\Language;
use VitesseCms\Language\Repositories\LanguageRepository;
use VitesseCms\User\Repositories\PermissionRoleRepository;

class Module extends AbstractModule
{
    public function registerServices(DiInterface $di, string $string = null)
    {
        parent::registerServices($di, 'Install');

        if (AdminUtil::isAdminPage()) :
            $di->setShared(
                'repositories',
                new AdminRepositoryCollection(
                    new ItemRepository(),
                    new DatagroupRepository(),
                    new DatafieldRepository(),
                    new LanguageRepository(Language::class)
                )
            );
        else :
            $di->setShared(
                'repositories',
                new RepositoryCollection(
                    new PermissionRoleRepository()
                )
            );
        endif;
    }
}
