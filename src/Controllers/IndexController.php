<?php declare(strict_types=1);

namespace VitesseCms\Install\Controllers;

use VitesseCms\Core\Factories\ObjectFactory;
use VitesseCms\Database\Models\FindValue;
use VitesseCms\Database\Models\FindValueIterator;
use VitesseCms\Install\AbstractCreatorController;
use VitesseCms\Install\Forms\newPropertyForm;
use VitesseCms\Install\Repositories\RepositoriesInterface;
use VitesseCms\Language\Factories\LanguageFactory;
use VitesseCms\User\Enums\UserRoleEnum;
use VitesseCms\User\Factories\UserFactory;

class IndexController extends AbstractCreatorController implements RepositoriesInterface
{
    public function indexAction(): void
    {
        $_REQUEST['embedded'] = 1;

        $form = new newPropertyForm();
        $this->view->setVar('content', $form->renderForm(
            $this->url->getBaseUri() . 'install/index/createproperty/'
        ));
        $this->prepareView();
    }

    public function createPropertyAction(): void
    {
        $form = new newPropertyForm();
        $form->bind($this->request->getPost(), new ObjectFactory());
        if ($form->validate($this)) :
            $post = $this->request->getPost();
            $language = LanguageFactory::create(
                'Default language',
                'Default language',
                $this->configuration->getLanguageLocale(),
                $this->configuration->getLanguageShort(),
                $this->url->getBaseUri(),
                'flag-icon flag-icon-' . $this->configuration->getLanguageShort(),
                true
            )->save();

            $this->createBasicPermissionRoles();

            UserFactory::create(
                $post['email'],
                $post['password'],
                (string) $this->repositories->permissionRole->findFirst(new FindValueIterator(
                    [new FindValue('calling_name', UserRoleEnum::SUPER_ADMIN)]
                ))->getId(),
                true
            )->save();

            $this->response->redirect($this->url->getBaseUri() . 'user/loginform');
        else:
            $this->response->redirect($this->url->getBaseUri());
        endif;

        $this->disableView();
    }
}
