<?php
declare(strict_types=1);

namespace VitesseCms\Install\Listeners;

use VitesseCms\Core\Interfaces\InitiateListenersInterface;
use VitesseCms\Core\Interfaces\InjectableInterface;
use VitesseCms\Install\Listeners\Admin\AdminMenuListener;

class InitiateListeners implements InitiateListenersInterface
{
    public static function setListeners(InjectableInterface $injectable): void
    {
        if ($injectable->user->hasAdminAccess()) :
            $injectable->eventsManager->attach('adminMenu', new AdminMenuListener());
        endif;
    }
}
