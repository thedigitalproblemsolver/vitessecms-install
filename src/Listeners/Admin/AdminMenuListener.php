<?php declare(strict_types=1);

namespace VitesseCms\Install\Listeners\Admin;

use VitesseCms\Admin\Models\AdminMenu;
use VitesseCms\Admin\Models\AdminMenuNavBarChildren;
use Phalcon\Events\Event;

class AdminMenuListener
{
    public function AddChildren(Event $event, AdminMenu $adminMenu): void
    {
        $children = new AdminMenuNavBarChildren();
        $children->addChild('Site Creator', 'admin/install/sitecreator/index');
        $adminMenu->addDropdown('System', $children);
    }
}
