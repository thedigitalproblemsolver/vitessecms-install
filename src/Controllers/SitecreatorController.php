<?php declare(strict_types=1);

namespace VitesseCms\Install\Controllers;

use VitesseCms\Core\Utils\SystemUtil;
use VitesseCms\Install\AbstractCreatorController;
use json_decode;

class SitecreatorController extends AbstractCreatorController
{
    public function indexAction(): void
    {
        $modules = SystemUtil::getModules($this->configuration);
        $menuItems = [];
        foreach ($modules as $modulePath):
            $file = $modulePath.'/install.json';
            if(is_file($file)):
                $menuItems[] = json_decode(file_get_contents($file), true);
            endif;
        endforeach;

        $this->view->setVar('content', $this->view->renderTemplate(
            'menu',
            $this->configuration->getVendorNameDir() . 'install/src/Resources/views/admin/',
            [
                'menuItems' => $menuItems
            ]
        ));

        $this->prepareView();
    }
}
