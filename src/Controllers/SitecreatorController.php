<?php declare(strict_types=1);

namespace VitesseCms\Install\Controllers;

use VitesseCms\Install\AbstractCreatorController;

class SitecreatorController extends AbstractCreatorController
{
    public function indexAction(): void
    {
        $this->view->setVar('content', $this->view->renderTemplate(
            'menu',
            $this->configuration->getVendorNameDir().'install/src/Resources/views/admin/'
        ));

        $this->prepareView();
    }
}
