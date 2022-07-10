<?php declare(strict_types=1);

namespace VitesseCms\Install\Listeners;

use VitesseCms\Content\Controllers\AdminitemController;
use VitesseCms\Content\Listeners\Controllers\AdminItemControllerListener;
use VitesseCms\Content\Repositories\AdminRepositoryCollection;
use VitesseCms\Content\Repositories\ItemRepository;
use VitesseCms\Core\Interfaces\InitiateListenersInterface;
use VitesseCms\Core\Interfaces\InjectableInterface;
use VitesseCms\Datafield\Repositories\DatafieldRepository;
use VitesseCms\Datagroup\Repositories\DatagroupRepository;
use VitesseCms\Install\Listeners\Admin\AdminMenuListener;
use VitesseCms\Language\Repositories\LanguageRepository;

class InitiateAdminListeners implements InitiateListenersInterface
{
    public static function setListeners(InjectableInterface $di): void
    {
        $di->eventsManager->attach('adminMenu', new AdminMenuListener());
        $di->eventsManager->attach(AdminitemController::class, new AdminItemControllerListener(
            new AdminRepositoryCollection(
                new ItemRepository(),
                new DatagroupRepository(),
                new DatafieldRepository(),
                new LanguageRepository()
            )
        ));
    }
}
