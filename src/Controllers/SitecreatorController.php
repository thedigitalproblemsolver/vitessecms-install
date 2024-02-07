<?php

declare(strict_types=1);

namespace VitesseCms\Install\Controllers;

use VitesseCms\Core\Utils\SystemUtil;
use VitesseCms\Install\AbstractCreatorController;

class SitecreatorController extends AbstractCreatorController
{
    public function indexAction(): void
    {
        $modules = SystemUtil::getModules($this->configuration);
        $menuItems = [];

        foreach ($modules as $modulePath) {
            $file = $modulePath.'/install.json';
            if (is_file($file)) {
                $content = file_get_contents($file);
                if (is_string($content)) {
                    $menuItems[] = json_decode($content, true);
                }
            }
        }

        $this->view->setVar(
            'content',
            $this->view->renderTemplate(
                'menu',
                $this->configuration->getVendorNameDir().'install/src/Resources/views/admin/',
                [
                    'menuItems' => $menuItems,
                ]
            )
        );

        $this->prepareView();
    }
}
