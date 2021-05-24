<?php declare(strict_types=1);

namespace VitesseCms\Install\Listeners;

use VitesseCms\Content\Controllers\AdminitemController;
use Phalcon\Events\Manager;
use VitesseCms\Content\Listeners\Controllers\AdminItemControllerListener;

class InitiateAdminListeners
{
    public static function setListeners(Manager $eventsManager): void
    {
        $eventsManager->attach('adminMenu', new AdminMenuListener());
        $eventsManager->attach(AdminitemController::class, new AdminItemControllerListener());
    }
}
